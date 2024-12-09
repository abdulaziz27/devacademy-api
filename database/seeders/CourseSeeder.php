<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $teacher = User::role('teacher')->first();

        Course::create([
            'title' => 'Laravel Development',
            'slug' => 'laravel-development',
            'description' => 'Learn Laravel from scratch',
            'is_premium' => true,
            'teacher_id' => $teacher->id,
            'category_id' => Category::where('slug', 'programming')->first()->id
        ]);
    }
}
