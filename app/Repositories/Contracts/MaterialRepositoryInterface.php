<?php

namespace App\Repositories\Contracts;

use App\Models\Material;
use Illuminate\Database\Eloquent\Builder;

interface MaterialRepositoryInterface
{
    public function query(): Builder;

    public function findById(int $id): ?Material;

    public function forClass(int $classId): Builder;

    public function create(array $data): Material;
}
