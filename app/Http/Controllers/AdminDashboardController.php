<?php

namespace App\Http\Controllers;

use App\Http\Resources\CertificateResource;
use App\Http\Resources\EnrollmentResource;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionTransaction;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'users' => [
                'total' => User::count(),
                'students' => User::role('student')->count(),
                'teachers' => User::role('teacher')->count(),
                'admin' => User::role('admin')->count()
            ],
            'courses' => [
                'total' => Course::count(),
                'free' => Course::where('is_premium', false)->count(),
                'premium' => Course::where('is_premium', true)->count()
            ],
            'subscriptions' => [
                'active_subscriptions' => UserSubscription::where('end_date', '>', now())
                    ->where('is_active', true)
                    ->count(),
                'total_revenue' => SubscriptionTransaction::where('status', 'settlement')
                    ->sum('amount')
            ],
            'recent_activities' => [
                'new_enrollments' => EnrollmentResource::collection(
                    Enrollment::with(['user', 'course'])
                        ->latest()
                        ->take(5)
                        ->get()
                ),
                'recent_completions' => CertificateResource::collection(
                    Certificate::with(['user', 'course'])
                        ->latest()
                        ->take(5)
                        ->get()
                )
            ]
        ]);
    }
}
