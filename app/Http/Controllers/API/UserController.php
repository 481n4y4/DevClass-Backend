<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreUserRequest;
use App\Http\Requests\AdminUpdateUserRequest;
use App\Http\Requests\UserAdminUpdateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    public function index(Request $request)
    {
        if (! $request->user()->canManageUsers()) {
            return response()->json([
                'message' => 'Only teacher or admin can access user list.',
            ], 403);
        }

        $query = User::query()->orderBy('kelas')->orderBy('kelas_index')->orderBy('no_absen');

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->string('kelas'));
        }

        if ($request->filled('kelas_index')) {
            $query->where('kelas_index', $request->string('kelas_index'));
        }

        return UserResource::collection($query->get());
    }

    public function store(AdminStoreUserRequest $request)
    {
        if (! $request->user()->canManageUsers()) {
            return response()->json([
                'message' => 'Only teacher or admin can create users.',
            ], 403);
        }

        try {
            $data = $request->validated();

            $data['password'] = empty($data['password'] ?? null)
                ? Hash::make($data['nis'])
                : Hash::make($data['password']);

            $data['no_absen'] = $data['no_absen'] ?? 0;
            $data['kelas'] = $data['kelas'] ?? '10';
            $data['kelas_index'] = $data['kelas_index'] ?? '1';

            $user = User::create($data);

            return (new UserResource($user->refresh()))->response()->setStatusCode(201);
        } catch (\Throwable $exception) {
            Log::error('Failed to create user.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to create user.',
            ], 500);
        }
    }

    public function show(Request $request, int $id)
    {
        if (! $request->user()->canManageUsers()) {
            return response()->json([
                'message' => 'Only teacher or admin can access user data.',
            ], 403);
        }

        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        return new UserResource($user);
    }

    public function updateAdmin(AdminUpdateUserRequest $request, int $id)
    {
        if (! $request->user()->canManageUsers()) {
            return response()->json([
                'message' => 'Only teacher or admin can update users.',
            ], 403);
        }

        try {
            $user = User::find($id);
            if (! $user) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404);
            }

            $data = $request->validated();

            if (array_key_exists('password', $data)) {
                $data['password'] = empty($data['password'])
                    ? $user->password
                    : Hash::make($data['password']);
            }

            $data['no_absen'] = $data['no_absen'] ?? $user->no_absen;
            $data['kelas'] = $data['kelas'] ?? $user->kelas;
            $data['kelas_index'] = $data['kelas_index'] ?? $user->kelas_index;

            $user->update($data);

            return new UserResource($user->refresh());
        } catch (\Throwable $exception) {
            Log::error('Failed to update user.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to update user.',
            ], 500);
        }
    }

    public function destroy(Request $request, int $id)
    {
        if (! $request->user()->canManageUsers()) {
            return response()->json([
                'message' => 'Only teacher or admin can delete users.',
            ], 403);
        }

        try {
            $user = User::find($id);

            if (! $user) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404);
            }

            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully.',
            ]);
        } catch (\Throwable $exception) {
            Log::error('Failed to delete user.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to delete user.',
            ], 500);
        }
    }
}
