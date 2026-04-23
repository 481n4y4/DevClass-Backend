<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nis' => $this->nis,
            'email' => $this->email,
            'name' => $this->name,
            'no_absen' => $this->no_absen,
            'kelas' => $this->kelas,
            'kelas_index' => $this->kelas_index,
            'role' => $this->role,
            'created_at' => $this->created_at,
        ];
    }
}
