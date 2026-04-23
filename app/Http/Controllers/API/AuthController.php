<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login($request->validated());

            return response()->json([
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ]);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            Log::error('Login failed.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to login.',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request->user());

            return response()->json([
                'message' => 'Logged out successfully.',
            ]);
        } catch (\Throwable $exception) {
            Log::error('Logout failed.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to logout.',
            ], 500);
        }
    }
}
