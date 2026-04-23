<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmissionStoreRequest;
use App\Http\Resources\SubmissionResource;
use App\Services\MaterialService;
use App\Services\SubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
    public function __construct(
        private readonly SubmissionService $submissions,
        private readonly MaterialService $materials
    ) {}

    public function store(SubmissionStoreRequest $request, int $materialId)
    {
        if (! $request->user()->isStudent()) {
            return response()->json([
                'message' => 'Only students can submit assignments.',
            ], 403);
        }

        $payload = array_merge($request->validated(), [
            'material_id' => $materialId,
        ]);

        try {
            $submission = $this->submissions->create(
                $request->user(),
                $payload,
                $request->file('file')
            );

            return (new SubmissionResource($submission->load('student')))
                ->response()
                ->setStatusCode(201);
        } catch (\Throwable $exception) {
            Log::error('Failed to submit assignment.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to submit assignment.',
            ], 500);
        }
    }

    public function listByMaterial(Request $request, int $materialId)
    {
        if (! $request->user()->isTeacher()) {
            return response()->json([
                'message' => 'Only teachers can view submissions.',
            ], 403);
        }

        try {
            $material = $this->materials->findOrFail($materialId);
            $submissions = $this->submissions->listByMaterial($material->id);

            return SubmissionResource::collection($submissions);
        } catch (\Throwable $exception) {
            Log::error('Failed to list submissions.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to fetch submissions.',
            ], 500);
        }
    }
}
