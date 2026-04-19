<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssignmentPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Assignment $assignment): bool
    {
        return $this->canAccessClass($user, $assignment->classroom);
    }

    public function viewSubmissions(User $user, Assignment $assignment): bool
    {
        return $this->canManageClass($user, $assignment->classroom);
    }

    public function createForClass(User $user, Classroom $classroom): bool
    {
        return $this->canManageClass($user, $classroom);
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $this->canManageClass($user, $assignment->classroom);
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $this->canManageClass($user, $assignment->classroom);
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
