<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\FileStorageService;
use App\Services\MaterialService;
use App\Services\SubmissionService;

class FileController extends Controller
{
    public function __construct(
        private readonly FileStorageService $storage,
        private readonly MaterialService $materials,
        private readonly SubmissionService $submissions
    ) {}

    public function material(int $id)
    {
        $material = $this->materials->findOrFail($id);
        $this->authorize('view', $material);

        return $this->storage->streamDownload($material->file_path);
    }

    public function submission(int $id)
    {
        $submission = $this->submissions->findOrFail($id);
        $this->authorize('view', $submission);

        return $this->storage->streamDownload($submission->file_path);
    }
}
