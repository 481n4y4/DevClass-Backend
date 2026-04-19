<?php

namespace App\Repositories\Eloquent;

use App\Models\Grade;
use App\Repositories\Contracts\GradeRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class GradeRepository implements GradeRepositoryInterface
{
    public function query(): Builder
    {
        return Grade::query();
    }

    public function findById(int $id): ?Grade
    {
        return Grade::find($id);
    }

    public function updateOrCreateForSubmission(int $submissionId, array $data): Grade
    {
        return Grade::updateOrCreate(
            ['submission_id' => $submissionId],
            $data
        );
    }

    public function forStudent(int $studentId): Builder
    {
        return Grade::whereHas('submission', function (Builder $query) use ($studentId) {
            $query->where('student_id', $studentId);
        });
    }
}
