<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\LicenseExportController;
use App\Http\Controllers\Admin\ProductRequestController as AdminProductRequestController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\LicenseController;
use App\Http\Controllers\Portal\ProductRequestController as PortalProductRequestController;
use App\Http\Controllers\ProfileController;
use App\Models\Customer;
use App\Models\ProductRequest;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes (auth + admin role)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/', function (Request $request) {
        $tenantId = $request->user()?->tenant_id;
        $customersCount = Customer::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->count();
        $requestsCount = ProductRequest::query()
            ->when($tenantId, fn ($q) => $q->whereHas('customer', fn ($qq) => $qq->where('tenant_id', $tenantId)))
            ->count();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'tenants' => Tenant::query()->when($tenantId, fn ($q) => $q->where('id', $tenantId))->count(),
                'customers' => $customersCount,
                'productRequests' => $requestsCount,
            ],
        ]);
    })->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::patch('/tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
    Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::patch('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::post('/customers/{customer}/regenerate-credentials', [CustomerController::class, 'regenerateCredentials'])->name('customers.regenerate-credentials');
    Route::get('/product-requests', [AdminProductRequestController::class, 'index'])->name('product-requests.index');
    Route::get('/export/licenses', [LicenseExportController::class, 'index'])->name('export.licenses');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Portal de clientes (guard: customer) - login unificado en /login
Route::prefix('portal')->name('portal.')->group(function () {
    Route::redirect('login', '/login')->name('login');

    Route::middleware('auth:customer')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('licenses', [LicenseController::class, 'index'])->name('licenses.index');
        Route::post('licenses/{subscription}/increment', [LicenseController::class, 'increment'])->name('licenses.increment');
        Route::post('licenses/{subscription}/decrement', [LicenseController::class, 'decrement'])->name('licenses.decrement');
        Route::post('product-requests', [PortalProductRequestController::class, 'store'])->name('product-requests.store');
    });
});

require __DIR__.'/auth.php';
