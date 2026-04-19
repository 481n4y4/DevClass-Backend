<?php

namespace App\Repositories\Eloquent;

use App\Models\Submission;
use App\Repositories\Contracts\SubmissionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class SubmissionRepository implements SubmissionRepositoryInterface
{
    public function query(): Builder
    {
        return Submission::query();
    }

    public function findById(int $id): ?Submission
    {
        return Submission::find($id);
    }

    public function findByAssignmentAndStudent(int $assignmentId, int $studentId): ?Submission
    {
        return Submission::where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->first();
    }

    public function forAssignment(int $assignmentId): Builder
    {
        return Submission::where('assignment_id', $assignmentId);
    }

    public function forStudent(int $studentId): Builder
    {
        return Submission::where('student_id', $studentId);
    }

    public function create(array $data): Submission
    {
        return Submission::create($data);
    }

    public function update(Submission $submission, array $data): Submission
    {
        $submission->fill($data);
        $submission->save();

        return $submission;
    }
}
