<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'material_id' => $this->material_id,
            'student' => new UserResource($this->whenLoaded('student')),
            'submitted_at' => $this->submitted_at,
            'file_path' => $this->file_path,
            'grade' => new GradeResource($this->whenLoaded('grade')),
            'created_at' => $this->created_at,
        ];
    }
}
