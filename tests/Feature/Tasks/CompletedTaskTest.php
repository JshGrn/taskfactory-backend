<?php

use App\Models\User;

test('user can mark their test as complete', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('tasks.store'), [
        'name' => 'Test task',
        'description' => 'This is a description for my test task',
        'due_date' => now()->addWeek()->toDateString(),
    ]);

    $response->assertCreated();
    $taskUuid = $response->json('task.uuid');
    $this->assertNotNull($taskUuid);

    $response->assertJsonFragment([
        'is_completed' => false,
    ]);

    $completeResponse = $this->patchJson(
        route('tasks.complete', ['task' => $taskUuid])
    );

    $completeResponse->assertOk();
    $completeResponse->assertJsonFragment([
        'uuid' => $taskUuid,
        'is_completed' => true,
    ]);
});

test('user cannot mark non existent task as complete', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $nonExistentTaskUuid = '123e4567-e89b-12d3-a456-426614174000';

    $completeResponse = $this->patchJson(
        route('tasks.complete', ['task' => $nonExistentTaskUuid])
    );

    $completeResponse->assertNotFound();
});

test('user can mark their completed task as incomplete', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('tasks.store'), [
        'name' => 'Test task',
        'description' => 'This is a description for my test task',
        'due_date' => now()->addWeek()->toDateString(),
    ]);

    $response->assertCreated();
    $taskUuid = $response->json('task.uuid');
    $this->assertNotNull($taskUuid);

    $completeResponse = $this->patchJson(
        route('tasks.complete', ['task' => $taskUuid])
    );

    $completeResponse->assertOk();
    $completeResponse->assertJsonFragment([
        'is_completed' => true,
    ]);

    $incompleteResponse = $this->patchJson(
        route('tasks.incomplete', ['task' => $taskUuid])
    );

    $incompleteResponse->assertOk();
    $incompleteResponse->assertJsonFragment([
        'uuid' => $taskUuid,
        'is_completed' => false,
    ]);
});

test('user cannot mark other users tasks as complete', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $this->actingAs($user1);

    $response = $this->postJson(route('tasks.store'), [
        'name' => 'User 1\'s test task',
        'description' => 'This is a description for my test task',
        'due_date' => now()->addWeek()->toDateString(),
    ]);

    $response->assertCreated();
    $taskUuid = $response->json('task.uuid');
    $this->assertNotNull($taskUuid);

    $this->actingAs($user2);

    $completeResponse = $this->patchJson(
        route('tasks.complete', ['task' => $taskUuid])
    );

    $completeResponse->assertForbidden();
});
