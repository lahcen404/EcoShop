<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);

        return response()->json($cart->load('items.product'));
    }

    public function addItem(AddCartItemRequest $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $product = Product::findOrFail($request->integer('product_id'));

        $item = $cart->items()->where('product_id', $request->integer('product_id'))->first();
        $requestedQuantity = $request->integer('quantity');
        $currentQuantity = $item?->quantity ?? 0;
        $newQuantity = $currentQuantity + $requestedQuantity;

        if ($newQuantity > $product->stock_quantity) {
            return response()->json([
                'message' => 'Requested quantity exceeds available stock',
            ], 422);
        }

        if ($item) {
            $item->update([
                'quantity' => $newQuantity,
            ]);
        } else {
            $item = $cart->items()->create($request->validated());
        }

        return response()->json($item->load('product'), 201);
    }

    public function updateItem(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);

        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $newQuantity = $request->integer('quantity');
        $availableStock = $cartItem->product->stock_quantity;

        if ($newQuantity > $availableStock) {
            return response()->json([
                'message' => 'Requested quantity exceeds available stock',
            ], 422);
        }

        $cartItem->update($request->validated());

        return response()->json($cartItem->fresh()->load('product'));
    }

    public function removeItem(Request $request, CartItem $cartItem): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);

        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Cart item removed successfully']);
    }

    private function getOrCreateCart(Request $request): Cart
    {
        return Cart::firstOrCreate(['user_id' => $request->user()->id]);
    }
}
