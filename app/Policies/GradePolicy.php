<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GradePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Grade $grade): bool
    {
        $classroom = $grade->submission->assignment->classroom;

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $classroom->teacher_id === $user->id;
        }

        return $grade->submission->student_id === $user->id;
    }

    public function createForSubmission(User $user, Submission $submission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isTeacher() && $submission->assignment->classroom->teacher_id === $user->id;
    }
}
