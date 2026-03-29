<?php

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('authenticated user can view cart', function () {
    $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/cart');

    $response->assertOk()
        ->assertJsonStructure(['id', 'user_id', 'items']);
});

test('authenticated user can add product to cart', function () {
    $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
    Sanctum::actingAs($user);
    $product = Product::factory()->create();

    $response = $this->postJson('/api/cart/items', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response->assertCreated()
        ->assertJsonPath('product_id', $product->id)
        ->assertJsonPath('quantity', 2);
});

test('user cannot add quantity greater than available stock', function () {
    $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
    Sanctum::actingAs($user);
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $response = $this->postJson('/api/cart/items', [
        'product_id' => $product->id,
        'quantity' => 20,
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'Requested quantity exceeds available stock');
});

test('adding same product increments quantity', function () {
    $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
    Sanctum::actingAs($user);
    $product = Product::factory()->create();

    $this->postJson('/api/cart/items', [
        'product_id' => $product->id,
        'quantity' => 2,
    ])->assertCreated();

    $response = $this->postJson('/api/cart/items', [
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    $response->assertCreated()
        ->assertJsonPath('quantity', 5);
});

test('authenticated user can update cart item quantity', function () {
    $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
    Sanctum::actingAs($user);
    $product = Product::factory()->create();

    $item = $this->postJson('/api/cart/items', [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->json();

    $response = $this->putJson('/api/cart/items/' . $item['id'], [
        'quantity' => 7,
    ]);

    $response->assertOk()
        ->assertJsonPath('id', $item['id'])
        ->assertJsonPath('quantity', 7);
});

test('user cannot update cart item quantity greater than stock', function () {
    $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
    Sanctum::actingAs($user);
    $product = Product::factory()->create(['stock_quantity' => 5]);

    $item = $this->postJson('/api/cart/items', [
        'product_id' => $product->id,
        'quantity' => 2,
    ])->json();

    $response = $this->putJson('/api/cart/items/' . $item['id'], [
        'quantity' => 9,
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'Requested quantity exceeds available stock');
});

test('authenticated user can remove item from cart', function () {
    $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
    Sanctum::actingAs($user);
    $product = Product::factory()->create();

    $item = $this->postJson('/api/cart/items', [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->json();

    $response = $this->deleteJson('/api/cart/items/' . $item['id']);

    $response->assertOk()
        ->assertJsonPath('message', 'Cart item removed successfully');
});

test('guest cannot access cart endpoints', function () {
    $product = Product::factory()->create();

    $this->getJson('/api/cart')->assertUnauthorized();
    $this->postJson('/api/cart/items', [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertUnauthorized();
});
