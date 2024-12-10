<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run()
    {
        SubscriptionPlan::create([
            'name' => 'Monthly Plan',
            'description' => 'Access all premium courses for 30 days',
            'price' => 99000,
            'duration_in_days' => 30,
            'is_active' => true
        ]);

        SubscriptionPlan::create([
            'name' => 'Yearly Plan',
            'description' => 'Access all premium courses for 365 days',
            'price' => 999000,
            'duration_in_days' => 365,
            'is_active' => true
        ]);
    }
}
