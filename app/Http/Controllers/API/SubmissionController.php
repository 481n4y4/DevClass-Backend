<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmissionStoreRequest;
use App\Http\Resources\SubmissionResource;
use App\Http\Resources\GradeResource;
use App\Services\MaterialService;
use App\Services\SubmissionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
        } catch (ValidationException $exception) {
            Log::warning('Validation failed during submission.', ['errors' => $exception->errors()]);
            return response()->json([
                'message' => 'Submission validation failed.',
                'errors' => $exception->errors(),
            ], 422);
        } catch (AuthorizationException $exception) {
            Log::warning('Unauthorized submission attempt.', ['error' => $exception->getMessage()]);
            return response()->json([
                'message' => $exception->getMessage() ?: 'Unauthorized.',
            ], 403);
        } catch (ModelNotFoundException $exception) {
            Log::warning('Material not found for submission.', ['error' => $exception->getMessage()]);
            return response()->json([
                'message' => 'Material not found.',
            ], 404);
        } catch (\Throwable $exception) {
            Log::error('Failed to submit assignment.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to submit assignment.',
            ], 500);
        }
    }

    public function mySubmission(Request $request, int $materialId)
    {
        if (! $request->user()->isStudent()) {
            return response()->json([
                'message' => 'Only students can access their submission.',
            ], 403);
        }

        try {
            $submission = $this->submissions->findMySubmission($request->user(), $materialId);

            return response()->json([
                'data' => $submission
                    ? new SubmissionResource($submission)
                    : null,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Failed to fetch my submission.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to fetch submission.',
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

    public function addGrade(Request $request, int $submissionId)
    {
        if (! $request->user()->isTeacher()) {
            return response()->json([
                'message' => 'Only teachers can grade submissions.',
            ], 403);
        }

        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        try {
            $submission = \App\Models\Submission::with('student', 'material', 'grade')
                ->findOrFail($submissionId);

            // Check if teacher owns the material
            if ($submission->material->created_by !== $request->user()->id) {
                return response()->json([
                    'message' => 'Unauthorized. You are not the teacher of this material.',
                ], 403);
            }

            // Update or create grade
            $grade = \App\Models\Grade::updateOrCreate(
                ['submission_id' => $submissionId],
                [
                    'score' => $validated['score'],
                    'feedback' => $validated['feedback'],
                    'graded_by' => $request->user()->id,
                    'graded_at' => now(),
                ]
            );

            return (new GradeResource($grade->load('gradedBy')))
                ->response()
                ->setStatusCode(201);
        } catch (\Throwable $exception) {
            Log::error('Failed to add grade.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to add grade.',
            ], 500);
        }
    }

    public function deleteSubmission(Request $request, int $submissionId)
    {
        try {
            $submission = \App\Models\Submission::with('grade')->findOrFail($submissionId);

            // Check if student owns submission or teacher owns material
            $isStudent = $request->user()->isStudent() && $submission->student_id === $request->user()->id;
            $isMaterialTeacher = $request->user()->isTeacher() &&
                $submission->material->created_by === $request->user()->id;

            if (!$isStudent && !$isMaterialTeacher) {
                return response()->json([
                    'message' => 'Unauthorized.',
                ], 403);
            }

            // Student cannot delete if already graded
            if ($isStudent && $submission->grade) {
                return response()->json([
                    'message' => 'Cannot delete submission that has been graded.',
                ], 422);
            }

            // Delete file from storage
            if ($submission->file_path) {
                try {
                    \Illuminate\Support\Facades\Storage::disk('sftp')->delete($submission->file_path);
                } catch (\Throwable $e) {
                    Log::warning('Failed to delete file from storage', ['file' => $submission->file_path]);
                }
            }

            $submission->delete();

            return response()->json([
                'message' => 'Submission deleted successfully.',
            ]);
        } catch (\Throwable $exception) {
            Log::error('Failed to delete submission.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to delete submission.',
            ], 500);
        }
    }
}
