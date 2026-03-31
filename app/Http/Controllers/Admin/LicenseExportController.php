<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LicenseSubscription;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LicenseExportController extends Controller
{
    public function index(Request $request): StreamedResponse
    {
        $month = max(1, min(12, (int) ($request->input('month') ?? now()->month)));
        $year = (int) ($request->input('year') ?? now()->year);
        $tenantId = $request->input('tenant_id');
        $user = $request->user();

        if ($user?->tenant_id) {
            $tenantId = $user->tenant_id;
        }

        $query = LicenseSubscription::query()
            ->with('customer.tenant')
            ->whereYear('synced_at', $year)
            ->whereMonth('synced_at', $month);

        if ($tenantId) {
            $query->whereHas('customer', fn ($q) => $q->where('tenant_id', (int) $tenantId));
        }

        $subscriptions = $query->orderBy('offer_name')->get();
        $filename = sprintf('licenses-%04d-%02d.csv', $year, $month);

        return response()->streamDownload(function () use ($subscriptions): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tenant', 'Cliente', 'Email', 'Producto', 'Cantidad', 'Estado', 'Fecha sync']);

            foreach ($subscriptions as $subscription) {
                $customer = $subscription->customer;
                fputcsv($out, [
                    $customer?->tenant?->code,
                    $customer?->name,
                    $customer?->email,
                    $subscription->offer_name,
                    $subscription->quantity,
                    $subscription->status,
                    $subscription->synced_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
