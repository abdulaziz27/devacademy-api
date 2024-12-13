<?php

namespace App\Http\Controllers;

use App\Http\Resources\CertificateResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\LessonProgressResource;
use App\Models\Course;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return response()->json([
            'subscription' => [
                'is_active' => $user->hasActiveSubscription(),
                'details' => $user->subscriptions()
                    ->where('end_date', '>', now())
                    ->where('is_active', true)
                    ->first()
            ],
            'enrolled_courses' => [
                'total' => $user->enrollments()->count(),
                'completed' => $user->enrollments()->whereNotNull('completed_at')->count(),
                'in_progress' => $user->enrollments()->whereNull('completed_at')->count(),
                'courses' => EnrollmentResource::collection(
                    $user->enrollments()->with(['course.lessons'])->get()
                )
            ],
            'certificates' => CertificateResource::collection(
                $user->certificates()->with('course')->get()
            ),
            'recent_activities' => LessonProgressResource::collection(
                $user->lessonProgress()
                    ->with(['lesson.course'])
                    ->latest()
                    ->take(5)
                    ->get()
            )
        ]);
    }

    public function courseProgress(Course $course)
    {
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->firstOrFail();

        $totalLessons = $course->lessons()->count();
        $completedLessons = auth()->user()
            ->lessonProgress()
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->where('is_completed', true)
            ->count();

        return response()->json([
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'progress_percentage' => ($totalLessons > 0)
                ? round(($completedLessons / $totalLessons) * 100)
                : 0,
            'last_activity' => $enrollment->updated_at
        ]);
    }
}
