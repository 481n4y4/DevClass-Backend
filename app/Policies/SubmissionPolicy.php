<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubmissionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Submission $submission): bool
    {
        $classroom = $submission->assignment->classroom;

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $classroom->teacher_id === $user->id;
        }

        return $submission->student_id === $user->id;
    }

    public function createForAssignment(User $user, Assignment $assignment): bool
    {
        if (! $user->isStudent()) {
            return false;
        }

        return $assignment->classroom
            ->students()
            ->where('users.id', $user->id)
            ->exists();
    }
}
