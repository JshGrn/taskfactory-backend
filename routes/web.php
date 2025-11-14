<?php

use Illuminate\Support\Facades\Route;

Route::get('/sanctum/csrf-token', function () {
    return response()->noContent();
});

