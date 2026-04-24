<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MaterialController;
use App\Http\Controllers\API\SubmissionController;
use App\Http\Controllers\API\UserController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return response()->json([
        'message' => 'Unauthenticated.',
    ], 401);
})->name('login');

// Fallback for misconfigured reverse proxies that rewrite /api/login to /login.
Route::post('/login', [AuthController::class, 'login'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

// Fallback for proxies that rewrite /api/* to /*
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::put('/me', [UserController::class, 'update'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
    Route::put('/users/{id}', [UserController::class, 'updateUser'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post('/logout', [AuthController::class, 'logout'])
        ->withoutMiddleware([VerifyCsrfToken::class]);

    Route::get('/materials', [MaterialController::class, 'index']);
    Route::get('/materials/{id}', [MaterialController::class, 'show']);
    Route::post('/materials', [MaterialController::class, 'store'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
    Route::put('/materials/{id}', [MaterialController::class, 'update'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
    Route::delete('/materials/{id}', [MaterialController::class, 'destroy'])
        ->withoutMiddleware([VerifyCsrfToken::class]);

    Route::post('/submit/{materialId}', [SubmissionController::class, 'store'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
    Route::get('/materials/{materialId}/submissions', [SubmissionController::class, 'listByMaterial']);
});
