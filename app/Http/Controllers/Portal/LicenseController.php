<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\LicenseSubscription;
use App\Services\LicenseService;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function __construct(
        protected LicenseService $licenseService
    ) {}

    public function index(Request $request)
    {
        $customer = $request->user('customer');
        $this->licenseService->syncLicensesForCustomer($customer);

        return redirect()->route('portal.dashboard');
    }

    public function increment(Request $request, LicenseSubscription $subscription)
    {
        $customer = $request->user('customer');

        if ($subscription->customer_id !== $customer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'delta' => ['required', 'integer', 'min:1'],
        ]);

        $success = $this->licenseService->incrementQuantity(
            $customer,
            $subscription->partner_subscription_id,
            $validated['delta']
        );

        if (! $success) {
            return back()->with('error', 'No se pudo actualizar la cantidad.');

        }

        return back()->with('success', 'Licencias actualizadas correctamente.');
    }

    public function decrement(Request $request, LicenseSubscription $subscription)
    {
        $customer = $request->user('customer');

        if ($subscription->customer_id !== $customer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'delta' => ['required', 'integer', 'min:1'],
        ]);

        $result = $this->licenseService->decreaseQuantity(
            $customer,
            $subscription->partner_subscription_id,
            $validated['delta']
        );

        if (! ($result['ok'] ?? false)) {
            $message = match ($result['reason'] ?? null) {
                'gap_expired' => 'No es posible reducir en este momento; la ventana de devolución ha expirado.',
                default => 'No se pudo reducir la cantidad.',
            };

            return back()->with('error', $message);
        }

        return back()->with('success', 'Licencias reducidas correctamente.');
    }
}
