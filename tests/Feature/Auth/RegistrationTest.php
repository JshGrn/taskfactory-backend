<?php

use App\Models\User;

test('user can register with valid credentials', function () {
    $response = $this->postJson(route('auth.register'), [
        'name' => 'Test User',
        'email' => 'test.user@joshgreen.dev',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated();

    expect(
        User::query()
            ->where('email', '=', 'test.user@joshgreen.dev')
            ->exists()
    )->toBeTrue();
});

test('user has valid email', function () {
    $response = $this->postJson(route('auth.register'), [
        'name' => 'Test User',
        'email' => 'not-valid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertJsonValidationErrors('email');
});

test('user password is correctly confirmed', function () {
    $response = $this->postJson(route('auth.register'), [
        'name' => 'Test User',
        'email' => 'not-valid-email',
        'password' => 'password123',
        'password_confirmation' => 'not-confirmed-correctly',
    ]);

    $response->assertJsonValidationErrors('password');
});

test('user cannot register with an existing email', function () {
    $duplicateEmail = 'test.user@joshgreen.dev';

    $response = $this->postJson(route('auth.register'), [
        'name' => 'Test User',
        'email' => $duplicateEmail,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated();

    $response = $this->postJson(route('auth.register'), [
        'name' => 'Another User',
        'email' => $duplicateEmail,
        'password' => '123password',
        'password_confirmation' => '123password',
    ]);

    $response->assertJsonValidationErrors('email');

    expect(
        (bool) User::query()->where('email', '=', $duplicateEmail)->sole()
    )->toBeTrue();
});
