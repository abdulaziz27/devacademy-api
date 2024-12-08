<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    // Regular user routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [AdminController::class, 'getAllUsers']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::post('/promote-teacher', [AdminController::class, 'promoteToTeacher']);
    });
});
