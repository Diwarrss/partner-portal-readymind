<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'external_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $customer = Customer::query()
            ->where('external_id', $validated['external_id'])
            ->where('is_active', true)
            ->first();

        if (! $customer || ! Hash::check($validated['password'], $customer->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 422);
        }

        $customer->tokens()->delete();
        $token = $customer->createToken('portal-api-token', ['subscriptions:read', 'subscriptions:write', 'contact:write']);

        return response()->json([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'external_id' => $customer->external_id,
            ],
        ]);
    }
}
