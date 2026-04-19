<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignmentStoreRequest;
use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Services\AssignmentService;
use App\Services\ClassroomService;

class AssignmentController extends Controller
{
    public function __construct(
        private readonly AssignmentService $assignments,
        private readonly ClassroomService $classrooms
    ) {}

    public function getByClass(int $classId)
    {
        $classroom = $this->classrooms->findOrFail($classId);
        $this->authorize('view', $classroom);

        $assignments = $this->assignments->listByClass($classId);

        return AssignmentResource::collection($assignments);
    }

    public function store(AssignmentStoreRequest $request)
    {
        $classroom = $this->classrooms->findOrFail($request->validated()['class_id']);
        $this->authorize('createForClass', [Assignment::class, $classroom]);

        $assignment = $this->assignments->create($request->user(), $request->validated());

        return (new AssignmentResource($assignment))
            ->response()
            ->setStatusCode(201);
    }
}
