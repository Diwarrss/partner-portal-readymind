<?php

namespace App\Services\PartnerCenter;

use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PartnerCenterClient
{
    protected function defaultHeaders(array $extra = []): array
    {
        return array_merge([
            'MS-RequestId' => (string) Str::uuid(),
            'MS-CorrelationId' => (string) Str::uuid(),
            'Accept' => 'application/json',
        ], $extra);
    }

    public function getAccessToken(Tenant $tenant): ?string
    {
        $clientId = $tenant->partner_center_client_id;
        $clientSecret = $tenant->partner_center_client_secret;
        $tenantId = $tenant->partner_center_tenant_id;

        if (! $clientId || ! $clientSecret || ! $tenantId) {
            Log::warning('Partner Center credentials missing for tenant', ['tenant_id' => $tenant->id]);

            return null;
        }

        $authUrl = config('partner_center.auth_url').'/'.$tenantId.'/oauth2/token';
        $resource = 'https://api.partnercenter.microsoft.com';

        $response = Http::asForm()->post($authUrl, [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'resource' => $resource,
        ]);

        if (! $response->successful()) {
            Log::error('Partner Center auth failed', [
                'tenant_id' => $tenant->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $data = $response->json();

        return $data['access_token'] ?? null;
    }

    public function getCustomerSubscriptions(string $customerTenantId, Tenant $regionTenant): array
    {
        $token = $this->getAccessToken($regionTenant);
        if (! $token) {
            return [];
        }

        $baseUrl = config('partner_center.base_url');
        $url = "{$baseUrl}/v1/customers/{$customerTenantId}/subscriptions";

        $response = Http::withToken($token)
            ->withHeaders($this->defaultHeaders())
            ->get($url);

        if (! $response->successful()) {
            Log::error('Partner Center get subscriptions failed', [
                'customer_tenant_id' => $customerTenantId,
                'status' => $response->status(),
            ]);

            return [];
        }

        $data = $response->json();

        return $data['items'] ?? [];
    }

    public function getSubscriptionById(string $customerTenantId, string $subscriptionId, Tenant $regionTenant): ?array
    {
        $token = $this->getAccessToken($regionTenant);
        if (! $token) {
            return null;
        }

        $baseUrl = config('partner_center.base_url');
        $url = "{$baseUrl}/v1/customers/{$customerTenantId}/subscriptions/{$subscriptionId}";

        $response = Http::withToken($token)
            ->withHeaders($this->defaultHeaders())
            ->get($url);

        if (! $response->successful()) {
            Log::error('Partner Center get subscription failed', [
                'customer_tenant_id' => $customerTenantId,
                'subscription_id' => $subscriptionId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $etag = $response->header('ETag') ?? $response->header('etag');
        $subscription = $response->json();

        return [
            'subscription' => $subscription,
            'etag' => $etag,
        ];
    }

    public function updateSubscriptionQuantity(
        string $customerTenantId,
        string $subscriptionId,
        int $quantity,
        Tenant $regionTenant
    ): bool {
        $subscriptionResponse = $this->getSubscriptionById($customerTenantId, $subscriptionId, $regionTenant);
        if (! $subscriptionResponse) {
            return false;
        }

        $token = $this->getAccessToken($regionTenant);
        if (! $token) {
            return false;
        }

        $subscription = $subscriptionResponse['subscription'] ?? [];
        $etag = $subscriptionResponse['etag'] ?? ($subscription['attributes']['etag'] ?? null);

        $subscription['quantity'] = $quantity;
        $subscription['Quantity'] = $quantity;

        $baseUrl = config('partner_center.base_url');
        $url = "{$baseUrl}/v1/customers/{$customerTenantId}/subscriptions/{$subscriptionId}";

        $headers = $this->defaultHeaders();
        if ($etag) {
            $headers['If-Match'] = $etag;
        }

        $response = Http::withToken($token)
            ->withHeaders($headers)
            ->patch($url, $subscription);

        if (! $response->successful()) {
            Log::error('Partner Center update quantity failed', [
                'customer_tenant_id' => $customerTenantId,
                'subscription_id' => $subscriptionId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }

    public function decreaseSubscriptionQuantity(
        string $customerTenantId,
        string $subscriptionId,
        int $quantity,
        Tenant $regionTenant
    ): bool {
        return $this->updateSubscriptionQuantity($customerTenantId, $subscriptionId, $quantity, $regionTenant);
    }

    public function cancelSubscription(
        string $customerTenantId,
        string $subscriptionId,
        Tenant $regionTenant
    ): bool {
        $subscriptionResponse = $this->getSubscriptionById($customerTenantId, $subscriptionId, $regionTenant);
        if (! $subscriptionResponse) {
            return false;
        }

        $token = $this->getAccessToken($regionTenant);
        if (! $token) {
            return false;
        }

        $subscription = $subscriptionResponse['subscription'] ?? [];
        $etag = $subscriptionResponse['etag'] ?? ($subscription['attributes']['etag'] ?? null);
        $subscription['status'] = 'deleted';
        $subscription['Status'] = 'deleted';

        $baseUrl = config('partner_center.base_url');
        $url = "{$baseUrl}/v1/customers/{$customerTenantId}/subscriptions/{$subscriptionId}";

        $headers = $this->defaultHeaders();
        if ($etag) {
            $headers['If-Match'] = $etag;
        }

        $response = Http::withToken($token)
            ->withHeaders($headers)
            ->patch($url, $subscription);

        if (! $response->successful()) {
            Log::error('Partner Center cancel subscription failed', [
                'customer_tenant_id' => $customerTenantId,
                'subscription_id' => $subscriptionId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }
}
