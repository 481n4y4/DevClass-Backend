<?php

namespace App\Repositories\Eloquent;

use App\Models\Material;
use App\Repositories\Contracts\MaterialRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class MaterialRepository implements MaterialRepositoryInterface
{
    public function query(): Builder
    {
        return Material::query();
    }

    public function findById(int $id): ?Material
    {
        return Material::find($id);
    }

    public function forClass(int $classId): Builder
    {
        return Material::where('class_id', $classId);
    }

    public function create(array $data): Material
    {
        return Material::create($data);
    }
}
