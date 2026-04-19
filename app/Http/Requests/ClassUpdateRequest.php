<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'teacher_id' => ['sometimes', 'exists:users,id'],
        ];

        if ($this->user() && ! $this->user()->isAdmin()) {
            unset($rules['teacher_id']);
        }

        return $rules;
    }
}
