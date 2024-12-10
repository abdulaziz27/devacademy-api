<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);
Route::get('/subscription/plans', [SubscriptionController::class, 'plans']);
Route::post('/subscription/callback', [SubscriptionController::class, 'handleCallback']);



Route::middleware('auth:sanctum')->group(function () {
    // Public Course Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    // Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);

    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{course:slug}', [CourseController::class, 'show']);

    Route::get('/courses/{course:slug}/lessons', [LessonController::class, 'index']);
    Route::get('/courses/{course:slug}/lessons/{lesson}', [LessonController::class, 'show']);


    Route::post('/courses/{course:slug}/enroll', [EnrollmentController::class, 'enroll']);
    Route::get('/my-courses', [EnrollmentController::class, 'myCourses']);
    // Route::post('/lessons/{lesson}/complete', [ProgressController::class, 'markAsComplete']);
    Route::get('/courses/{course:slug}/progress', [ProgressController::class, 'getCourseProgress']);

    Route::post('/lessons/{lesson}/complete', [ProgressController::class, 'markAsComplete'])
        ->middleware('enrolled');

    Route::post('/subscription/subscribe/{plan}', [SubscriptionController::class, 'subscribe']);

    // Route Testing
    if (app()->environment('local')) {
        Route::post('/test/midtrans-signature', [SubscriptionController::class, 'generateTestSignature']);
    }

    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [AdminController::class, 'getAllUsers']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::post('/promote-teacher', [AdminController::class, 'promoteToTeacher']);

        // Category Management
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::post('/categories/{category:slug}/update', [CategoryController::class, 'update']);
        Route::delete('/categories/{category:slug}', [CategoryController::class, 'destroy']);
    });

    // Admin and Teacher Routes
    Route::middleware(['role:admin|teacher'])->group(function () {
        Route::post('/courses', [CourseController::class, 'store']);
        Route::post('/courses/{course:slug}/update', [CourseController::class, 'update']);
        Route::delete('/courses/{course:slug}', [CourseController::class, 'destroy']);

        Route::post('/courses/{course:slug}/lessons', [LessonController::class, 'store']);
        Route::post('/courses/{course:slug}/lessons/{lesson}/update', [LessonController::class, 'update']);
        Route::delete('/courses/{course:slug}/lessons/{lesson}', [LessonController::class, 'destroy']);

        Route::apiResource('courses.lessons', LessonController::class)
            ->except(['index', 'show'])
            ->scoped(['course' => 'slug']);
    });
});
