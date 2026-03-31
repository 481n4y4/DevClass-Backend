<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Classes
    Route::apiResource('classes', ClassController::class)->except(['index', 'show']);
    Route::get('classes', [ClassController::class, 'index']);
    Route::get('classes/{id}', [ClassController::class, 'show']);
    
    // Materials
    Route::get('classes/{classId}/materials', [MaterialController::class, 'getByClass']);
    Route::apiResource('materials', MaterialController::class)->except(['index', 'show']);
    
    // Assignments
    Route::get('classes/{classId}/assignments', [AssignmentController::class, 'getByClass']);
    Route::apiResource('assignments', AssignmentController::class)->except(['index', 'show']);
    
    // Submissions
    Route::post('submissions', [SubmissionController::class, 'store']);
    Route::get('assignments/{assignmentId}/submissions', [SubmissionController::class, 'getByAssignment']);
    Route::get('my-submissions', [SubmissionController::class, 'getMySubmissions']);
    
    // Enrollments
    Route::post('enroll', [EnrollmentController::class, 'enroll']);
    Route::get('my-classes', [EnrollmentController::class, 'getMyClasses']);
    Route::delete('enroll/{classId}', [EnrollmentController::class, 'unenroll']);
});