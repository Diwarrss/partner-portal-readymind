<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TenantController extends Controller
{
    public function index(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        $paginator = Tenant::withCount('customers')
            ->when($tenantId, fn ($q) => $q->where('id', $tenantId))
            ->orderBy('name')
            ->paginate(15);

        $paginator->setCollection(
            $paginator->getCollection()->map(function (Tenant $tenant) {
                return [
                    'id' => $tenant->id,
                    'code' => $tenant->code,
                    'name' => $tenant->name,
                    'is_sandbox' => $tenant->is_sandbox,
                    'partner_center_configured' => (bool) (
                        $tenant->partner_center_client_id
                        && $tenant->partner_center_tenant_id
                    ),
                    'customers_count' => $tenant->customers_count,
                ];
            })
        );

        $tenants = $paginator;

        return Inertia::render('Admin/Tenants/Index', [
            'tenants' => $tenants,
        ]);
    }

    public function create(Request $request): Response
    {
        if ($request->user()?->tenant_id) {
            abort(403, 'Solo un administrador global puede crear tenants.');
        }

        return Inertia::render('Admin/Tenants/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->user()?->tenant_id) {
            abort(403);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'regex:/^[A-Za-z0-9]+$/', 'unique:tenants,code'],
            'name' => ['required', 'string', 'max:255'],
            'partner_center_client_id' => ['nullable', 'string', 'max:255'],
            'partner_center_client_secret' => ['nullable', 'string'],
            'partner_center_tenant_id' => ['nullable', 'string', 'max:255'],
            'is_sandbox' => ['sometimes', 'boolean'],
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_sandbox'] = $request->boolean('is_sandbox', true);

        Tenant::create($validated);

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant creado correctamente.');
    }

    public function edit(Request $request, Tenant $tenant): Response
    {
        $userTenantId = $request->user()?->tenant_id;
        if ($userTenantId && (int) $tenant->id !== (int) $userTenantId) {
            abort(403);
        }

        return Inertia::render('Admin/Tenants/Edit', [
            'tenant' => [
                'id' => $tenant->id,
                'code' => $tenant->code,
                'name' => $tenant->name,
                'partner_center_client_id' => $tenant->partner_center_client_id,
                'partner_center_tenant_id' => $tenant->partner_center_tenant_id,
                'is_sandbox' => $tenant->is_sandbox,
                'has_partner_center_client_secret' => filled($tenant->partner_center_client_secret),
            ],
            'canDeleteTenant' => ! $request->user()?->tenant_id,
        ]);
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $userTenantId = $request->user()?->tenant_id;
        if ($userTenantId && (int) $tenant->id !== (int) $userTenantId) {
            abort(403);
        }

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Za-z0-9]+$/',
                Rule::unique('tenants', 'code')->ignore($tenant->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'partner_center_client_id' => ['nullable', 'string', 'max:255'],
            'partner_center_client_secret' => ['nullable', 'string'],
            'partner_center_tenant_id' => ['nullable', 'string', 'max:255'],
            'is_sandbox' => ['required', 'boolean'],
        ]);

        $validated['code'] = strtoupper($validated['code']);

        $payload = [
            'code' => $validated['code'],
            'name' => $validated['name'],
            'partner_center_client_id' => $validated['partner_center_client_id'] ?: null,
            'partner_center_tenant_id' => $validated['partner_center_tenant_id'] ?: null,
            'is_sandbox' => $validated['is_sandbox'],
        ];

        if (! empty($validated['partner_center_client_secret'])) {
            $payload['partner_center_client_secret'] = $validated['partner_center_client_secret'];
        }

        $tenant->update($payload);

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant actualizado correctamente.');
    }

    public function destroy(Request $request, Tenant $tenant): RedirectResponse
    {
        if ($request->user()?->tenant_id) {
            abort(403);
        }

        if ($tenant->customers()->exists()) {
            return redirect()
                ->route('admin.tenants.index')
                ->with('error', 'No se puede eliminar un tenant que tiene clientes.');
        }

        $tenant->delete();

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant eliminado.');
    }
}
