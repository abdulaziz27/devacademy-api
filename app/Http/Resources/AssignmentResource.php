<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'course' => new CourseResource($this->whenLoaded('course')),
            'submissions_count' => $this->whenLoaded('submissions', function () {
                return $this->submissions->count();
            }),
            'created_at' => $this->created_at
        ];
    }
}
