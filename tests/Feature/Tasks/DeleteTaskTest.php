<?php

use App\Models\User;

test('user can delete their own task', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('tasks.store'), [
        'name' => 'Task to be deleted',
        'description' => 'This task will be deleted in the test',
        'due_date' => now()->addWeek()->toDateString(),
    ]);

    $response->assertCreated();
    $taskUuid = $response->json('task.uuid');
    $this->assertNotNull($taskUuid);

    $deleteResponse = $this->deleteJson(route('tasks.destroy', ['task' => $taskUuid]));
    $deleteResponse->assertNoContent();

    $showResponse = $this->getJson(route('tasks.show', ['task' => $taskUuid]));
    $showResponse->assertNotFound();
});

test('user cannot delete other users task', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $this->actingAs($user1);

    $response = $this->postJson(route('tasks.store'), [
        'name' => 'User 1\'s task to be deleted',
        'description' => 'This task will be attempted to be deleted by another user',
        'due_date' => now()->addWeek()->toDateString(),
    ]);

    $response->assertCreated();
    $taskUuid = $response->json('task.uuid');
    $this->assertNotNull($taskUuid);

    $this->actingAs($user2);

    $deleteResponse = $this->deleteJson(route('tasks.destroy', ['task' => $taskUuid]));
    $deleteResponse->assertForbidden();
});
