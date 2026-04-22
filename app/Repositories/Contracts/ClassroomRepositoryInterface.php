<?php

namespace App\Repositories\Contracts;

use App\Models\Classroom;
use Illuminate\Database\Eloquent\Builder;

interface ClassroomRepositoryInterface
{
    public function query(): Builder;

    public function findById(int $id): ?Classroom;

    public function findByCode(string $code): ?Classroom;

    public function create(array $data): Classroom;

    public function update(Classroom $classroom, array $data): Classroom;

    public function delete(Classroom $classroom): void;
}
