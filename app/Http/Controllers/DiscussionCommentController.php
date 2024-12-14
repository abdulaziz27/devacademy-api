<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\DiscussionComment;
use App\Http\Resources\DiscussionCommentResource;
use App\Http\Requests\StoreDiscussionCommentRequest;
use App\Http\Requests\UpdateDiscussionCommentRequest;

class DiscussionCommentController extends Controller
{
    public function index(Discussion $discussion)
    {
        $comments = $discussion->comments()
            ->with('user')
            ->latest()
            ->get();

        return DiscussionCommentResource::collection($comments);
    }


    public function store(StoreDiscussionCommentRequest $request, Discussion $discussion)
    {
        $comment = $discussion->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);

        return new DiscussionCommentResource($comment);
    }

    public function update(UpdateDiscussionCommentRequest $request, DiscussionComment $comment)
    {
        $comment->update($request->validated());

        return new DiscussionCommentResource($comment);
    }

    public function destroy(DiscussionComment $comment)
    {
        if (auth()->id() !== $comment->user_id && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
