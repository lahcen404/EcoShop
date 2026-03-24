<?php

use App\Models\User;
use App\Enums\UserRole;

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
