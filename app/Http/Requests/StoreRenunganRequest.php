<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRenunganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin_users')->check();
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'unique:renungans,judul', 'max:255'],
            'alkitab' => ['nullable', 'string', 'max:255'],
            'bacaan_alkitab' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:16384'],
            'isi_bacaan' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul renungan wajib diisi.',
            'judul.unique' => 'Judul renungan sudah ada, silakan gunakan judul lain.',
            'judul.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            'alkitab.max' => 'Alkitab tidak boleh lebih dari 255 karakter.',
            'thumbnail.image' => 'File yang diunggah harus berupa gambar.',
            'thumbnail.mimes' => 'Format gambar yang didukung: jpeg, png, jpg, gif, svg, webp.',
            'thumbnail.max' => 'Ukuran gambar tidak boleh lebih dari 16 MB.',
            'isi_bacaan.required' => 'Isi renungan wajib diisi.',
        ];
    }
}
