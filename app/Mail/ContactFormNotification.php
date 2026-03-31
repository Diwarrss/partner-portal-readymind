<?php

namespace App\Mail;

use App\Models\ProductRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ProductRequest $productRequest) {}

    public function envelope(): Envelope
    {
        $customer = $this->productRequest->customer;

        return new Envelope(
            subject: 'ReadyMind — Solicitud de productos: '.($customer?->name ?? 'Portal'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form-notification',
        );
    }
}
