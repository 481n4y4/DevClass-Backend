<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    /**
     * Get assignments by class.
     */
    public function getByClass($classId)
    {
        $class = Classes::find($classId);
        
        if (!$class) {
            return response()->json([
                'status' => 'error',
                'message' => 'Class not found'
            ], 404);
        }
        
        $assignments = Assignment::where('class_id', $classId)
            ->with('submissions')
            ->orderBy('due_date', 'asc')
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $assignments
        ]);
    }

    /**
     * Store a newly created assignment.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date|after:now',
            'max_score' => 'nullable|integer|min:0|max:100'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $class = Classes::find($request->class_id);
        
        // Check if user is the teacher of this class
        if ($class->teacher_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to add assignments to this class'
            ], 403);
        }
        
        $assignment = Assignment::create([
            'class_id' => $request->class_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'max_score' => $request->max_score ?? 100
        ]);
        
        return response()->json([
            'status' => 'success',
            'data' => $assignment,
            'message' => 'Assignment created successfully'
        ], 201);
    }

    /**
     * Update the specified assignment.
     */
    public function update(Request $request, $id)
    {
        $assignment = Assignment::with('class')->find($id);
        
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
                'message' => 'Unauthorized to update this assignment'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'sometimes|date|after:now',
            'max_score' => 'nullable|integer|min:0|max:100'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $assignment->update($request->only(['title', 'description', 'due_date', 'max_score']));
        
        return response()->json([
            'status' => 'success',
            'data' => $assignment,
            'message' => 'Assignment updated successfully'
        ]);
    }

    /**
     * Remove the specified assignment.
     */
    public function destroy($id)
    {
        $assignment = Assignment::with('class')->find($id);
        
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
                'message' => 'Unauthorized to delete this assignment'
            ], 403);
        }
        
        $assignment->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Assignment deleted successfully'
        ]);
    }
}