<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): Response|RedirectResponse
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->intended(route('portal.dashboard'));
        }

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            return redirect()->intended(
                $user?->hasRole('admin') ? route('admin.dashboard') : route('dashboard')
            );
        }

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'canRegister' => Route::has('register'),
            'status' => session('status'),
            'tenants' => Tenant::orderBy('name')->get(['id', 'code', 'name']),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     * Redirects to admin or portal based on which guard authenticated.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if (Auth::guard('customer')->check()) {
            return redirect()->intended(route('portal.dashboard'));
        }

        $user = Auth::guard('web')->user();

        return redirect()->intended(
            $user?->hasRole('admin') ? route('admin.dashboard') : route('dashboard')
        );
    }

    /**
     * Destroy an authenticated session (both admin and customer).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        Auth::guard('customer')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
