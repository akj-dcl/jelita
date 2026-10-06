<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Verified - ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.verified',
            with: [
                'order' => $this->order,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
