<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lesson\LessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function index(Course $course)
    {
        $lessons = $course->lessons()->orderBy('order')->get();
        return LessonResource::collection($lessons);
    }

    public function store(LessonRequest $request, Course $course)
    {
        $validated = $request->validated();
        $validated['course_id'] = $course->id;

        if ($request->hasFile('video')) {
            $validated['video_url'] = $request->file('video')->store('lessons', 'public');
        }

        $lesson = Lesson::create($validated);
        return response()->json([
            'message' => 'Lesson created successfully',
            'lesson' => new LessonResource($lesson)
        ], 201);
    }

    public function show(Course $course, Lesson $lesson)
    {
        return new LessonResource($lesson);
    }

    public function update(LessonRequest $request, Course $course, Lesson $lesson)
    {
        $validated = $request->validated();

        if ($request->hasFile('video')) {
            if ($lesson->video_url) {
                Storage::disk('public')->delete($lesson->video_url);
            }
            $validated['video_url'] = $request->file('video')->store('lessons', 'public');
        }

        $lesson->update($validated);
        return new LessonResource($lesson->fresh());
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        if ($lesson->video_url) {
            Storage::disk('public')->delete($lesson->video_url);
        }
        $lesson->delete();
        return response()->json(['message' => 'Lesson deleted successfully']);
    }
}
