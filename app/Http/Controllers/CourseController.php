<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['teacher', 'category'])
            ->when(request('category'), function ($q) {
                return $q->where('category_id', request('category'));
            })
            ->when(request('is_premium'), function ($q) {
                return $q->where('is_premium', request('is_premium'));
            })
            ->when(request('teacher'), function ($q) {
                return $q->where('teacher_id', request('teacher'));
            })
            ->withCount('lessons')
            ->paginate(10);

        return CourseResource::collection($courses);
    }

    public function store(StoreCourseRequest $request)
    {
        $validated = $request->validated();
        $validated['teacher_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course = Course::create($validated);

        return new CourseResource($course->load(['teacher', 'category']));
    }

    public function show(Course $course)
    {
        $course->load(['teacher', 'category', 'lessons']);
        return new CourseResource($course);
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        \Log::info('Request data:', [
            'name' => $request->input('name'),
            'has_file' => $request->hasFile('thumbnail'),
            'all_data' => $request->all()
        ]);

        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $course->update($validated);

        return new CourseResource($course->load(['teacher', 'category']));
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return response()->json(['message' => 'Course deleted successfully']);
    }
}
