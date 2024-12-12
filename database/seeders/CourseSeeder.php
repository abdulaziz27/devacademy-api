<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $teachers = User::role('teacher')->get();
        $categories = Category::all();

        foreach ($categories as $category) {
            // Free Courses (2 per category)
            Course::create([
                'title' => "Basic {$category->name} Course",
                'slug' => Str::slug("Basic {$category->name} Course"),
                'description' => "Learn basic {$category->name} from scratch",
                'is_premium' => false,
                'teacher_id' => $teachers->random()->id,
                'category_id' => $category->id,
            ]);

            Course::create([
                'title' => "Introduction to {$category->name}",
                'slug' => Str::slug("Introduction to {$category->name}"),
                'description' => "Start your journey in {$category->name}",
                'is_premium' => false,
                'teacher_id' => $teachers->random()->id,
                'category_id' => $category->id,
            ]);

            Course::create([
                'title' => "Advanced {$category->name} Masterclass",
                'slug' => Str::slug("Advanced {$category->name} Masterclass"),
                'description' => "Deep dive into advanced {$category->name} concepts",
                'is_premium' => true,
                'teacher_id' => $teachers->random()->id,
                'category_id' => $category->id,
            ]);

            Course::create([
                'title' => "Professional {$category->name}",
                'slug' => Str::slug("Professional {$category->name}"),
                'description' => "Master {$category->name} like a pro",
                'is_premium' => true,
                'teacher_id' => $teachers->random()->id,
                'category_id' => $category->id,
            ]);
        }
    }
}
