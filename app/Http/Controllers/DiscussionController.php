<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Http\Resources\DiscussionResource;
use App\Http\Requests\StoreDiscussionRequest;
use App\Http\Requests\UpdateDiscussionRequest;
use Illuminate\Support\Facades\Storage;

class DiscussionController extends Controller
{
    public function index()
    {
        $discussions = Discussion::withCount('comments')
            ->latest()
            ->paginate(10);

        return DiscussionResource::collection($discussions);
    }

    public function store(StoreDiscussionRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('discussions', 'public');
            $data['image_url'] = Storage::url($path);
        }

        $data['user_id'] = auth()->id();
        $discussion = Discussion::create($data);

        return new DiscussionResource($discussion->load('comments'));
    }

    public function show(Discussion $discussion)
    {
        return new DiscussionResource($discussion->load('comments'));
    }

    public function update(UpdateDiscussionRequest $request, Discussion $discussion)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($discussion->image_url) {
                Storage::delete(str_replace('/storage/', 'public/', $discussion->image_url));
            }

            $path = $request->file('image')->store('discussions', 'public');
            $data['image_url'] = Storage::url($path);
        }

        $discussion->update($data);

        return new DiscussionResource($discussion->load('comments'));
    }

    public function destroy(Discussion $discussion)
    {
        if (auth()->id() !== $discussion->user_id && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($discussion->image_url) {
            Storage::delete(str_replace('/storage/', 'public/', $discussion->image_url));
        }

        $discussion->delete();

        return response()->json(['message' => 'Discussion deleted successfully']);
    }
}
