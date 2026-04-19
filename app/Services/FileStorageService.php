<?php

namespace App\Services;

use App\Jobs\StoreSftpFileJob;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class FileStorageService
{
    private string $disk;
    private string $materialsDir;
    private string $submissionsDir;
    private bool $useQueue;

    public function __construct()
    {
        $this->disk = (string) config('devclass.files.disk');
        $this->materialsDir = trim((string) config('devclass.files.materials_dir'), '/');
        $this->submissionsDir = trim((string) config('devclass.files.submissions_dir'), '/');
        $this->useQueue = (bool) config('devclass.files.use_queue');
    }

    public function storeMaterial(UploadedFile $file, int $classId): string
    {
        return $this->store($file, $this->materialsDir, $classId);
    }

    public function storeSubmission(UploadedFile $file, int $assignmentId): string
    {
        return $this->store($file, $this->submissionsDir, $assignmentId);
    }

    public function streamDownload(string $path)
    {
        $disk = Storage::disk($this->disk);

        if (! $disk->exists($path)) {
            throw new ModelNotFoundException();
        }

        $stream = $disk->readStream($path);
        if (! is_resource($stream)) {
            throw new RuntimeException('Unable to read file stream.');
        }

        $filename = basename($path);

        return response()->streamDownload(function () use ($stream): void {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, $filename);
    }

    private function store(UploadedFile $file, string $baseDir, int $resourceId): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = (string) Str::uuid();

        if ($extension !== '') {
            $filename .= '.' . $extension;
        }

        $path = $baseDir . '/' . $resourceId . '/' . $filename;

        if ($this->useQueue) {
            StoreSftpFileJob::dispatch($this->disk, $path, $file->get());

            return $path;
        }

        $stream = fopen($file->getRealPath(), 'r');
        Storage::disk($this->disk)->put($path, $stream);

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $path;
    }
}
