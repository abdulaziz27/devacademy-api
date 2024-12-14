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

        foreach ($courses as $course) {
            $lessonCount = rand(1, 2);

            for ($i = 1; $i <= $lessonCount; $i++) {
                $type = ['video', 'text', 'mixed'][rand(0, 2)];

                Lesson::create([
                    'title' => "Lesson {$i}: " . fake()->sentence(),
                    'type' => $type,
                    'content' => fake()->paragraphs(3, true),
                    'video_url' => $type !== 'text' ? "lessons/https://www.youtube.com/watch?v=3iM_06QeZi8" : null,
                    'duration' => $type !== 'text' ? rand(15, 60) : null,
                    'order' => $i,
                    'course_id' => $course->id
                ]);
            }
        }
    }
}
