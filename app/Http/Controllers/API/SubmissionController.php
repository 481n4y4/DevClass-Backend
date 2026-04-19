<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmissionStoreRequest;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use App\Services\AssignmentService;
use App\Services\SubmissionService;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function __construct(
        private readonly SubmissionService $submissions,
        private readonly AssignmentService $assignments
    ) {}

    public function store(SubmissionStoreRequest $request)
    {
        $assignment = $this->assignments->findOrFail($request->validated()['assignment_id']);
        $this->authorize('createForAssignment', [Submission::class, $assignment]);

        $submission = $this->submissions->create(
            $request->user(),
            $request->validated(),
            $request->file('file')
        );

        return (new SubmissionResource($submission->load(['student', 'grade'])))
            ->response()
            ->setStatusCode(201);
    }

    public function getByAssignment(int $assignmentId)
    {
        $assignment = $this->assignments->findOrFail($assignmentId);
        $this->authorize('viewSubmissions', $assignment);

        $submissions = $this->submissions->listByAssignment($assignmentId);

        return SubmissionResource::collection($submissions);
    }

    public function getMySubmissions(Request $request)
    {
        if (! $request->user()->isStudent()) {
            abort(403, 'Only students can view their submissions.');
        }

        $submissions = $this->submissions->listMySubmissions($request->user());

        return SubmissionResource::collection($submissions);
    }
}
