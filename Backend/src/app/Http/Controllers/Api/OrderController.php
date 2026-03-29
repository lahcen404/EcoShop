<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Events\OrderPlaced;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $cart = Cart::query()
            ->with('items.product')
            ->firstOrCreate(['user_id' => $user->id]);

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        foreach ($cart->items as $item) {
            if ($item->quantity > $item->product->stock_quantity) {
                return response()->json([
                    'message' => "Insufficient stock for product {$item->product->name}",
                ], 422);
            }
        }

        $order = DB::transaction(function () use ($cart, $user) {
            $totalPrice = $cart->items->sum(fn ($item) => $item->quantity * $item->product->price);

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => OrderStatus::PENDING,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                ]);
            }

            OrderPlaced::dispatch($order->load('user', 'items.product'));

            $cart->items()->delete();

            return $order;
        });

        return response()->json($order->load('items.product'), 201);
    }

    // Admiin Ordeers
    public function index(Request $request): JsonResponse
    {
        if ($request->user()->role !== UserRole::ADMIN) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $orders = Order::with('user', 'items.product')->latest()->paginate(10);

        return response()->json($orders);
    }
}
