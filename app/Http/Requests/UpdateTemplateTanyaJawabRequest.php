<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTemplateTanyaJawabRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin_users')->check();
    }

    public function rules(): array
    {
        return [
            'pertanyaan' => ['required', 'string'],
            'jawaban' => ['required', 'string'],
        ];
    }

     public function messages(): array
    {
         return [
            'pertanyaan.required' => 'Kolom pertanyaan wajib diisi.',
            'jawaban.required' => 'Kolom jawaban wajib diisi.',
        ];
    }
}