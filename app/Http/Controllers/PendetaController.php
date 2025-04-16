<?php

namespace App\Http\Controllers;

use App\Models\Pendeta;
use App\Http\Requests\StorePendetaRequest;
use App\Http\Requests\UpdatePendetaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\View\View;

class PendetaController extends Controller
{
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $query = Pendeta::query()->select(['id', 'nama', 'kategori', 'created_at', 'updated_at'])
                ->orderBy('kategori', 'asc')
                ->orderBy('nama', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function (Pendeta $pendeta) {
                    return view('dashboard.pendeta.tombol-aksi', compact('pendeta'));
                })
                ->editColumn('created_at', function (Pendeta $pendeta) {
                    return $pendeta->created_at?->isoFormat('D MMM YYYY, HH:mm');
                })
                ->editColumn('updated_at', function (Pendeta $pendeta) {
                    return $pendeta->updated_at?->diffForHumans();
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('dashboard.pendeta.index');
    }

    public function edit(Pendeta $pendeta): JsonResponse
    {
        return response()->json(['data' => $pendeta]);
    }

    public function store(StorePendetaRequest $request): JsonResponse
    {
        $validatedData = $request->validated(); // Validasi via Form Request

        try {
            Pendeta::create($validatedData);
            return response()->json(['message' => 'Data Pendeta/Majelis berhasil ditambahkan.'], 201);
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan Data Pendeta/Majelis: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat menyimpan.'], 500);
        }
    }

    public function update(UpdatePendetaRequest $request, Pendeta $pendeta): JsonResponse // Route Model Binding
    {
        $validatedData = $request->validated(); // Validasi via Form Request

        try {
            $pendeta->update($validatedData);
            return response()->json(['message' => 'Data Pendeta/Majelis berhasil diperbarui.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui Data Pendeta/Majelis ID {$pendeta->id}: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat memperbarui.'], 500);
        }
    }

    public function destroy(Pendeta $pendeta): JsonResponse
    {
        try {
            $pendeta->delete();

            return response()->json(['message' => 'Data Pendeta/Majelis berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus Data Pendeta/Majelis ID {$pendeta->id}: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat menghapus.'], 500);
        }
    }
}
