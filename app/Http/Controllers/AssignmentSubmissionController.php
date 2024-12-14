<?php

namespace App\Http\Controllers;

use App\Http\Requests\Submission\GradeSubmissionRequest;
use App\Http\Requests\Submission\StoreSubmissionRequest;
use App\Http\Resources\AssignmentSubmissionResource;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\Request;

class AssignmentSubmissionController extends Controller
{
    public function store(StoreSubmissionRequest $request, Course $course, Assignment $assignment)
    {
        // Check if student is enrolled
        if (!auth()->user()->enrollments()->where('course_id', $assignment->course_id)->exists()) {
            return response()->json(['message' => 'You must be enrolled to submit'], 403);
        }

        $validated = $request->validated();

        if ($request->hasFile('file_url')) {
            $validated['file_url'] = $request->file('file_url')->store('submissions', 'public');
        }

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'file_url' => $validated['file_url'] ?? null
        ]);

        return new AssignmentSubmissionResource($submission);
    }

    public function grade(GradeSubmissionRequest $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        // Check if teacher owns the course
        if ($assignment->course->teacher_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $submission->update($request->validated());

        return new AssignmentSubmissionResource($submission);
    }
}
