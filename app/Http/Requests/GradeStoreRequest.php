<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'submission_id' => ['required', 'exists:submissions,id'],
            'score' => ['required', 'integer', 'between:0,100'],
            'feedback' => ['nullable', 'string'],
        ];
    }
}
