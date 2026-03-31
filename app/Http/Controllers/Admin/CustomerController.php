<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CustomerCredentials;
use App\Models\Customer;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tenantId = $user?->tenant_id;

        $customers = Customer::with('tenant')
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->when($request->tenant_id, fn ($q) => $q->where('tenant_id', $request->integer('tenant_id')))
            ->orderBy('name')
            ->paginate(15);

        $tenants = Tenant::query()
            ->when($tenantId, fn ($q) => $q->where('id', $tenantId))
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers,
            'tenants' => $tenants,
            'filters' => [
                'tenant_id' => $request->tenant_id,
            ],
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $tenantId = $user?->tenant_id;

        $tenants = Tenant::query()
            ->when($tenantId, fn ($q) => $q->where('id', $tenantId))
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Customers/Create', [
            'tenants' => $tenants,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $scopedTenantId = $user?->tenant_id;

        $validated = $request->validate([
            'tenant_id' => [
                'required',
                'integer',
                Rule::exists('tenants', 'id'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'microsoft_tenant_id' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($scopedTenantId && (int) $validated['tenant_id'] !== (int) $scopedTenantId) {
            abort(403);
        }

        $emailExists = Customer::query()
            ->where('tenant_id', $validated['tenant_id'])
            ->where('email', $validated['email'])
            ->exists();

        if ($emailExists) {
            return back()->withErrors([
                'email' => 'Ya existe un cliente con este email en el tenant seleccionado.',
            ])->withInput();
        }

        $customer = Customer::create([
            'external_id' => (string) Str::uuid(),
            'tenant_id' => $validated['tenant_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'microsoft_tenant_id' => $validated['microsoft_tenant_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (config('portal.send_customer_credentials_email')) {
            Mail::to($customer->email)->send(new CustomerCredentials($customer, $validated['password']));
        }

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Request $request, Customer $customer)
    {
        $user = $request->user();
        if ($user?->tenant_id && $customer->tenant_id !== $user->tenant_id) {
            abort(403);
        }

        $tenantId = $user?->tenant_id;
        $tenants = Tenant::query()
            ->when($tenantId, fn ($q) => $q->where('id', $tenantId))
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Customers/Edit', [
            'customer' => $customer->only([
                'id',
                'tenant_id',
                'name',
                'email',
                'microsoft_tenant_id',
                'is_active',
                'external_id',
            ]),
            'tenants' => $tenants,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $user = $request->user();
        $scopedTenantId = $user?->tenant_id;

        if ($scopedTenantId && $customer->tenant_id !== $scopedTenantId) {
            abort(403);
        }

        $validated = $request->validate([
            'tenant_id' => [
                'required',
                'integer',
                Rule::exists('tenants', 'id'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'microsoft_tenant_id' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if ($scopedTenantId && (int) $validated['tenant_id'] !== (int) $scopedTenantId) {
            abort(403);
        }

        $emailExists = Customer::query()
            ->where('tenant_id', $validated['tenant_id'])
            ->where('email', $validated['email'])
            ->where('id', '!=', $customer->id)
            ->exists();

        if ($emailExists) {
            return back()->withErrors([
                'email' => 'Ya existe un cliente con este email en el tenant seleccionado.',
            ])->withInput();
        }

        $payload = [
            'tenant_id' => $validated['tenant_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'microsoft_tenant_id' => $validated['microsoft_tenant_id'] ?? null,
            'is_active' => $validated['is_active'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $customer->update($payload);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function regenerateCredentials(Request $request, Customer $customer)
    {
        $user = $request->user();
        if ($user?->tenant_id && $customer->tenant_id !== $user->tenant_id) {
            abort(403);
        }

        $password = Str::password(12);
        $customer->update(['password' => $password]);

        if (config('portal.send_customer_credentials_email')) {
            Mail::to($customer->email)->send(new CustomerCredentials($customer, $password));
        }

        return back()->with('success', 'Credenciales regeneradas correctamente.');
    }
}
