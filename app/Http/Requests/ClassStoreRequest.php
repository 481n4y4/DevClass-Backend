<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'grade' => ['required', 'in:10,11,12,13'],
            'name_class' => ['required', 'string', 'max:255'],
            'teacher_id' => ['nullable', 'exists:users,id'],
        ];

        if ($this->user() && $this->user()->isAdmin()) {
            $rules['teacher_id'] = ['required', 'exists:users,id'];
        }

        return $rules;
    }
}
