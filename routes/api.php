<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/token', [CustomerAuthController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/subscriptions', [SubscriptionController::class, 'index']);
        Route::post('/subscriptions/{subscription}/increment', [SubscriptionController::class, 'increment']);
        Route::post('/subscriptions/{subscription}/decrement', [SubscriptionController::class, 'decrement']);
        Route::post('/contact', [ContactController::class, 'store']);
    });
});
