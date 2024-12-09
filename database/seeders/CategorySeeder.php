<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['name' => 'Programming', 'slug' => 'programming', 'icon' => 'categories/code.png'],
            ['name' => 'Design', 'slug' => 'design', 'icon' => 'categories/palette.png'],
            ['name' => 'Business', 'slug' => 'business', 'icon' => 'categories/briefcase.png'],
            ['name' => 'Marketing', 'slug' => 'marketing', 'icon' => 'categories/trending-up.png']
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
