<?php

namespace App\Http\Requests\Auth;

use App\Models\Customer;
use App\Models\Tenant;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'tenant_code' => ['nullable', 'string', 'max:10'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     * Tries admin (users) first, then customer. Priority: admin if email exists in both.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');
        $remember = $this->boolean('remember');

        // Try admin (users) first
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            RateLimiter::clear($this->throttleKey());

            return;
        }

        $tenantCode = strtoupper((string) $this->input('tenant_code', ''));

        // Try customer scoped by tenant code to avoid cross-tenant auth collisions.
        if ($tenantCode !== '') {
            $tenant = Tenant::query()->where('code', $tenantCode)->first();
            if ($tenant) {
                $customer = Customer::query()
                    ->where('tenant_id', $tenant->id)
                    ->where('email', $credentials['email'])
                    ->where('is_active', true)
                    ->first();

                if ($customer && Hash::check($credentials['password'], $customer->password)) {
                    Auth::guard('customer')->login($customer, $remember);
                    RateLimiter::clear($this->throttleKey());

                    return;
                }
            }
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->string('email'))
            .'|'.Str::lower((string) $this->input('tenant_code', ''))
            .'|'.$this->ip()
        );
    }
}
