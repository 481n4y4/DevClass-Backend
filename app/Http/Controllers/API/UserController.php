<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAdminUpdateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function me(Request $request)
    {
        try {
            return new UserResource($request->user());
        } catch (\Throwable $exception) {
            Log::error('Failed to fetch user profile.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to fetch user profile.',
            ], 500);
        }
    }

    public function update(UserUpdateRequest $request)
    {
        try {
            $user = $request->user();
            $user->update($request->validated());

            return new UserResource($user->refresh());
        } catch (\Throwable $exception) {
            Log::error('Failed to update user profile.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to update user profile.',
            ], 500);
        }
    }

    public function updateUser(UserAdminUpdateRequest $request, int $id)
    {
        if (! $request->user()->isTeacher()) {
            return response()->json([
                'message' => 'Only teachers can update user data.',
            ], 403);
        }

        try {
            $user = User::find($id);
            if (! $user) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404);
            }

            $user->update($request->validated());

            return new UserResource($user->refresh());
        } catch (\Throwable $exception) {
            Log::error('Failed to update user data.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to update user data.',
            ], 500);
        }
    }
}
