<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreJadwalIbadahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin_users')->check();
    }

    public function rules(): array
    {
        return [
            'keterangan' => ['required', 'string', 'max:255'],
            'hari' => ['nullable', 'string', 'max:25'],
            'jam' => ['required', 'string', 'max:25'],
            'kategori' => ['required', Rule::in(['Ibadah Minggu', 'Ibadah Pelkat'])],
        ];
    }

    public function messages(): array
    {
        return [
            'keterangan.required' => 'Keterangan ibadah wajib diisi.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 255 karakter.',
            'hari.max' => 'Hari tidak boleh lebih dari 25 karakter.',
            'jam.required' => 'Jam ibadah wajib diisi.',
            'jam.max' => 'Jam tidak boleh lebih dari 25 karakter.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'kategori.in' => 'Kategori yang dipilih tidak valid.',
        ];
    }
}
