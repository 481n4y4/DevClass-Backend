<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'file_path' => $this->file_path,
            'kelas_target' => $this->kelas_target,
            'kelas_index_target' => $this->kelas_index_target,
            'deadline' => $this->deadline,
            'submission_required' => $this->submission_required,
            'created_by' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at,
        ];
    }
}
