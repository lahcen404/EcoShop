<?php

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

test('new users can register as a customer', function () {
    $email = 'lahcen.test@example.com';

    $response = $this->postJson('/api/register', [
        'name' => 'Lahcen Test',
        'email' => $email,
        'password' => 'lahcen123',
        'password_confirmation' => 'lahcen123',
    ]);

    $response->assertCreated();

    // cheeck if user in DB
    $this->assertDatabaseHas('users', [
        'email' => $email,
        'role'  => UserRole::CUSTOMER->value,
    ]);


    // cheeck user data
    $user = User::where('email', $email)->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Lahcen Test');
});


// logiin teest

test('existing user can login and receive token', function () {

    // create user in DB

    $user = User::factory()->create([
        'name' => 'Lahcen Login',
        'email' => 'lahcen.maskour2003@gmail.com',
        'password' => Hash::make('lahcen123'),
        'role' => UserRole::CUSTOMER,
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'lahcen.maskour2003@gmail.com',
        'password' => 'lahcen123',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['user', 'token'])
        ->assertJsonPath('user.email', $user->email);

    expect($response->json('token'))->not->toBeEmpty();
});

test('authenticated user can view profile', function () {
    $user = User::factory()->create([
        'role' => UserRole::CUSTOMER,
    ]);

     Sanctum::actingAs($user);

    $response = $this->getJson('/api/profile');

    $response->assertOk()
        ->assertJsonPath('user.email', $user->email);
});

test('unauthenticated user cannot view profile', function () {
    $response = $this->getJson('/api/profile');

    $response->assertUnauthorized();
});
