<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lesson\LessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
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

        if ($request->has('video_url')) {
            $validated['video_url'] = $request->video_url;  // Simpan URL eksternal
        }

        $lesson = Lesson::create($validated);
        return response()->json([
            'message' => 'Lesson created successfully',
            'lesson' => new LessonResource($lesson)
        ], 201);
    }

    public function show(Course $course, Lesson $lesson)
    {
        $userId = auth()->id();

        $lessonProgress = LessonProgress::where('user_id', $userId)
            ->where('lesson_id', $lesson->id)
            ->first();

        $isCompleted = $lessonProgress ? $lessonProgress->is_completed : false;

        return response()->json([
            'lesson' => new LessonResource($lesson),
            'is_completed' => $isCompleted,
        ]);
    }

    public function update(LessonRequest $request, Course $course, Lesson $lesson)
    {
        $validated = $request->validated();

        if ($request->has('video_url')) {
            $validated['video_url'] = $request->video_url;  // Update dengan URL baru
        }

        $lesson->update($validated);
        return new LessonResource($lesson->fresh());
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        $lesson->delete();
        return response()->json(['message' => 'Lesson deleted successfully']);
    }
}
