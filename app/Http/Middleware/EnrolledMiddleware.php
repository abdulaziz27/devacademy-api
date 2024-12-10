<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnrolledMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $lesson = $request->route('lesson');
        $user = auth()->user();

        if ($lesson->course->is_premium && !$user->hasActiveSubscription()) {
            return response()->json([
                'message' => 'Subscription required'
            ], 403);
        }

        if (!$user->enrollments()->where('course_id', $lesson->course_id)->exists()) {
            return response()->json([
                'message' => 'Please enroll first'
            ], 403);
        }

        return $next($request);
    }
}
