<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable
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
            subject: 'Order Placed - ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.placed',
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
