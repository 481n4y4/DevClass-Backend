<?php

namespace App\Services;

use App\Models\Material;
use App\Models\User;
use App\Notifications\MaterialCreatedNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class MaterialService
{
    public function __construct(private readonly FileStorageService $storage) {}

    public function listForUser(User $user)
    {
        $query = Material::query()->with('creator');

        if ($user->isStudent()) {
            $query->where('kelas_target', $user->kelas)
                ->where('kelas_index_target', $user->kelas_index);
        }

        return $query
            ->latest()
            ->paginate((int) config('devclass.pagination.per_page'));
    }

    public function findOrFail(int $id): Material
    {
        $material = Material::find($id);
        if (! $material) {
            throw new ModelNotFoundException();
        }

        return $material;
    }

    public function create(User $user, array $data, ?UploadedFile $file): Material
    {
        return DB::transaction(function () use ($user, $data, $file): Material {
            $material = Material::create([
                'title' => $data['title'],
                'content' => $data['content'] ?? null,
                'file_path' => null,
                'kelas_target' => $data['kelas_target'],
                'kelas_index_target' => $data['kelas_index_target'],
                'deadline' => $data['deadline'] ?? null,
                'submission_required' => $data['submission_required'] ?? false,
                'created_by' => $user->id,
            ]);

            if ($file) {
                $path = $this->storage->storeMaterial($file, $material->id);
                $material->update(['file_path' => $path]);
            }

            DB::afterCommit(function () use ($material): void {
                $this->notifyStudents($material);
            });

            return $material->refresh();
        });
    }

    public function update(Material $material, array $data, ?UploadedFile $file): Material
    {
        return DB::transaction(function () use ($material, $data, $file): Material {
            $material->fill([
                'title' => $data['title'] ?? $material->title,
                'content' => array_key_exists('content', $data) ? $data['content'] : $material->content,
                'kelas_target' => $data['kelas_target'] ?? $material->kelas_target,
                'kelas_index_target' => $data['kelas_index_target'] ?? $material->kelas_index_target,
                'deadline' => array_key_exists('deadline', $data) ? $data['deadline'] : $material->deadline,
                'submission_required' => array_key_exists('submission_required', $data)
                    ? (bool) $data['submission_required']
                    : $material->submission_required,
            ]);

            if ($file) {
                $path = $this->storage->storeMaterial($file, $material->id);
                $material->file_path = $path;
            }

            $material->save();

            return $material->refresh();
        });
    }

    public function delete(Material $material): void
    {
        $material->delete();
    }

    private function notifyStudents(Material $material): void
    {
        $students = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->where('kelas', $material->kelas_target)
            ->where('kelas_index', $material->kelas_index_target)
            ->whereNotNull('email')
            ->get();

        if ($students->isEmpty()) {
            return;
        }

        Notification::send($students, new MaterialCreatedNotification($material));
    }
}
