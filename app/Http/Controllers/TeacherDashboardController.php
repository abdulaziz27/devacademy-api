<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();

        return response()->json([
            'courses' => [
                'total' => $teacher->teacherCourses()->count(),
                'free_courses' => $teacher->teacherCourses()->where('is_premium', false)->count(),
                'premium_courses' => $teacher->teacherCourses()->where('is_premium', true)->count(),
                'courses' => CourseResource::collection(
                    $teacher->teacherCourses()->withCount(['enrollments', 'lessons'])->get()
                )
            ],
            'students' => [
                'total_enrolled' => $teacher->teacherCourses()
                    ->withCount('enrollments')
                    ->get()
                    ->sum('enrollments_count'),
                'completed_courses' => $teacher->teacherCourses()
                    ->whereHas('enrollments', function ($q) {
                        $q->whereNotNull('completed_at');
                    })
                    ->count()
            ]
        ]);
    }
}
