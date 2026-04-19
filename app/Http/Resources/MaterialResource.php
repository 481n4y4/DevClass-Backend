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
            'class_id' => $this->class_id,
            'title' => $this->title,
            'description' => $this->description,
            'uploaded_by' => new UserResource($this->whenLoaded('uploader')),
            'file_url' => url('/api/files/material/' . $this->id),
            'created_at' => $this->created_at,
        ];
    }
}
