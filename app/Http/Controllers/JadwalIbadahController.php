<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use App\Models\JadwalIbadah;

class JadwalIbadahController extends Controller
{
    public function index()
    {
        $data = JadwalIbadah::orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($data) {
                return view('dashboard.jadwal_ibadah.tombol-aksi')->with('data', $data);
            })
            ->editColumn('created_at', function ($jadwalIbadah) {
                return $jadwalIbadah->created_at->isoFormat('dddd, D MMMM YYYY, HH.mm');
            })
            ->editColumn('updated_at', function ($jadwalIbadah) {
                return $jadwalIbadah->updated_at->isoFormat('dddd, D MMMM YYYY, HH.mm');
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'keterangan' => 'required',
            'hari' => 'nullable',
            'jam' => 'required',
            'kategori' => 'required|in:Ibadah Minggu,Ibadah Pelkat',
        ], [
            'keterangan.required' => 'Keterangan ibadah wajib diisi.',
            'jam.required' => 'Jam ibadah wajid diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
        ]);

        try {
            // Simpan data ke dalam database
            JadwalIbadah::create([
                'keterangan' => $validatedData['keterangan'],
                'hari' => $request->has('hari') ? $validatedData['hari'] : null,
                'jam' => $validatedData['jam'],
                'kategori' => $validatedData['kategori'],
            ]);

            // Berhasil menyimpan
            return response()->json(['message' => 'Jadwal Ibadah berhasil ditambahkan'], 200);
        } catch (\Exception $e) {
            // Tangkap dan log error jika terjadi
            Log::error($e->getMessage());
            return response()->json(['errors' => 'Terjadi kesalahan saat menyimpan data'], 422);
        }
    }

    public function edit(int $id)
    {
        $data = JadwalIbadah::findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        // Find the existing record
        $data = JadwalIbadah::findOrFail($id);

        $validatedData = $request->validate([
            'keterangan' => 'required',
            'hari' => 'nullable',
            'jam' => 'required',
            'kategori' => 'required|in:Ibadah Minggu,Ibadah Pelkat',
        ], [
            'keterangan.required' => 'Keterangan ibadah wajib diisi.',
            'jam.required' => 'Jam ibadah wajid diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
        ]);

        // Update the record
        $data->keterangan = $validatedData['keterangan'];
        $data->hari = $request->has('hari') ? $validatedData['hari'] : null;
        $data->jam = $validatedData['jam'];
        $data->kategori = $validatedData['kategori'];

        // Save changes
        $data->save();

        return response()->json(['message' => 'Jadwal Ibadah berhasil diupdate'], 200);
    }

    public function destroy($id)
    {
        try {
            $renungan = JadwalIbadah::findOrFail($id);
            $renungan->delete();

            return response()->json(['message' => 'Jadwal Ibadah berhasil dihapus'], 200);
        } catch (\Exception $e) {
            // Handle error
            Log::error($e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data'], 500);
        }
    }
}
