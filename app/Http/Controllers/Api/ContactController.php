<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormNotification;
use App\Models\ProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $customer = $request->user();
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $contactRequest = ProductRequest::create([
            'customer_id' => $customer->id,
            'product_name' => $validated['product_name'],
            'quantity' => $validated['quantity'] ?? null,
            'product_description' => $validated['message'],
            'notes' => $validated['message'],
            'status' => 'pending',
        ]);

        $recipients = config('portal.contact_form_recipients', []);
        if (! empty($recipients)) {
            Mail::to($recipients)->send(new ContactFormNotification($contactRequest));
            $contactRequest->update(['notified_at' => now()]);
        }

        return response()->json(['message' => 'Contact request submitted.']);
    }
}
