<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductRequestController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = $request->user()?->tenant_id;

        $productRequests = ProductRequest::with('customer.tenant')
            ->when($tenantId, fn ($q) => $q->whereHas('customer', fn ($qq) => $qq->where('tenant_id', $tenantId)))
            ->when($request->tenant_id, fn ($q) => $q->whereHas('customer', fn ($qq) => $qq->where('tenant_id', $request->integer('tenant_id'))))
            ->orderByDesc('created_at')
            ->paginate(15);

        return Inertia::render('Admin/ProductRequests/Index', [
            'productRequests' => $productRequests,
        ]);
    }
}
