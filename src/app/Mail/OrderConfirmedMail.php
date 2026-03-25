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

    // 1. Define a public property. Laravel automatically sends this to the Blade view!
    public $order;

    // 2. Accept the Order model when this class is created
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    // 3. Set the Subject of the Email
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmed - EcoShop ',
        );
    }

    // 4. Tell Laravel which template to use
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.confirmed',
        );
    }
}
