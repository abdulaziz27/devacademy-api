<?php

namespace App\Http\Controllers;

use App\Http\Resources\CertificateResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\LessonProgressResource;
use App\Models\Course;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $enrollments = $user->enrollments()->with(['course.lessons'])->get();

        return response()->json([
            'subscription' => [
                'is_active' => $user->hasActiveSubscription(),
                'details' => $user->subscriptions()
                    ->where('end_date', '>', now())
                    ->where('is_active', true)
                    ->first()
            ],
            'enrolled_courses' => [
                'total' => $enrollments->count(),
                'completed' => $enrollments->where('completed_at', '!=', null)->count(),
                'in_progress' => $enrollments->where('completed_at', null)->count(),
                'courses' => EnrollmentResource::collection($enrollments)
            ],
            'certificates' => CertificateResource::collection(
                $user->certificates()->with('course')->get()
            )
        ]);
    }

    public function courseProgress(Course $course)
    {
        // Check if student is enrolled
        $enrollment = auth()->user()
            ->enrollments()
            ->where('course_id', $course->id)
            ->firstOr(function () {
                return response()->json([
                    'message' => 'You are not enrolled in this course'
                ], 403);
            });

        // If student is enrolled, get progress
        $totalLessons = $course->lessons()->count();
        $completedLessons = auth()->user()
            ->lessonProgress()
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->where('is_completed', true)
            ->count();

        $progressPercentage = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100)
            : 0;

        return response()->json([
            'course' => new CourseResource($course),
            'progress' => [
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'percentage' => $progressPercentage
            ],
            'enrollment' => [
                'enrolled_at' => optional($enrollment)->enrolled_at,
                'completed_at' => optional($enrollment)->completed_at
            ]
        ]);
    }
}
