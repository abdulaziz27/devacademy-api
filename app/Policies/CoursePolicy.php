<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function create(User $user)
    {
        return $user->hasRole('admin') || $user->hasRole('teacher');
    }

    public function update(User $user, Course $course)
    {
        return $user->hasRole('admin') || $user->id === $course->teacher_id;
    }

    public function delete(User $user, Course $course)
    {
        return $user->hasRole('admin') || $user->id === $course->teacher_id;
    }
}
