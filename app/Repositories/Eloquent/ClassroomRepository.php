<?php

namespace App\Repositories\Eloquent;

use App\Models\Classroom;
use App\Repositories\Contracts\ClassroomRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class ClassroomRepository implements ClassroomRepositoryInterface
{
    public function query(): Builder
    {
        return Classroom::query();
    }

    public function findById(int $id): ?Classroom
    {
        return Classroom::find($id);
    }

    public function findByCode(string $code): ?Classroom
    {
        return Classroom::where('code', $code)->first();
    }

    public function create(array $data): Classroom
    {
        return Classroom::create($data);
    }

    public function update(Classroom $classroom, array $data): Classroom
    {
        $classroom->fill($data);
        $classroom->save();

        return $classroom;
    }

    public function delete(Classroom $classroom): void
    {
        $classroom->delete();
    }
}
