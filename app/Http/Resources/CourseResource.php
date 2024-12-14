<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'trailer_url' => $this->trailer_url,
            'is_premium' => $this->is_premium,
            'teacher' => new UserResource($this->whenLoaded('teacher')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
            'lessons_count' => $this->whenLoaded('lessons', function () {
                return $this->lessons->count();
            }),
            'created_at' => $this->created_at
        ];

        if (isset($this->additional['is_enrolled'])) {
            $data['is_enrolled'] = $this->additional['is_enrolled'];
        }

        if (isset($this->additional['user_type'])) {
            $data['user_type'] = $this->additional['user_type'];
        }

        return $data;
    }
}
