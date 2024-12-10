<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    public function view(User $user, Enrollment $enrollment)
    {
        return $user->id === $enrollment->user_id;
    }

    public function create(User $user, Course $course)
    {
        return !$user->enrollments()->where('course_id', $course->id)->exists();
    }
}
