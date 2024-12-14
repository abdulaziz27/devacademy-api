<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AssignmentSubmissionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TeacherDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);
Route::get('/subscription/plans', [SubscriptionController::class, 'plans']);
Route::post('/subscription/callback', [SubscriptionController::class, 'handleCallback']);
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->middleware('auth.optional');



Route::middleware('auth:sanctum')->group(function () {
    // Public Course Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    // Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
    Route::get('/subscription/status', [AuthController::class, 'checkSubscriptionStatus']);

    Route::get('/courses/{course:slug}/lessons', [LessonController::class, 'index']);



    Route::post('/courses/{course:slug}/enroll', [EnrollmentController::class, 'enroll']);
    Route::get('/my-courses', [EnrollmentController::class, 'myCourses']);
    // Route::post('/lessons/{lesson}/complete', [ProgressController::class, 'markAsComplete']);
    Route::get('/courses/{course:slug}/progress', [ProgressController::class, 'getCourseProgress']);


    // Get Lesson Details
    Route::get('/courses/{course:slug}/lessons/{lesson}', [LessonController::class, 'show']);

    // Mark Lesson Complete
    Route::post('/courses/{course:slug}/lessons/{lesson}/complete', [ProgressController::class, 'markAsComplete'])->middleware('enrolled');

    // Make Subscription
    Route::post('/subscription/subscribe/{plan}', [SubscriptionController::class, 'subscribe']);



    // Submit Assignment
    // Route::post('/courses/{course:slug}/assignments/{assignment}/submit', [AssignmentSubmissionController::class, 'store']);

    // Get Assignment Submissions (Teacher only)
    Route::middleware('role:teacher')->get(
        '/courses/{course:slug}/assignments/{assignment}/submissions',
        [AssignmentController::class, 'submissions']
    );


    // Course Completion and Download Certificate
    Route::post('/courses/{course:slug}/complete', [EnrollmentController::class, 'completeCourse']);
    Route::get('/certificates', [CertificateController::class, 'index']);
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download']);

    // Student Assigments

    // Get All Assignments
    Route::get('/courses/{course:slug}/assignments', [AssignmentController::class, 'index']);

    // Get Assignment Details
    Route::get('/courses/{course:slug}/assignments/{assignment}', [AssignmentController::class, 'show']);

    // Submit Assignment
    Route::post('/courses/{course:slug}/assignments/{assignment}/submit', [AssignmentSubmissionController::class, 'store']);


    // Student Dashboard
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index']);
    Route::get('/student/courses/{course:slug}/progress', [StudentDashboardController::class, 'courseProgress']);

    // Student & Teacher can view assignments
    Route::get('/courses/{course:slug}/assignments', [AssignmentController::class, 'index']);

    // Student submission routes
    // Route::post('/assignments/{assignment}/submit', [AssignmentSubmissionController::class, 'store']);

    // Get Assignment Submissions (Teacher only)
    Route::get('/courses/{course:slug}/assignments/{assignment}/submissions', [AssignmentController::class, 'submissions']);

    // Route Testing
    if (app()->environment('local')) {
        Route::post('/test/midtrans-signature', [SubscriptionController::class, 'generateTestSignature']);
    }

    // Teacher Routes
    Route::middleware(['role:teacher'])->group(function () {
        Route::get('/teacher/dashboard', [TeacherDashboardController::class, 'index']);

        Route::post('/courses/{course:slug}/assignments', [AssignmentController::class, 'store']);


        Route::post('/assignments/{assignment}/submissions/{submission}/grade', [AssignmentSubmissionController::class, 'grade']);
    });

    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [AdminController::class, 'getAllUsers']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::post('/promote-teacher', [AdminController::class, 'promoteToTeacher']);

        // Category Management
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::post('/categories/{category:slug}/update', [CategoryController::class, 'update']);
        Route::delete('/categories/{category:slug}', [CategoryController::class, 'destroy']);

        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index']);
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


        Route::post('/courses/{course:slug}/assignments', [AssignmentController::class, 'store']);
        Route::post('/courses/{course:slug}/assignments/{assignment}/update', [AssignmentController::class, 'update']);
        Route::delete('/courses/{course:slug}/assignments/{assignment}', [AssignmentController::class, 'destroy']);
        Route::get('/assignments/{assignment}/submissions', [AssignmentController::class, 'submissions']);
        Route::post('/assignments/{assignment}/submissions/{submission}/grade', [AssignmentSubmissionController::class, 'grade']);
    });
});
