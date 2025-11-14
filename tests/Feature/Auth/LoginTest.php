<?php

use App\Models\User;

test('user can login with valid credentials', function () {
    $knownUserDetails = [
        'email' => 'known.user@joshgreen.dev',
        'password' => 'securepassword123',
    ];

    $expectedUser = User::factory()->create($knownUserDetails);

    $this->postJson(route('auth.login'), $knownUserDetails)->assertOk();
    $this->assertAuthenticatedAs($expectedUser);
});

test('user cannot login with incorrect credentials', function () {
    $knownUserDetails = [
        'email' => 'known.user@joshgreen.dev',
        'password' => 'securepassword123',
    ];

    $user = User::factory()->create($knownUserDetails);

    $this->postJson(route('auth.login'), [
        ...$knownUserDetails,
        'password' => 'wrongpassword',
    ])->assertUnauthorized();
});

test('user can login and access protected routes', function () {
    $knownUserDetails = [
        'email' => 'known.user@joshgreen.dev',
        'password' => 'securepassword123',
    ];

    User::factory()->create($knownUserDetails);

    $this->postJson(route('auth.login'), $knownUserDetails);

    $this->getJson(route('auth.user'))->assertOk()->assertJsonStructure([
        'id',
        'name',
        'email',
    ]);
});
