<?php

test('user can logout successfully whilst logged in', function () {
    $knownUserDetails = [
        'email' => 'test.user@joshgreen.dev',
        'password' => 'securepassword123',
    ];

    $expectedUser = \App\Models\User::factory()->create($knownUserDetails);

    $this->post('/api/auth/login', $knownUserDetails)->assertOk();
    $this->assertAuthenticatedAs($expectedUser);

    $this->post(route('auth.logout'))->assertOk();
    $this->assertGuest();
});

test('user cant logout if not logged in', function () {
    $this->post(route('auth.logout'))->assertRedirect();
    $this->assertGuest();
});
