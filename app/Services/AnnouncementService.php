<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\User;
use App\Repositories\Contracts\AnnouncementRepositoryInterface;
use App\Repositories\Contracts\ClassroomRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AnnouncementService
{
    public function __construct(
        private readonly AnnouncementRepositoryInterface $announcements,
        private readonly ClassroomRepositoryInterface $classrooms
    ) {}

    public function listByClass(int $classId)
    {
        return $this->announcements
            ->forClass($classId)
            ->paginate((int) config('devclass.pagination.per_page'));
    }

    public function findOrFail(int $id): Announcement
    {
        $announcement = $this->announcements->findById($id);
        if (! $announcement) {
            throw new ModelNotFoundException();
        }

        return $announcement;
    }

    public function create(User $user, array $data): Announcement
    {
        $classroom = $this->classrooms->findById($data['class_id']);
        if (! $classroom) {
            throw new ModelNotFoundException();
        }

        return $this->announcements->create([
            'class_id' => $classroom->id,
            'title' => $data['title'],
            'content' => $data['content'],
        ]);
    }
}
