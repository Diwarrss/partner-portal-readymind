<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LicenseSubscription;
use App\Services\LicenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        protected LicenseService $licenseService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();
        $this->licenseService->syncLicensesForCustomer($customer);

        $subscriptions = LicenseSubscription::query()
            ->where('customer_id', $customer->id)
            ->orderBy('offer_name')
            ->get();

        return response()->json(['data' => $subscriptions]);
    }

    public function increment(Request $request, LicenseSubscription $subscription): JsonResponse
    {
        $customer = $request->user();
        if ($subscription->customer_id !== $customer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'delta' => ['required', 'integer', 'min:1'],
        ]);

        $ok = $this->licenseService->incrementQuantity($customer, $subscription->partner_subscription_id, $validated['delta']);
        if (! $ok) {
            return response()->json(['message' => 'Unable to increase quantity.'], 422);
        }

        return response()->json(['message' => 'Quantity updated successfully.']);
    }

    public function decrement(Request $request, LicenseSubscription $subscription): JsonResponse
    {
        $customer = $request->user();
        if ($subscription->customer_id !== $customer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'delta' => ['required', 'integer', 'min:1'],
        ]);

        $result = $this->licenseService->decreaseQuantity($customer, $subscription->partner_subscription_id, $validated['delta']);
        if (! ($result['ok'] ?? false)) {
            $message = ($result['reason'] ?? null) === 'gap_expired'
                ? 'Grace period expired, quantity cannot be reduced.'
                : 'Unable to reduce quantity.';

            return response()->json(['message' => $message], 422);
        }

        return response()->json(['message' => 'Quantity reduced successfully.']);
    }
}
