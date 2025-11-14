<?php

use App\Models\User;

test('user can update their own task', function(){
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('tasks.store'), [
        'name' => 'My test task',
        'description' => 'This is a description for my test task',
        'due_date' => now()->addWeek()->toDateString(),
    ]);

    $response->assertCreated();
    $taskUuid = $response->json('task.uuid');
    $this->assertNotNull($taskUuid);

    $updateResponse = $this->putJson(
        route('tasks.update', ['task' => $taskUuid]),
        [
            'name' => 'Updated task name',
            'description' => 'Updated description'
        ]
    );

    $updateResponse->assertOk();
    $updateResponse->assertJsonFragment([
        'uuid' => $taskUuid,
        'name' => 'Updated task name',
        'description' => 'Updated description',
    ]);
});

test('user cannot update other users task', function(){
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

    $updateResponse = $this->putJson(
        route('tasks.update', ['task' => $taskUuid]),
        [
            'name' => 'Updated task user 1sname',
            'description' => 'Updated description'
        ]
    );

    $updateResponse->assertForbidden();
});
