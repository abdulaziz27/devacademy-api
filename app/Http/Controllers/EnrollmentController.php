<?php

namespace App\Http\Controllers;

use App\Http\Resources\EnrollmentResource;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function enroll(Course $course)
    {
        if ($course->is_premium && !auth()->user()->hasActiveSubscription()) {
            return response()->json(['message' => 'Subscription required'], 403);
        }

        $enrollment = Enrollment::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'enrolled_at' => now()
        ]);

        return new EnrollmentResource($enrollment);
    }

    public function myCourses()
    {
        $enrollments = auth()->user()->enrollments()->with('course')->get();
        return EnrollmentResource::collection($enrollments);
    }
}
