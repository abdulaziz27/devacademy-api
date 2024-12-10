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
            'enrolled_at' => $this->enrolled_at,
            'completed_at' => $this->completed_at,
            'course' => new CourseResource($this->whenLoaded('course')),
            'progress' => [
                'completed_lessons' => $this->course->lessons()
                    ->whereHas('progress', function ($q) {
                        $q->where('user_id', auth()->id())
                            ->where('is_completed', true);
                    })->count(),
                'total_lessons' => $this->course->lessons()->count()
            ]
        ];
    }
}
