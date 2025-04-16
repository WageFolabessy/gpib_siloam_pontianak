<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJadwalIbadahRequest;
use App\Http\Requests\UpdateJadwalIbadahRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use App\Models\JadwalIbadah;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class JadwalIbadahController extends Controller
{
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $query = JadwalIbadah::query()->select(['id', 'keterangan', 'hari', 'jam', 'kategori', 'created_at', 'updated_at'])
                ->orderBy('kategori', 'asc')
                ->orderByRaw("
                    CASE hari
                        WHEN 'Minggu' THEN 1
                        WHEN 'Senin' THEN 2
                        WHEN 'Selasa' THEN 3
                        WHEN 'Rabu' THEN 4
                        WHEN 'Kamis' THEN 5
                        WHEN 'Jumat' THEN 6
                        WHEN 'Sabtu' THEN 7
                        ELSE 8 -- Meletakkan NULL atau nilai lain di akhir (ASC)
                    END ASC
                ")
                ->orderBy('jam', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function (JadwalIbadah $jadwalIbadah) {
                    return view('dashboard.jadwal_ibadah.tombol-aksi', compact('jadwalIbadah'));
                })
                ->editColumn('created_at', function (JadwalIbadah $jadwalIbadah) {
                    return $jadwalIbadah->created_at?->isoFormat('D MMM YY, HH:mm'); // Format YY agar lebih pendek
                })
                ->editColumn('updated_at', function (JadwalIbadah $jadwalIbadah) {
                    return $jadwalIbadah->updated_at?->diffForHumans();
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('dashboard.jadwal_ibadah.index');
    }

    public function edit(JadwalIbadah $jadwalIbadah): JsonResponse
    {
        return response()->json(['data' => $jadwalIbadah]);
    }

    public function store(StoreJadwalIbadahRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        try {
            JadwalIbadah::create($validatedData);
            return response()->json(['message' => 'Jadwal Ibadah berhasil ditambahkan.'], 201);
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan Jadwal Ibadah: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat menyimpan.'], 500);
        }
    }

    public function update(UpdateJadwalIbadahRequest $request, JadwalIbadah $jadwalIbadah): JsonResponse
    {
        $validatedData = $request->validated();
        try {
            $jadwalIbadah->update($validatedData);
            return response()->json(['message' => 'Jadwal Ibadah berhasil diperbarui.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui Jadwal Ibadah ID {$jadwalIbadah->id}: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat memperbarui.'], 500);
        }
    }

    public function destroy(JadwalIbadah $jadwalIbadah): JsonResponse
    {
        try {
            $jadwalIbadah->delete();
            return response()->json(['message' => 'Jadwal Ibadah berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus Jadwal Ibadah ID {$jadwalIbadah->id}: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat menghapus.'], 500);
        }
    }
}
