<?php

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('users can list products', function () {
    Product::factory()->count(3)->create();

    $response = $this->getJson('/api/products');

    $response->assertOk()
        ->assertJsonStructure(['data']);
});

test('users can view one product', function () {
    $product = Product::factory()->create();

    $response = $this->getJson('/api/products/' . $product->id);

    $response->assertOk()
        ->assertJsonPath('id', $product->id);
});

test('users can filter products by category id', function () {
    $categoryA = Category::factory()->create();
    $categoryB = Category::factory()->create();

    Product::factory()->count(2)->create(['category_id' => $categoryA->id]);
    Product::factory()->count(1)->create(['category_id' => $categoryB->id]);

    $response = $this->getJson('/api/products?category_id=' . $categoryA->id);

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

test('admin can create product', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    Sanctum::actingAs($admin); // act as admin without login

    $category = Category::factory()->create();

    $response = $this->postJson('/api/products', [
        'category_id' => $category->id,
        'name' => 'Bamboo Toothbrush',
        'description' => 'Eco-friendly toothbrush',
        'price' => 25.50,
        'stock_quantity' => 100,
        'image_url' => 'https://example.com/toothbrush.jpg',
    ]);

    $response->assertCreated()
        ->assertJsonPath('name', 'Bamboo Toothbrush');

    $this->assertDatabaseHas('products', [
        'name' => 'Bamboo Toothbrush',
        'category_id' => $category->id,
    ]);
});

test('customer cannot create product', function () {
    $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
    Sanctum::actingAs($customer); // act as customer without login

    $category = Category::factory()->create();

    $response = $this->postJson('/api/products', [
        'category_id' => $category->id,
        'name' => 'Should Not Be Created',
        'description' => 'Forbidden action',
        'price' => 10,
        'stock_quantity' => 5,
    ]);

    $response->assertForbidden();
});

test('admin can update and delete product', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    Sanctum::actingAs($admin);

    $product = Product::factory()->create(['name' => 'Old Name']);

    $updateResponse = $this->putJson('/api/products/' . $product->id, [
        'name' => 'New Name',
        'price' => 42.99,
    ]);

    $updateResponse->assertOk()
        ->assertJsonPath('name', 'New Name');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'New Name',
    ]);

    $deleteResponse = $this->deleteJson('/api/products/' . $product->id);
    $deleteResponse->assertOk();

    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});
