<?php

use Illuminate\Support\Facades\Route;

Route::get('/tasks', function () {
    return response()->json([
        ['id' => 1, 'title' => 'Task One', 'completed' => false],
        ['id' => 2, 'title' => 'Task Two', 'completed' => true],
    ]);
});
