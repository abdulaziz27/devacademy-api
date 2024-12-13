<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentSubmissionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'file_url' => $this->file_url ? asset('storage/' . $this->file_url) : null,
            'score' => $this->score,
            'feedback' => $this->feedback,
            'user' => new UserResource($this->whenLoaded('user')),
            'assignment' => new AssignmentResource($this->whenLoaded('assignment')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
