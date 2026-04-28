<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'nis' => ['sometimes', 'string', 'max:255', Rule::unique('users', 'nis')->ignore($userId)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'name' => ['sometimes', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'no_absen' => ['nullable', 'integer', 'min:1'],
            'kelas' => ['nullable', Rule::in(['10', '11', '12', '13'])],
            'kelas_index' => ['nullable', Rule::in(['1', '2', '3'])],
            'role' => ['sometimes', Rule::in(['admin', 'teacher', 'student'])],
        ];
    }
}
