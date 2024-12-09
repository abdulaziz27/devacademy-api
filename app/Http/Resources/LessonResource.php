<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'content' => $this->when($request->user()?->can('view', $this->resource), $this->content),
            'video_url' => $this->when(
                $request->user()?->can('view', $this->resource),
                $this->video_url ? asset('storage/' . $this->video_url) : null
            ),
            'duration' => $this->duration,
            'order' => $this->order
        ];
    }
}
