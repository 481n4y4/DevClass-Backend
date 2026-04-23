<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $max = (int) config('devclass.files.max_upload_kb');
        $mimes = implode(',', config('devclass.files.allowed_mimes'));

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'kelas_target' => ['sometimes', 'in:10,11,12,13'],
            'kelas_index_target' => ['sometimes', 'in:1,2,3'],
            'deadline' => ['nullable', 'date'],
            'submission_required' => ['nullable', 'boolean'],
            'file' => ['nullable', 'file', 'max:' . $max, 'mimes:' . $mimes],
        ];
    }
}
