<?php

namespace App\Repositories\Contracts;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Builder;

interface AnnouncementRepositoryInterface
{
    public function query(): Builder;

    public function findById(int $id): ?Announcement;

    public function forClass(int $classId): Builder;

    public function create(array $data): Announcement;
}
