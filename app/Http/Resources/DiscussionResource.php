<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscussionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar
            ],
            'comments_count' => $this->comments_count,
            'comments' => DiscussionCommentResource::collection($this->whenLoaded('comments')),
            'can_edit' => auth()->id() === $this->user_id,
            'can_delete' => auth()->id() === $this->user_id || auth()->user()->role === 'admin'
        ];
    }
}
