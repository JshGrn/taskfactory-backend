<?php

use App\Models\User;

test('logged in user can create a valid task', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('tasks.store'), [
        'name' => 'My first task',
        'description' => 'This is a description for my first task',
        'due_date' => now()->addWeek()->toDateString(),
    ]);

    $response->assertCreated();
});

test('logged in user cannot create a task without name', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('tasks.store'), [
        'description' => 'This is a description for my first task',
        'due_date' => now()->addWeek()->toDateString(),
    ]);

    $response->assertJsonValidationErrors('name');
});

test('logged in user cannot create a task with invalid due date', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('tasks.store'), [
        'name' => 'My first task',
        'description' => 'This is a description for my first task',
        'due_date' => 'invalid-date',
    ]);

    $response->assertJsonValidationErrors('due_date');
});

test('logged out user cannot create a valid task', function () {
    $response = $this->postJson(route('tasks.store'), [
        'name' => 'My first task',
        'description' => 'This is a description for my first task',
        'due_date' => now()->addWeek()->toDateString(),
    ]);

    $response->assertStatus(401);
});
