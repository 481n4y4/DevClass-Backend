<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\User;
use App\Repositories\Contracts\AssignmentRepositoryInterface;
use App\Repositories\Contracts\ClassroomRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AssignmentService
{
    public function __construct(
        private readonly AssignmentRepositoryInterface $assignments,
        private readonly ClassroomRepositoryInterface $classrooms
    ) {}

    public function listByClass(int $classId)
    {
        return $this->assignments
            ->forClass($classId)
            ->paginate((int) config('devclass.pagination.per_page'));
    }

    public function findOrFail(int $id): Assignment
    {
        $assignment = $this->assignments->findById($id);
        if (! $assignment) {
            throw new ModelNotFoundException();
        }

        return $assignment;
    }

    public function create(User $user, array $data): Assignment
    {
        $classroom = $this->classrooms->findById($data['class_id']);
        if (! $classroom) {
            throw new ModelNotFoundException();
        }

        return $this->assignments->create([
            'class_id' => $classroom->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'deadline' => $data['deadline'],
        ]);
    }
}
