<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;
use App\Repositories\Contracts\AssignmentRepositoryInterface;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use App\Repositories\Contracts\SubmissionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class SubmissionService
{
    public function __construct(
        private readonly SubmissionRepositoryInterface $submissions,
        private readonly AssignmentRepositoryInterface $assignments,
        private readonly EnrollmentRepositoryInterface $enrollments,
        private readonly FileStorageService $storage
    ) {}

    public function create(User $user, array $data, UploadedFile $file): Submission
    {
        $assignment = $this->assignments->findById($data['assignment_id']);
        if (! $assignment) {
            throw new ModelNotFoundException();
        }

        if (Carbon::now()->greaterThan($assignment->deadline)) {
            throw ValidationException::withMessages([
                'assignment_id' => 'Submission deadline has passed.',
            ]);
        }

        if (! $this->enrollments->findByUserAndClass($user->id, $assignment->class_id)) {
            throw new AuthorizationException('You are not enrolled in this class.');
        }

        $path = $this->storage->storeSubmission($file, $assignment->id);
        $payload = [
            'assignment_id' => $assignment->id,
            'student_id' => $user->id,
            'file_path' => $path,
            'submitted_at' => Carbon::now(),
        ];

        $existing = $this->submissions->findByAssignmentAndStudent($assignment->id, $user->id);
        if ($existing) {
            return $this->submissions->update($existing, $payload);
        }

        return $this->submissions->create($payload);
    }

    public function listByAssignment(int $assignmentId)
    {
        return $this->submissions
            ->forAssignment($assignmentId)
            ->with(['student', 'grade'])
            ->paginate((int) config('devclass.pagination.per_page'));
    }

    public function listMySubmissions(User $user)
    {
        return $this->submissions
            ->forStudent($user->id)
            ->with('assignment.classroom')
            ->paginate((int) config('devclass.pagination.per_page'));
    }

    public function findOrFail(int $id): Submission
    {
        $submission = $this->submissions->findById($id);
        if (! $submission) {
            throw new ModelNotFoundException();
        }

        return $submission;
    }
}
