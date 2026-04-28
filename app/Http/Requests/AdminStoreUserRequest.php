<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminStoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nis' => ['required', 'string', 'max:255', 'unique:users,nis'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'no_absen' => ['nullable', 'integer', 'min:1'],
            'kelas' => ['nullable', Rule::in(['10', '11', '12', '13'])],
            'kelas_index' => ['nullable', Rule::in(['1', '2', '3'])],
            'role' => ['required', Rule::in(['admin', 'teacher', 'student'])],
        ];
    }
}
