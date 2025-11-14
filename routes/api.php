<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegistrationController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::post('/register', [RegistrationController::class, 'create'])->name('register');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login');
});

Route::group(['middleware' => 'auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::get('/user', [UserController::class, 'index'])->name('user');
});

Route::group(['middleware' => 'auth'], function () {
    Route::apiResource('tasks', TaskController::class);
    Route::patch('tasks/{task}/complete', [TaskController::class, 'markAsComplete'])->name('tasks.complete');
    Route::patch('tasks/{task}/incomplete', [TaskController::class, 'markAsIncomplete'])->name('tasks.incomplete');
});


