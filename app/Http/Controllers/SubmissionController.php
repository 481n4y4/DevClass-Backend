<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    /**
     * Submit an assignment.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assignment_id' => 'required|exists:assignments,id',
            'file_path' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $assignment = Assignment::find($request->assignment_id);
        
        // Check if assignment exists
        if (!$assignment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Assignment not found'
            ], 404);
        }
        
        // Check if student is enrolled in the class
        $isEnrolled = Auth::user()->classes()->where('classes.id', $assignment->class_id)->exists();
        
        if (!$isEnrolled) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must be enrolled in this class to submit assignments'
            ], 403);
        }
        
        // Check if already submitted
        $existingSubmission = Submission::where('assignment_id', $request->assignment_id)
            ->where('user_id', Auth::id())
            ->first();
        
        if ($existingSubmission) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already submitted this assignment'
            ], 409);
        }
        
        // Check if due date has passed
        if (now()->gt($assignment->due_date)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission deadline has passed'
            ], 422);
        }
        
        $submission = Submission::create([
            'assignment_id' => $request->assignment_id,
            'user_id' => Auth::id(),
            'file_path' => $request->file_path,
            'notes' => $request->notes,
            'submitted_at' => now()
        ]);
        
        return response()->json([
            'status' => 'success',
            'data' => $submission,
            'message' => 'Assignment submitted successfully'
        ], 201);
    }

    /**
     * Get submissions for a specific assignment.
     */
    public function getByAssignment($assignmentId)
    {
        $assignment = Assignment::find($assignmentId);
        
        if (!$assignment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Assignment not found'
            ], 404);
        }
        
        // Check if user is the teacher of the class
        if ($assignment->class->teacher_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to view submissions for this assignment'
            ], 403);
        }
        
        $submissions = Submission::where('assignment_id', $assignmentId)
            ->with('user')
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $submissions
        ]);
    }

    /**
     * Get submissions by the authenticated user.
     */
    public function getMySubmissions()
    {
        $submissions = Submission::where('user_id', Auth::id())
            ->with(['assignment', 'assignment.class'])
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $submissions
        ]);
    }
}