<?php

namespace App\Http\Controllers;

use App\Http\Resources\CertificateResource;
use App\Services\CertificateService;
use App\Http\Resources\EnrollmentResource;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

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

    public function completeCourse(Course $course)
    {
        // Cek semua lessons sudah complete
        $totalLessons = $course->lessons()->count();
        $completedLessons = auth()->user()
            ->lessonProgress()
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->where('is_completed', true)
            ->count();

        if ($totalLessons !== $completedLessons) {
            return response()->json([
                'message' => 'Complete all lessons first'
            ], 400);
        }

        // Generate certificate
        $certificate = $this->certificateService->generateCertificate(
            auth()->user(),
            $course
        );

        // Update enrollment status
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->first();

        $enrollment->update([
            'completed_at' => now()
        ]);

        return response()->json([
            'message' => 'Course completed successfully',
            'certificate' => new CertificateResource($certificate)
        ]);
    }
}
