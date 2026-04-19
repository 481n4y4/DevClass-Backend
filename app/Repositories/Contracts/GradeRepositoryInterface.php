<?php

namespace App\Repositories\Contracts;

use App\Models\Grade;
use Illuminate\Database\Eloquent\Builder;

interface GradeRepositoryInterface
{
    public function query(): Builder;

    public function findById(int $id): ?Grade;

    public function updateOrCreateForSubmission(int $submissionId, array $data): Grade;

    public function forStudent(int $studentId): Builder;
}
