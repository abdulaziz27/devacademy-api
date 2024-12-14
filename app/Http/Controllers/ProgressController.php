<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function markAsComplete(Lesson $lesson)
    {
        // Check if course is premium and user has subscription
        if ($lesson->course->is_premium && !auth()->user()->hasActiveSubscription()) {
            return response()->json([
                'message' => 'Subscription required to access premium content'
            ], 403);
        }

        // Check if user is enrolled
        $isEnrolled = auth()->user()->enrollments()
            ->where('course_id', $lesson->course_id)
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'message' => 'Please enroll in this course first'
            ], 403);
        }

        $progress = LessonProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'lesson_id' => $lesson->id
            ],
            [
                'is_completed' => true,
                'completed_at' => now()
            ]
        );

        return response()->json([
            'message' => 'Lesson marked as complete',
            'progress' => new LessonProgressResource($progress)
        ]);
    }

    public function getCourseProgress(Course $course)
    {
        $totalLessons = $course->lessons()->count();
        $completedLessons = auth()->user()
            ->lessonProgress()
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->where('is_completed', true)
            ->count();

        return response()->json([
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'progress_percentage' => ($totalLessons > 0) ? ($completedLessons / $totalLessons) * 100 : 0
        ]);
    }
}
