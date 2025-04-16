<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateRenunganRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Izinkan jika admin sudah login
        // Anda bisa menambahkan logic otorisasi yang lebih spesifik di sini
        // Misalnya, memeriksa apakah admin ini boleh mengedit renungan spesifik
        return Auth::guard('admin_users')->check();
    }

    public function rules(): array
    {
        // Dapatkan ID renungan dari route model binding
        $renunganId = $this->route('renungan')->id;

        return [
            'judul' => [
                'required',
                'string',
                Rule::unique('renungans', 'judul')->ignore($renunganId), // Abaikan ID saat ini
                'max:255'
            ],
            'alkitab' => ['nullable', 'string', 'max:255'],
            'bacaan_alkitab' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:16384'], // Max 16MB, nullable karena tidak wajib ganti
            'isi_bacaan' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        // Gunakan pesan yang sama atau sesuaikan jika perlu
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
