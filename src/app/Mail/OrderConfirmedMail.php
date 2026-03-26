<?php
namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    // subject
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmed - EcoShop ',
        );
    }

    // content
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.confirmed',
        );
    }
}
