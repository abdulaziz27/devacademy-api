<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\AssignmentSubmission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    public function run()
    {
        $courses = Course::all();

        // Static assignments titles and descriptions (disesuaikan untuk semua kursus)
        $defaultAssignmentTitle = 'Assignment: Course Completion';
        $defaultAssignmentDescription = 'Complete the exercises and submit your solution for evaluation.';
        $defaultDueDateRange = [7, 30]; // Due date within 7 to 30 days

        foreach ($courses as $course) {
            // Create one assignment for each course
            Assignment::create([
                'course_id' => $course->id,
                'title' => $defaultAssignmentTitle,
                'description' => $defaultAssignmentDescription,
                'due_date' => now()->addDays(rand($defaultDueDateRange[0], $defaultDueDateRange[1]))
            ]);
        }

        // Create some sample submissions
        $students = User::role('student')->get();
        $assignments = Assignment::all();

        foreach ($students as $student) {
            // Find assignments related to the courses the student is enrolled in
            $studentAssignments = $assignments->filter(function ($assignment) use ($student) {
                return $student->enrollments()->where('course_id', $assignment->course_id)->exists();
            });

            if ($studentAssignments->isNotEmpty()) {
                // Select one random assignment from the student's assignments
                $randomAssignment = $studentAssignments->random(1)->first();

                // Create submission for the selected assignment
                AssignmentSubmission::create([
                    'assignment_id' => $randomAssignment->id,
                    'user_id' => $student->id,
                    'content' => 'I have completed the assignment according to the instructions, and I believe the solution works as expected.',
                    'score' => rand(60, 100),
                    'feedback' => 'Good job! Please review the sections on error handling and optimization.'
                ]);
            }
        }
    }
}
