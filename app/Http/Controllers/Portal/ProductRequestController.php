<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormNotification;
use App\Models\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ProductRequestController extends Controller
{
    public function store(Request $request)
    {
        $customer = $request->user('customer');

        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $productRequest = ProductRequest::create([
            'customer_id' => $customer->id,
            'product_name' => $validated['product_name'],
            'quantity' => $validated['quantity'] ?? null,
            'product_description' => $validated['message'],
            'notes' => $validated['message'],
            'status' => 'pending',
        ]);

        $recipients = config('portal.contact_form_recipients', []);
        if (! empty($recipients)) {
            Mail::to($recipients)->send(new ContactFormNotification($productRequest));
            $productRequest->update(['notified_at' => now()]);
        }

        return back()->with('success', 'Mensaje enviado correctamente. Nuestro equipo te contactará.');
    }
}
