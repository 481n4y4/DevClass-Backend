<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    /**
     * Display a listing of classes.
     */
    public function index()
    {
        $classes = Classes::with('teacher')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $classes
        ]);
    }

    /**
     * Display the specified class with materials and assignments.
     */
    public function show($id)
    {
        $class = Classes::with(['teacher', 'materials', 'assignments'])->find($id);
        
        if (!$class) {
            return response()->json([
                'status' => 'error',
                'message' => 'Class not found'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $class
        ]);
    }

    /**
     * Store a newly created class.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,archived'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $class = Classes::create([
            'title' => $request->title,
            'description' => $request->description,
            'teacher_id' => Auth::id(),
            'status' => $request->status ?? 'active'
        ]);
        
        return response()->json([
            'status' => 'success',
            'data' => $class,
            'message' => 'Class created successfully'
        ], 201);
    }

    /**
     * Update the specified class.
     */
    public function update(Request $request, $id)
    {
        $class = Classes::find($id);
        
        if (!$class) {
            return response()->json([
                'status' => 'error',
                'message' => 'Class not found'
            ], 404);
        }
        
        // Check if user is the teacher who created the class
        if ($class->teacher_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to update this class'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,archived'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $class->update($request->only(['title', 'description', 'status']));
        
        return response()->json([
            'status' => 'success',
            'data' => $class,
            'message' => 'Class updated successfully'
        ]);
    }

    /**
     * Remove the specified class.
     */
    public function destroy($id)
    {
        $class = Classes::find($id);
        
        if (!$class) {
            return response()->json([
                'status' => 'error',
                'message' => 'Class not found'
            ], 404);
        }
        
        // Check if user is the teacher who created the class
        if ($class->teacher_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to delete this class'
            ], 403);
        }
        
        $class->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Class deleted successfully'
        ]);
    }
}