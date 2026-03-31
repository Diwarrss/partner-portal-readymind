<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LicenseSubscription;
use App\Services\PartnerCenter\PartnerCenterClient;

class LicenseService
{
    public function __construct(
        protected PartnerCenterClient $partnerCenterClient
    ) {}

    public function syncLicensesForCustomer(Customer $customer): void
    {
        $microsoftTenantId = $customer->microsoft_tenant_id;
        if (! $microsoftTenantId) {
            return;
        }

        $tenant = $customer->tenant;
        $subscriptions = $this->partnerCenterClient->getCustomerSubscriptions($microsoftTenantId, $tenant);

        foreach ($subscriptions as $sub) {
            $subId = $sub['id'] ?? $sub['Id'] ?? null;
            if (! $subId) {
                continue;
            }

            $cancellationAllowedUntil = $sub['cancellationAllowedUntilDate']
                ?? $sub['CancellationAllowedUntilDate']
                ?? null;
            $creationDate = $sub['creationDate'] ?? $sub['CreationDate'] ?? null;
            $refundableQuantity = $sub['refundableQuantity'] ?? $sub['RefundableQuantity'] ?? null;
            $etag = $sub['attributes']['etag'] ?? $sub['Attributes']['Etag'] ?? null;

            LicenseSubscription::updateOrCreate(
                [
                    'customer_id' => $customer->id,
                    'partner_subscription_id' => $subId,
                ],
                [
                    'offer_id' => $sub['offerId'] ?? $sub['OfferId'] ?? '',
                    'offer_name' => $sub['offerName'] ?? $sub['OfferName'] ?? 'Unknown',
                    'quantity' => $sub['quantity'] ?? $sub['Quantity'] ?? 0,
                    'status' => $sub['status'] ?? $sub['Status'] ?? 'Unknown',
                    'cancellation_allowed_until_date' => $cancellationAllowedUntil,
                    'creation_date' => $creationDate,
                    'refundable_quantity' => $refundableQuantity,
                    'etag' => $etag,
                    'raw_data' => $sub,
                    'synced_at' => now(),
                ]
            );
        }
    }

    public function incrementQuantity(Customer $customer, string $subscriptionId, int $delta): bool
    {
        $subscription = LicenseSubscription::where('customer_id', $customer->id)
            ->where('partner_subscription_id', $subscriptionId)
            ->first();

        if (! $subscription || ! $customer->microsoft_tenant_id) {
            return false;
        }

        $newQuantity = max(0, $subscription->quantity + $delta);
        $success = $this->partnerCenterClient->updateSubscriptionQuantity(
            $customer->microsoft_tenant_id,
            $subscriptionId,
            $newQuantity,
            $customer->tenant
        );

        if ($success) {
            $subscription->update([
                'quantity' => $newQuantity,
                'synced_at' => now(),
            ]);
        }

        return $success;
    }

    public function decreaseQuantity(Customer $customer, string $subscriptionId, int $delta): array
    {
        $subscription = LicenseSubscription::where('customer_id', $customer->id)
            ->where('partner_subscription_id', $subscriptionId)
            ->first();

        if (! $subscription || ! $customer->microsoft_tenant_id) {
            return ['ok' => false, 'reason' => 'not_found'];
        }

        if (
            ! $subscription->cancellation_allowed_until_date
            || now()->greaterThan($subscription->cancellation_allowed_until_date)
        ) {
            return ['ok' => false, 'reason' => 'gap_expired'];
        }

        $newQuantity = max(0, $subscription->quantity - $delta);
        $success = $this->partnerCenterClient->decreaseSubscriptionQuantity(
            $customer->microsoft_tenant_id,
            $subscriptionId,
            $newQuantity,
            $customer->tenant
        );

        if (! $success) {
            return ['ok' => false, 'reason' => 'partner_center_error'];
        }

        $subscription->update([
            'quantity' => $newQuantity,
            'synced_at' => now(),
        ]);

        return ['ok' => true, 'reason' => null];
    }
}
