<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeStoreRequest;
use App\Http\Resources\GradeResource;
use App\Models\Grade;
use App\Services\GradeService;
use App\Services\SubmissionService;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function __construct(
        private readonly GradeService $grades,
        private readonly SubmissionService $submissions
    ) {}

    public function store(GradeStoreRequest $request)
    {
        $submission = $this->submissions->findOrFail($request->validated()['submission_id']);
        $this->authorize('createForSubmission', [Grade::class, $submission]);

        $grade = $this->grades->create($request->user(), $request->validated());

        return (new GradeResource($grade->load('grader')))
            ->response()
            ->setStatusCode(201);
    }

    public function getMyGrades(Request $request)
    {
        if (! $request->user()->isStudent()) {
            abort(403, 'Only students can view their grades.');
        }

        $grades = $this->grades->listMyGrades($request->user());

        return GradeResource::collection($grades);
    }
}
