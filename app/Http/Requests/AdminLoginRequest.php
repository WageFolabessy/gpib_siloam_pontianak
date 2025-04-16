<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AdminLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin_users')->guest();
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Kolom username wajib diisi.',
            'password.required' => 'Kolom password wajib diisi.',
        ];
    }
}
