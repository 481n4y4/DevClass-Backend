<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MaterialController;
use App\Http\Controllers\API\SubmissionController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
    Route::put('/users/{id}', [UserController::class, 'updateUser']);

    // Materials
    Route::get('materials', [MaterialController::class, 'index']);
    Route::get('materials/{id}', [MaterialController::class, 'show']);
    Route::post('materials', [MaterialController::class, 'store']);
    Route::put('materials/{id}', [MaterialController::class, 'update']);
    Route::delete('materials/{id}', [MaterialController::class, 'destroy']);

    // Submissions
    Route::post('submit/{materialId}', [SubmissionController::class, 'store']);
    Route::get('materials/{materialId}/submissions', [SubmissionController::class, 'listByMaterial']);

    // SFTP download
    Route::get('/download/{path}', function (string $path) {
        try {
            $decodedPath = urldecode($path);
            $disk = Storage::disk('sftp');

            if (! $disk->exists($decodedPath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            $stream = $disk->readStream($decodedPath);
            if (! is_resource($stream)) {
                return response()->json(['error' => 'Failed to read file'], 500);
            }

            $fileName = basename($decodedPath);

            return response()->streamDownload(function () use ($stream): void {
                fpassthru($stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }, $fileName);
        } catch (\Throwable $exception) {
            Log::error('Download error: ' . $exception->getMessage());

            return response()->json(['error' => 'Failed to download file'], 500);
        }
    })->where('path', '.*');
});
