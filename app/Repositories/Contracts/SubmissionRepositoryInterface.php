<?php

namespace App\Repositories\Contracts;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Builder;

interface SubmissionRepositoryInterface
{
    public function query(): Builder;

    public function findById(int $id): ?Submission;

    public function findByAssignmentAndStudent(int $assignmentId, int $studentId): ?Submission;

    public function forAssignment(int $assignmentId): Builder;

    public function forStudent(int $studentId): Builder;

    public function create(array $data): Submission;

    public function update(Submission $submission, array $data): Submission;
}
