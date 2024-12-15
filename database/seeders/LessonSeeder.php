<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $courses = Course::all();
        $lessonTitles = [
            'Introduction',
            'Installation and Configuration',
            'Practice',
            'Basics',
            'Advanced Concepts',
            'Final Project'
        ];

        foreach ($courses as $course) {
            $lessonCount = rand(1, 3); // Adjust lesson count to your needs

            for ($i = 0; $i < $lessonCount; $i++) {
                $type = ['video', 'text', 'mixed'][rand(0, 2)];

                Lesson::create([
                    'title' => "Lesson " . ($i + 1) . ": " . $lessonTitles[$i % count($lessonTitles)], // Cycle through the titles array
                    'type' => $type,
                    'content' => fake()->paragraphs(3, true),
                    'video_url' => $type !== 'text' ? "https://www.youtube.com/watch?v=3iM_06QeZi8" : null,
                    'duration' => $type !== 'text' ? rand(15, 60) : null,
                    'order' => $i + 1,
                    'course_id' => $course->id
                ]);
            }
        }
    }
}
