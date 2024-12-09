<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'courses_count' => $this->whenCounted('courses'),
            'created_at' => $this->created_at
        ];
    }
}
