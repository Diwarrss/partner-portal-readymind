<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\LicenseService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected LicenseService $licenseService
    ) {}

    public function index(Request $request): Response
    {
        $customer = $request->user('customer');
        $this->licenseService->syncLicensesForCustomer($customer);

        $customer->load(['licenseSubscriptions']);

        return Inertia::render('Portal/Dashboard', [
            'licenses' => $customer->licenseSubscriptions,
        ]);
    }
}
