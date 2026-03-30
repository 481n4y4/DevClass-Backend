<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Enroll user to a class.
     */
    public function enroll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $class = Classes::find($request->class_id);
        
        // Check if class exists and is active
        if (!$class || $class->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Class not available for enrollment'
            ], 404);
        }
        
        // Check if already enrolled
        $existingEnrollment = Enrollment::where('user_id', Auth::id())
            ->where('class_id', $request->class_id)
            ->first();
        
        if ($existingEnrollment) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are already enrolled in this class'
            ], 409);
        }
        
        // Create enrollment
        $enrollment = Enrollment::create([
            'user_id' => Auth::id(),
            'class_id' => $request->class_id,
            'enrolled_at' => now()
        ]);
        
        return response()->json([
            'status' => 'success',
            'data' => $enrollment,
            'message' => 'Successfully enrolled in class'
        ], 201);
    }

    /**
     * Get classes the authenticated user is enrolled in.
     */
    public function getMyClasses()
    {
        $classes = Auth::user()->classes()
            ->with('teacher')
            ->orderBy('enrollments.created_at', 'desc')
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $classes
        ]);
    }

    /**
     * Unenroll from a class.
     */
    public function unenroll($classId)
    {
        $class = Classes::find($classId);
        
        if (!$class) {
            return response()->json([
                'status' => 'error',
                'message' => 'Class not found'
            ], 404);
        }
        
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('class_id', $classId)
            ->first();
        
        if (!$enrollment) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not enrolled in this class'
            ], 404);
        }
        
        $enrollment->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully unenrolled from class'
        ]);
    }
}   