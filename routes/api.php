<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MaterialController;
use App\Http\Controllers\API\SubmissionController;
use App\Http\Controllers\API\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);
    Route::put('/me', [UserController::class, 'update']);

    // Materials
    Route::get('materials', [MaterialController::class, 'index']);
    Route::get('materials/{id}', [MaterialController::class, 'show']);
    Route::post('materials', [MaterialController::class, 'store']);
    Route::put('materials/{id}', [MaterialController::class, 'update']);
    Route::delete('materials/{id}', [MaterialController::class, 'destroy']);

    // Submissions
    Route::post('submit/{materialId}', [SubmissionController::class, 'store']);
    Route::get('materials/{materialId}/submissions', [SubmissionController::class, 'listByMaterial']);
});
