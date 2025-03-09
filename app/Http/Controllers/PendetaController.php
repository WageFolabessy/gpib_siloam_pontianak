<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use App\Models\Pendeta;

class PendetaController extends Controller
{
    public function index()
    {
        $data = Pendeta::orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($data) {
                return view('dashboard.pendeta.tombol-aksi')->with('data', $data);
            })
            ->editColumn('created_at', function ($pendeta) {
                return $pendeta->created_at->isoFormat('dddd, D MMMM YYYY, HH.mm');
            })
            ->editColumn('updated_at', function ($pendeta) {
                return $pendeta->updated_at->isoFormat('dddd, D MMMM YYYY, HH.mm');
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nama' => 'required',
            'kategori' => 'required|in:Ketua Majelis Jemaat,Pendeta Jemaat',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
        ]);

        try {
            // Simpan data ke dalam database
            Pendeta::create([
                'nama' => $validatedData['nama'],
                'kategori' => $validatedData['kategori'],
            ]);

            // Berhasil menyimpan
            return response()->json(['message' => 'Pengurus berhasil ditambahkan'], 200);
        } catch (\Exception $e) {
            // Tangkap dan log error jika terjadi
            Log::error($e->getMessage());
            return response()->json(['errors' => 'Terjadi kesalahan saat menyimpan data'], 422);
        }
    }

    public function edit(int $id)
    {
        $data = Pendeta::findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        // Find the existing record
        $data = Pendeta::findOrFail($id);

        $validatedData = $request->validate([
            'nama' => 'required',
            'kategori' => 'required|in:Ketua Majelis Jemaat,Pendeta Jemaat',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
        ]);

        // Update the record
        $data->nama = $validatedData['nama'];
        $data->kategori = $validatedData['kategori'];

        // Save changes
        $data->save();

        return response()->json(['message' => 'Pengurus berhasil diupdate'], 200);
    }

    public function destroy($id)
    {
        try {
            $renungan = Pendeta::findOrFail($id);
            $renungan->delete();

            return response()->json(['message' => 'Pengurus berhasil dihapus'], 200);
        } catch (\Exception $e) {
            // Handle error
            Log::error($e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data'], 500);
        }
    }
}
