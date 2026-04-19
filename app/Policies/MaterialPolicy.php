<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\Material;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaterialPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Material $material): bool
    {
        return $this->canAccessClass($user, $material->classroom);
    }

    public function createForClass(User $user, Classroom $classroom): bool
    {
        return $this->canManageClass($user, $classroom);
    }

    public function update(User $user, Material $material): bool
    {
        return $this->canManageClass($user, $material->classroom);
    }

    public function delete(User $user, Material $material): bool
    {
        return $this->canManageClass($user, $material->classroom);
    }

    private function canManageClass(User $user, Classroom $classroom): bool
    {
        return $user->isAdmin() || ($user->isTeacher() && $classroom->teacher_id === $user->id);
    }

    private function canAccessClass(User $user, Classroom $classroom): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $classroom->teacher_id === $user->id;
        }

        return $classroom->students()->where('users.id', $user->id)->exists();
    }
}
