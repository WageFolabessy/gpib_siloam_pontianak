<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWartaJemaatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'tanggal_terbit' => 'required|date',
            'file_pdf' => 'nullable|file|mimes:pdf|max:20480',
            'is_published' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul warta tidak boleh kosong.',
            'tanggal_terbit.required' => 'Tanggal terbit tidak boleh kosong.',
            'file_pdf.mimes' => 'File harus berformat PDF.',
            'file_pdf.max' => 'Ukuran file PDF maksimal 20MB.',
        ];
    }
}
