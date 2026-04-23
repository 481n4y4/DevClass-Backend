<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmissionStoreRequest extends FormRequest
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
            'file' => ['required', 'file', 'max:' . $max, 'mimes:' . $mimes],
        ];
    }
}
