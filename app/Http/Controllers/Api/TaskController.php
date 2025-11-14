<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index()
    {
        return new TaskCollection(
            Task::query()
                ->where('user_id', auth()->id())
                ->paginate()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
        ]);

        $taskModel = auth()->user()->tasks()->create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'due_date' => $request->input('due_date'),
        ]);

        return response()->json([
            'message' => 'Task created successfully.',
            'task' => new TaskResource($taskModel),
        ], 201);
    }

    public function show(Task $task)
    {
        Gate::authorize('view', $task);

        return new TaskResource($task);
    }

    public function update(Task $task, Request $request)
    {
        Gate::authorize('update', $task);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
        ]);

        $task->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'due_date' => $request->input('due_date'),
        ]);

        return [
            'message' => 'Task updated successfully.',
            'task' => new TaskResource($task),
        ];
    }

    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return response()->noContent();
    }

    public function markAsComplete(Task $task)
    {
        Gate::authorize('update', $task);

        $task->update([
            'completed_at' => now()
        ]);

        return new TaskResource($task);
    }

    public function markAsIncomplete(Task $task)
    {
        Gate::authorize('update', $task);

        $task->update([
            'completed_at' => null
        ]);

        return new TaskResource($task);
    }
}
