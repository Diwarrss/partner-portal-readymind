<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(
        protected LicenseService $licenseService
    ) {}

    /**
     * Generate license report for customers.
     * Disabled in Phase 1 - config reports.enabled = false
     */
    public function generateLicenseReport(Customer|Collection $customers, string $format = 'csv'): string
    {
        $customers = $customers instanceof Collection
            ? $customers
            : collect([$customers]);

        if ($format === 'csv') {
            return $this->generateCsv($customers);
        }

        if ($format === 'pdf') {
            return $this->generatePdf($customers);
        }

        return '';
    }

    protected function generateCsv(Collection $customers): string
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Cliente', 'Producto', 'Cantidad', 'Estado', 'Última sincronización']);

        foreach ($customers as $customer) {
            foreach ($customer->licenseSubscriptions as $sub) {
                fputcsv($output, [
                    $customer->name,
                    $sub->offer_name,
                    $sub->quantity,
                    $sub->status,
                    $sub->synced_at?->format('Y-m-d H:i'),
                ]);
            }
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    protected function generatePdf(Collection $customers): string
    {
        // Placeholder - requires DomPDF or similar
        return '';
    }
}
