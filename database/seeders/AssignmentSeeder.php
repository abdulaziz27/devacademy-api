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

        foreach ($courses as $course) {
            $assignmentCount = rand(1, 2);

            for ($i = 1; $i <= $assignmentCount; $i++) {
                Assignment::create([
                    'course_id' => $course->id,
                    'title' => "Assignment {$i}: " . fake()->sentence(),
                    'description' => fake()->paragraphs(2, true),
                    'due_date' => now()->addDays(rand(7, 30))
                ]);
            }
        }

        // Create some sample submissions
        $students = User::role('student')->get();
        $assignments = Assignment::all();

        foreach ($students as $student) {
            // Submit to random assignments
            $randomAssignments = $assignments->random(3);

            foreach ($randomAssignments as $assignment) {
                if ($student->enrollments()->where('course_id', $assignment->course_id)->exists()) {
                    AssignmentSubmission::create([
                        'assignment_id' => $assignment->id,
                        'user_id' => $student->id,
                        'content' => fake()->paragraphs(3, true),
                        'score' => rand(60, 100),
                        'feedback' => fake()->sentence()
                    ]);
                }
            }
        }
    }
}
