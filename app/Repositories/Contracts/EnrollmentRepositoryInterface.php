<?php

namespace App\Repositories\Contracts;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Builder;

interface EnrollmentRepositoryInterface
{
    public function query(): Builder;

    public function findByUserAndClass(int $userId, int $classId): ?Enrollment;

    public function create(array $data): Enrollment;

    public function delete(Enrollment $enrollment): void;
}
