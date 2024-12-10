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
        $course = Course::first();

        Lesson::create([
            'title' => 'Introduction to Laravel',
            'type' => 'video',
            'content' => 'Introduction content',
            'video_url' => 'lessons/intro.mp4',
            'duration' => 60,
            'order' => 1,
            'course_id' => $course->id
        ]);
    }
}
