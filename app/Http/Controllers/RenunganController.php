<?php

namespace App\Http\Controllers;

use App\Models\Renungan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RenunganController extends Controller
{
    public function index()
    {
        $data = Renungan::orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($data) {
                return view('dashboard.renungan.tombol-aksi')->with('data', $data);
            })
            ->editColumn('created_at', function ($renungan) {
                return $renungan->created_at->isoFormat('dddd, D MMMM YYYY, HH.mm');
            })
            ->editColumn('updated_at', function ($renungan) {
                return $renungan->updated_at->isoFormat('dddd, D MMMM YYYY, HH.mm');
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'judul' => 'required|string|unique:renungans|max:255',
            'alkitab' => 'nullable',
            'bacaan_alkitab' => 'nullable',
            'thumbnail' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:16384',
            'isi_bacaan' => 'required|string',
        ], [
            'judul.required' => 'Judul renungan wajib diisi.',
            'judul.unique' => 'Judul renungan sudah ada.',
            'thumbnail.image' => 'Thumbnail harus berupa gambar.',
            'thumbnail.max' => 'Ukuran thumbnail tidak boleh lebih dari 16 MB.',
            'isi_bacaan.required' => 'Isi renungan wajib diisi.',
        ]);

        // Proses thumbnail (jika diunggah)
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailFile = $request->file('thumbnail');
            $thumbnailPath = $thumbnailFile->getClientOriginalName(); // Dapatkan nama asli file
            $thumbnailFile->storeAs('thumbnails', $thumbnailPath, 'public');
        }

        try {
            // Simpan data ke dalam database
            Renungan::create([
                'judul' => $validatedData['judul'],
                'alkitab' => $validatedData['alkitab'],
                'bacaan_alkitab' => $validatedData['bacaan_alkitab'],
                'thumbnail' => $thumbnailPath,
                'isi_bacaan' => $validatedData['isi_bacaan'],
                'slug' => Str::slug($validatedData['judul'], '-')
            ]);

            // Berhasil menyimpan
            return response()->json(['message' => 'Renungan berhasil ditambahkan'], 200);
        } catch (\Exception $e) {
            // Tangkap dan log error jika terjadi
            Log::error($e->getMessage());
            return response()->json(['errors' => 'Terjadi kesalahan saat menyimpan data'], 422);
        }
    }

    public function edit(int $id)
    {
        $data = Renungan::findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        // Find the existing record
        $renungan = Renungan::findOrFail($id);

        $validatedData = $request->validate([
            'judul' => 'required|string|unique:renungans,judul,' . $renungan->id . '|max:255',
            'alkitab' => 'nullable',
            'bacaan_alkitab' => 'nullable',
            'thumbnail' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'isi_bacaan' => 'required|string',
        ], [
            'judul.required' => 'Judul renungan wajib diisi.',
            'judul.unique' => 'Judul renungan sudah ada.',
            'thumbnail.image' => 'Thumbnail harus berupa gambar.',
            'isi_bacaan.required' => 'Isi renungan wajib diisi.',
        ]);

        // Update the record
        $renungan->judul = $validatedData['judul'];
        $renungan->alkitab = $validatedData['alkitab'];
        $renungan->bacaan_alkitab = $validatedData['bacaan_alkitab'];
        $renungan->isi_bacaan = $validatedData['isi_bacaan'];
        $renungan->slug = Str::slug($validatedData['judul'], '-');

        // Process thumbnail if provided
        if ($request->hasFile('thumbnail')) {
            $thumbnailFile = $request->file('thumbnail');
            $thumbnailPath = $thumbnailFile->getClientOriginalName();
            $thumbnailFile->storeAs('thumbnails', $thumbnailPath, 'public');
            $renungan->thumbnail = $thumbnailPath;
        }

        // Save changes
        $renungan->save();

        return response()->json(['message' => 'Renungan berhasil diupdate'], 200);
    }

    public function destroy($id)
    {
        try {
            $renungan = Renungan::findOrFail($id);
            $renungan->delete();

            return response()->json(['message' => 'Renungan berhasil dihapus'], 200);
        } catch (\Exception $e) {
            // Handle error
            Log::error($e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data'], 500);
        }
    }
}
