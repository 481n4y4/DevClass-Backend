<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\User;
use App\Repositories\Contracts\ClassroomRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClassroomService
{
    public function __construct(
        private readonly ClassroomRepositoryInterface $classrooms
    ) {}

    public function listForUser(User $user, ?string $search = null)
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

    public function findOrFail(int $id): Classroom
    {
        $classroom = $this->classrooms->findById($id);

        if (! $classroom) {
            throw new ModelNotFoundException();
        }

        return $classroom;
    }

    public function create(User $user, array $data): Classroom
    {
        if ($user->isTeacher()) {
            $data['teacher_id'] = $user->id;
        }

        return $this->classrooms->create($data);
    }

    public function update(Classroom $classroom, array $data): Classroom
    {
        return $this->classrooms->update($classroom, $data);
    }

    public function delete(Classroom $classroom): void
    {
        $this->classrooms->delete($classroom);
    }
}
