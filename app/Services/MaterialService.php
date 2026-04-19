<?php

namespace App\Services;

use App\Models\Material;
use App\Models\User;
use App\Repositories\Contracts\ClassroomRepositoryInterface;
use App\Repositories\Contracts\MaterialRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;

class MaterialService
{
    public function __construct(
        private readonly MaterialRepositoryInterface $materials,
        private readonly ClassroomRepositoryInterface $classrooms,
        private readonly FileStorageService $storage
    ) {}

    public function listByClass(int $classId)
    {
        return $this->materials
            ->forClass($classId)
            ->with('uploader')
            ->paginate((int) config('devclass.pagination.per_page'));
    }

    public function findOrFail(int $id): Material
    {
        $material = $this->materials->findById($id);
        if (! $material) {
            throw new ModelNotFoundException();
        }

        return $material;
    }

    public function create(User $user, array $data, UploadedFile $file): Material
    {
        $classroom = $this->classrooms->findById($data['class_id']);
        if (! $classroom) {
            throw new ModelNotFoundException();
        }

        $path = $this->storage->storeMaterial($file, $classroom->id);

        return $this->materials->create([
            'class_id' => $classroom->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'uploaded_by' => $user->id,
        ]);
    }
}
