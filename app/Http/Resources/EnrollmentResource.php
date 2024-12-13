<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'course' => new CourseResource($this->whenLoaded('course')),
            'enrolled_at' => $this->enrolled_at,
            'completed_at' => $this->completed_at,
            'progress' => [
                'total_lessons' => $this->whenLoaded('course', function () {
                    return $this->course->lessons->count();
                }, 0),
                'completed_lessons' => $this->whenLoaded('course', function () {
                    return auth()->user()
                        ->lessonProgress()
                        ->whereIn('lesson_id', $this->course->lessons->pluck('id'))
                        ->where('is_completed', true)
                        ->count();
                }, 0)
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
