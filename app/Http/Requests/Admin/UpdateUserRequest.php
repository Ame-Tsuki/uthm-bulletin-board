<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'uthm_id' => ['sometimes', 'string', Rule::unique('users')->ignore($userId)],
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', Rule::unique('users')->ignore($userId)],
            'phone' => 'nullable|string|max:20',
            'role' => ['sometimes', Rule::in(['student', 'staff', 'admin', 'club_admin'])],
            'faculty' => 'nullable|string|max:255',
            'password' => 'sometimes|string|min:8',
            'is_verified' => 'boolean',
        ];
    }
}

