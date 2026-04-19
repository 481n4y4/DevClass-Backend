<?php

namespace App\Repositories\Eloquent;

use App\Models\Assignment;
use App\Repositories\Contracts\AssignmentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class AssignmentRepository implements AssignmentRepositoryInterface
{
    public function query(): Builder
    {
        return Assignment::query();
    }

    public function findById(int $id): ?Assignment
    {
        return Assignment::find($id);
    }

    public function forClass(int $classId): Builder
    {
        return Assignment::where('class_id', $classId);
    }

    public function create(array $data): Assignment
    {
        return Assignment::create($data);
    }
}
