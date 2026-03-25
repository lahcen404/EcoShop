@component('mail::message')
# Thank you for your eco-friendly purchase! 🌍

Hi {{ $order->user->name }},

Your order **#{{ $order->id }}** has been successfully placed. We are preparing it for shipment!

**Order Summary:**
* Total Amount: **${{ number_format($order->total_price, 2) }}**
* Status: {{ ucfirst($order->status->value) }}

@component('mail::button', ['url' => config('app.url') . '/orders/' . $order->id])
View My Order
@endcomponent

Every purchase at **EcoShop** helps plant a tree. Thank you for making a difference!

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
