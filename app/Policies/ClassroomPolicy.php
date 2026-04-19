<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassroomPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Classroom $classroom): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $classroom->teacher_id === $user->id;
        }

        return $classroom->students()->where('users.id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function update(User $user, Classroom $classroom): bool
    {
        return $user->isAdmin() || ($user->isTeacher() && $classroom->teacher_id === $user->id);
    }

    public function delete(User $user, Classroom $classroom): bool
    {
        return $user->isAdmin() || ($user->isTeacher() && $classroom->teacher_id === $user->id);
    }
}
