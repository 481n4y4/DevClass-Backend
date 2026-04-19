<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementStoreRequest;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Services\AnnouncementService;
use App\Services\ClassroomService;

class AnnouncementController extends Controller
{
    public function __construct(
        private readonly AnnouncementService $announcements,
        private readonly ClassroomService $classrooms
    ) {}

    public function getByClass(int $classId)
    {
        $classroom = $this->classrooms->findOrFail($classId);
        $this->authorize('view', $classroom);

        $announcements = $this->announcements->listByClass($classId);

        return AnnouncementResource::collection($announcements);
    }

    public function store(AnnouncementStoreRequest $request)
    {
        $classroom = $this->classrooms->findOrFail($request->validated()['class_id']);
        $this->authorize('createForClass', [Announcement::class, $classroom]);

        $announcement = $this->announcements->create($request->user(), $request->validated());

        return (new AnnouncementResource($announcement))
            ->response()
            ->setStatusCode(201);
    }
}
