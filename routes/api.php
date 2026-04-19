<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AnnouncementController;
use App\Http\Controllers\API\AssignmentController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClassController;
use App\Http\Controllers\API\EnrollmentController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\GradeController;
use App\Http\Controllers\API\MaterialController;
use App\Http\Controllers\API\SubmissionController;
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
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Classes
    Route::get('classes', [ClassController::class, 'index']);
    Route::post('classes', [ClassController::class, 'store']);
    Route::get('classes/{id}', [ClassController::class, 'show']);
    Route::put('classes/{id}', [ClassController::class, 'update']);
    Route::delete('classes/{id}', [ClassController::class, 'destroy']);

    // Enrollments
    Route::post('enroll', [EnrollmentController::class, 'enroll']);
    Route::get('my-classes', [EnrollmentController::class, 'getMyClasses']);
    Route::delete('enroll/{classId}', [EnrollmentController::class, 'unenroll']);

    // Materials
    Route::post('materials', [MaterialController::class, 'store']);
    Route::get('classes/{classId}/materials', [MaterialController::class, 'getByClass']);

    // Assignments
    Route::post('assignments', [AssignmentController::class, 'store']);
    Route::get('classes/{classId}/assignments', [AssignmentController::class, 'getByClass']);

    // Submissions
    Route::post('submissions', [SubmissionController::class, 'store']);
    Route::get('assignments/{assignmentId}/submissions', [SubmissionController::class, 'getByAssignment']);
    Route::get('my-submissions', [SubmissionController::class, 'getMySubmissions']);

    // Grades
    Route::post('grades', [GradeController::class, 'store']);
    Route::get('my-grades', [GradeController::class, 'getMyGrades']);

    // Announcements
    Route::post('announcements', [AnnouncementController::class, 'store']);
    Route::get('classes/{classId}/announcements', [AnnouncementController::class, 'getByClass']);

    // File access
    Route::get('files/material/{id}', [FileController::class, 'material']);
    Route::get('files/submission/{id}', [FileController::class, 'submission']);
});
