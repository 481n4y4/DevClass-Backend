<?php

namespace App\Repositories\Eloquent;

use App\Models\Enrollment;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentRepository implements EnrollmentRepositoryInterface
{
    public function query(): Builder
    {
        return Enrollment::query();
    }

    public function findByUserAndClass(int $userId, int $classId): ?Enrollment
    {
        return Enrollment::where('user_id', $userId)
            ->where('class_id', $classId)
            ->first();
    }

    public function create(array $data): Enrollment
    {
        return Enrollment::create($data);
    }

    public function delete(Enrollment $enrollment): void
    {
        $enrollment->delete();
    }
}
