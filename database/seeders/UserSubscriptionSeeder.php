<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSubscriptionSeeder extends Seeder
{
    public function run()
    {
        $student = User::role('student')->first();
        UserSubscription::create([
            'user_id' => $student->id,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'is_active' => true
        ]);
    }
}
