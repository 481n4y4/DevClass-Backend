<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;
use App\Models\Material;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmissionService
{
    public function __construct(
        private readonly FileStorageService $storage
    ) {}

    public function create(User $user, array $data, UploadedFile $file): Submission
    {
        $material = Material::find($data['material_id']);
        if (! $material) {
            throw new ModelNotFoundException();
        }

        if ($material->submission_required === false) {
            throw ValidationException::withMessages([
                'material_id' => 'Submission is not required for this material.',
            ]);
        }

        if ($material->deadline && Carbon::now()->greaterThan($material->deadline)) {
            throw ValidationException::withMessages([
                'material_id' => 'Submission deadline has passed.',
            ]);
        }

        if ($user->kelas !== $material->kelas_target || $user->kelas_index !== $material->kelas_index_target) {
            throw new AuthorizationException('You are not assigned to this class group.');
        }

        return DB::transaction(function () use ($user, $material, $file): Submission {
            $path = $this->storage->storeSubmission($file, $material->id, $user->id);
            $payload = [
                'material_id' => $material->id,
                'student_id' => $user->id,
                'file_path' => $path,
                'submitted_at' => Carbon::now(),
            ];

            $existing = Submission::where('material_id', $material->id)
                ->where('student_id', $user->id)
                ->first();

            if ($existing) {
                $existing->update($payload);

                return $existing->refresh();
            }

            return Submission::create($payload);
        });
    }

    public function listByMaterial(int $materialId)
    {
        return Submission::where('material_id', $materialId)
            ->with(['student', 'grade.gradedBy'])
            ->paginate((int) config('devclass.pagination.per_page'));
    }

    public function findMySubmission(User $user, int $materialId): ?Submission
    {
        return Submission::where('material_id', $materialId)
            ->where('student_id', $user->id)
            ->with(['student', 'grade.gradedBy'])
            ->first();
    }

    public function findOrFail(int $id): Submission
    {
        $submission = Submission::find($id);
        if (! $submission) {
            throw new ModelNotFoundException();
        }

        return $submission;
    }
}
