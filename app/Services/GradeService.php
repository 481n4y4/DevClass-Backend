<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\User;
use App\Repositories\Contracts\GradeRepositoryInterface;
use App\Repositories\Contracts\SubmissionRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GradeService
{
    public function __construct(
        private readonly GradeRepositoryInterface $grades,
        private readonly SubmissionRepositoryInterface $submissions
    ) {}

    public function create(User $user, array $data): Grade
    {
        $submission = $this->submissions->findById($data['submission_id']);
        if (! $submission) {
            throw new ModelNotFoundException();
        }

        return $this->grades->updateOrCreateForSubmission($submission->id, [
            'score' => $data['score'],
            'feedback' => $data['feedback'] ?? null,
            'graded_by' => $user->id,
        ]);
    }

    public function listMyGrades(User $user)
    {
        return $this->grades
            ->forStudent($user->id)
            ->with('submission.assignment.classroom')
            ->paginate((int) config('devclass.pagination.per_page'));
    }

    public function findOrFail(int $id): Grade
    {
        $grade = $this->grades->findById($id);
        if (! $grade) {
            throw new ModelNotFoundException();
        }

        return $grade;
    }
}
