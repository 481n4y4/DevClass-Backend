<?php

namespace App\Repositories\Contracts;

use App\Models\Assignment;
use Illuminate\Database\Eloquent\Builder;

interface AssignmentRepositoryInterface
{
    public function query(): Builder;

    public function findById(int $id): ?Assignment;

    public function forClass(int $classId): Builder;

    public function create(array $data): Assignment;
}
