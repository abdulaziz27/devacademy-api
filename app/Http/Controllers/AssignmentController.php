<?php

namespace App\Http\Controllers;


use App\Http\Requests\Assignment\StoreAssignmentRequest;
use App\Http\Requests\Assignment\UpdateAssignmentRequest;
use App\Models\Course;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\AssignmentSubmissionResource;
use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Course $course)
    {
        $assignments = $course->assignments()->latest()->get();
        return AssignmentResource::collection($assignments);
    }

    public function store(StoreAssignmentRequest $request, Course $course)
    {
        $assignment = $course->assignments()->create($request->validated());
        return new AssignmentResource($assignment);
    }

    public function show(Course $course, Assignment $assignment)
    {
        return new AssignmentResource($assignment->load(['submissions' => function ($query) {
            $query->where('user_id', auth()->id());
        }]));
    }

    public function update(UpdateAssignmentRequest $request, Course $course, Assignment $assignment)
    {
        $assignment->update($request->validated());
        return new AssignmentResource($assignment);
    }

    public function destroy(Course $course, Assignment $assignment)
    {
        $assignment->delete();
        return response()->json(['message' => 'Assignment deleted successfully']);
    }

    public function submissions(Course $course, Assignment $assignment)
    {
        if ($course->teacher_id !== auth()->id() && !$assignment->submissions->contains('user_id', auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return AssignmentSubmissionResource::collection(
            $assignment->submissions()->with('user')->get()
        );
    }
}
