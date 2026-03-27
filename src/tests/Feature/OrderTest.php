<?php

use App\Enums\UserRole;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('authenticated user can place order from cart', function () {
    $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
    Sanctum::actingAs($user);

    $product = Product::factory()->create([
        'price' => 100,
        'stock_quantity' => 10,
    ]);

    $cart = Cart::create(['user_id' => $user->id]);
    $cart->items()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->postJson('/api/orders');

    $response->assertCreated()
        ->assertJsonPath('user_id', $user->id)
        ->assertJsonPath('total_price', 200);

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'total_price' => 200.00,
    ]);

    $this->assertDatabaseHas('order_items', [
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 100.00,
    ]);

    expect($cart->items()->count())->toBe(0);
});

test('guest cannot place order', function () {
    $this->postJson('/api/orders')->assertUnauthorized();
});

test('cannot place order with empty cart', function () {
    $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
    Sanctum::actingAs($user);
    Cart::create(['user_id' => $user->id]);

    $response = $this->postJson('/api/orders');

    $response->assertStatus(422)
        ->assertJsonPath('message', 'Cart is empty');
});

test('cannot place order with insufficient stock', function () {
    $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
    Sanctum::actingAs($user);

    $product = Product::factory()->create([
        'price' => 50,
        'stock_quantity' => 3,
    ]);

    $cart = Cart::create(['user_id' => $user->id]);
    $cart->items()->create([
        'product_id' => $product->id,
        'quantity' => 8,
    ]);

    $response = $this->postJson('/api/orders');

    $response->assertStatus(422)
        ->assertJsonPath('message', "Insufficient stock for product {$product->name}");

    expect(Order::query()->count())->toBe(0);
    expect(OrderItem::query()->count())->toBe(0);
});

test('admin can view all orders', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    Sanctum::actingAs($admin);

    Order::factory()->count(3)->create();

    $response = $this->getJson('/api/orders');

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

test('customer cannot view all orders', function () {
    $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
    Sanctum::actingAs($customer);

    $response = $this->getJson('/api/orders');

    $response->assertForbidden();
});
