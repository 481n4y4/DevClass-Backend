<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\ClassroomRepositoryInterface;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    public function __construct(
        private readonly EnrollmentRepositoryInterface $enrollments,
        private readonly ClassroomRepositoryInterface $classrooms
    ) {}

    public function enroll(User $user, string $classCode)
    {
        if (! $user->isStudent()) {
            throw new AuthorizationException('Only students can enroll.');
        }

        $classroom = $this->classrooms->findByCode($classCode);
        if (! $classroom) {
            throw new ModelNotFoundException();
        }

        if ($this->enrollments->findByUserAndClass($user->id, $classroom->id)) {
            throw ValidationException::withMessages([
                'class_code' => 'Already enrolled in this class.',
            ]);
        }

        return $this->enrollments->create([
            'user_id' => $user->id,
            'class_id' => $classroom->id,
        ]);
    }

    public function myClasses(User $user, ?string $search = null)
    {
        $query = $this->classrooms->query()->with('teacher');

        if ($user->isTeacher()) {
            $query->where('teacher_id', $user->id);
        } elseif ($user->isStudent()) {
            $query->whereHas('enrollments', function ($builder) use ($user): void {
                $builder->where('user_id', $user->id);
            });
        }

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query->paginate((int) config('devclass.pagination.per_page'));
    }

    public function unenroll(User $user, int $classId): void
    {
        if (! $user->isStudent()) {
            throw new AuthorizationException('Only students can unenroll.');
        }

        $enrollment = $this->enrollments->findByUserAndClass($user->id, $classId);
        if (! $enrollment) {
            throw ValidationException::withMessages([
                'class_id' => 'Enrollment not found.',
            ]);
        }

        $this->enrollments->delete($enrollment);
    }
}
