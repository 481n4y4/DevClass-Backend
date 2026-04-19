<?php

namespace App\Repositories\Eloquent;

use App\Models\Announcement;
use App\Repositories\Contracts\AnnouncementRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    public function query(): Builder
    {
        return Announcement::query();
    }

    public function findById(int $id): ?Announcement
    {
        return Announcement::find($id);
    }

    public function forClass(int $classId): Builder
    {
        return Announcement::where('class_id', $classId);
    }

    public function create(array $data): Announcement
    {
        return Announcement::create($data);
    }
}
