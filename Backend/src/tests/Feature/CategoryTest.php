<?php

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('users can list categories', function () {
    Category::factory()->count(3)->create();

    $response = $this->getJson('/api/categories');

    $response->assertOk()
        ->assertJsonStructure(['data']);
});

test('users can view one category', function () {
    $category = Category::factory()->create();

    $response = $this->getJson('/api/categories/' . $category->id);

    $response->assertOk()
        ->assertJsonPath('id', $category->id);
});

test('admin can create category', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    Sanctum::actingAs($admin);

    $response = $this->postJson('/api/categories', [
        'name' => 'Eco Cleaning',
        'slug' => 'eco-cleaning',
        'description' => 'Cleaning products category',
    ]);

    $response->assertCreated()
        ->assertJsonPath('name', 'Eco Cleaning')
        ->assertJsonPath('slug', 'eco-cleaning');

    $this->assertDatabaseHas('categories', [
        'name' => 'Eco Cleaning',
        'slug' => 'eco-cleaning',
    ]);
});

test('customer cannot create category', function () {
    $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
    Sanctum::actingAs($customer);

    $response = $this->postJson('/api/categories', [
        'name' => 'Should Fail',
        'slug' => 'should-fail',
    ]);

    $response->assertForbidden();
});

test('admin can update category', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    Sanctum::actingAs($admin);
    $category = Category::factory()->create([
        'name' => 'Old Category',
        'slug' => 'old-category',
    ]);

    $response = $this->putJson('/api/categories/' . $category->id, [
        'name' => 'Updated Category',
        'slug' => 'updated-category',
    ]);

    $response->assertOk()
        ->assertJsonPath('name', 'Updated Category')
        ->assertJsonPath('slug', 'updated-category');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Updated Category',
        'slug' => 'updated-category',
    ]);
});

test('admin can delete category', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    Sanctum::actingAs($admin);
    $category = Category::factory()->create();

    $response = $this->deleteJson('/api/categories/' . $category->id);

    $response->assertOk()
        ->assertJsonPath('message', 'Category deleted successfully');

    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});

test('guest cannot create category', function () {
    $response = $this->postJson('/api/categories', [
        'name' => 'No Auth',
        'slug' => 'no-auth',
    ]);

    $response->assertUnauthorized();
});
