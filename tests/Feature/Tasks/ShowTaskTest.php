<?php

use App\Models\User;

test('user can show only their own tasks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $this->actingAs($user1);

    $response = $this->postJson(route('tasks.store', [
        'name' => 'My task for user 1',
        'description' => 'Task description',
        'due_date' => now()->addDays(3)->toDateString(),
    ]))->assertCreated();

    $this->actingAs($user2);
    $this->postJson(route('tasks.store', [
        'name' => 'My task for user 2',
        'description' => 'Task description',
        'due_date' => now()->addDays(4)->toDateString(),
    ]))->assertCreated();

    $this->postJson(route('tasks.store', [
        'name' => 'My second task for user 2',
        'description' => 'Task 2 description',
        'due_date' => now()->addDays(7)->toDateString(),
    ]))->assertCreated();

    $showResponse = $this->getJson(route('tasks.index'));
    $showResponse->assertOk();
    $showResponse->assertJsonCount(2, 'data');
});

test('user can show their own task', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('tasks.store', [
        'name' => 'My task',
        'description' => 'Task description',
        'due_date' => now()->addDays(3)->toDateString(),
    ]));

    $response->assertCreated();
    $taskUuid = $response->json('task.uuid');
    $this->assertNotNull($taskUuid);

    $showResponse = $this->getJson(route('tasks.show', ['task' => $taskUuid]));
    $showResponse->assertOk();
    $showResponse->assertJsonFragment([
        'uuid' => $taskUuid,
    ]);
});

test('user cannot show others tasks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $this->actingAs($user1);

    $response = $this->postJson(route('tasks.store', [
        'name' => 'User1 task',
        'description' => 'Task description',
        'due_date' => now()->addDays(3)->toDateString(),
    ]));

    $response->assertCreated();
    $taskUuid = $response->json('task.uuid');
    $this->assertNotNull($taskUuid);

    $this->actingAs($user2);
    $showResponse = $this->getJson(route('tasks.show', ['task' => $taskUuid]));
    $showResponse->assertForbidden();
});
