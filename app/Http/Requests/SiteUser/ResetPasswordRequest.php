<?php

namespace App\Http\Requests\SiteUser;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array {
        return [
            'token' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
            'password' => [
                'required', 'string', 'confirmed', Password::min(8)
            ],
        ];
    }

     public function messages(): array {
         return [
             'token.required' => 'Token reset password tidak valid.',
             'email.required' => 'Alamat email wajib diisi.',
             'email.email' => 'Format alamat email tidak valid.',
             'email.exists' => 'Alamat email ini tidak terdaftar.',
             'password.required' => 'Password baru wajib diisi.',
             'password.confirmed' => 'Konfirmasi password tidak cocok.',
             'password.min' => 'Password minimal harus 8 karakter.',
         ];
     }
}
