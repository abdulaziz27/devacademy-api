<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@devacademy.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
        $admin->assignRole('admin');

        // Teacher
        $teacher = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@devacademy.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
        $teacher->assignRole('teacher');

        // Students
        User::create([
            'name' => 'Student User',
            'email' => 'student@devacademy.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ])->assignRole('student');
    }
}
