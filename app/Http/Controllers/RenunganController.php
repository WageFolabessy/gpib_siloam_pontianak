<?php

namespace App\Http\Controllers;

use App\Models\Renungan;
use App\Http\Requests\StoreRenunganRequest;
use App\Http\Requests\UpdateRenunganRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\View\View;

class RenunganController extends Controller
{
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $query = Renungan::query()->select(['id', 'judul', 'alkitab', 'bacaan_alkitab', 'created_at', 'updated_at'])
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function (Renungan $renungan) {
                    return view('dashboard.renungan.tombol-aksi', compact('renungan'));
                })
                ->editColumn('created_at', function (Renungan $renungan) {
                    return $renungan->created_at?->isoFormat('dddd, D MMMM YYYY, HH:mm');
                })
                ->editColumn('updated_at', function (Renungan $renungan) {
                    return $renungan->updated_at?->isoFormat('dddd, D MMMM YYYY, HH:mm');
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('dashboard.renungan.index');
    }

    public function edit(Renungan $renungan): JsonResponse
    {
        return response()->json(['data' => $renungan]);
    }

    public function store(StoreRenunganRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            if (!$thumbnailPath) {
                return response()->json(['message' => 'Gagal mengunggah thumbnail.'], 500);
            }
        }

        try {
            $renunganData = $validatedData;
            $renunganData['thumbnail'] = $thumbnailPath;

            Renungan::create($renunganData);

            return response()->json(['message' => 'Renungan berhasil ditambahkan.'], 201);
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan renungan: " . $e->getMessage());
            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            return response()->json(['message' => 'Terjadi kesalahan internal saat menyimpan.'], 500);
        }
    }

    public function update(UpdateRenunganRequest $request, Renungan $renungan): JsonResponse
    {
        $validatedData = $request->validated();
        $thumbnailPath = $renungan->thumbnail;

        if ($request->hasFile('thumbnail')) {
            $newThumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            if (!$newThumbnailPath) {
                return response()->json(['message' => 'Gagal mengunggah thumbnail baru.'], 500);
            }
            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            $thumbnailPath = $newThumbnailPath;
        }

        try {
            $updateData = $validatedData;
            $updateData['thumbnail'] = $thumbnailPath;

            $renungan->update($updateData);

            return response()->json(['message' => 'Renungan berhasil diperbarui.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui renungan ID {$renungan->id}: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat memperbarui.'], 500);
        }
    }

    public function destroy(Renungan $renungan): JsonResponse
    {
        try {
            $thumbnailPath = $renungan->thumbnail;
            $renungan->delete();

            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            return response()->json(['message' => 'Renungan berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus renungan ID {$renungan->id}: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat menghapus.'], 500);
        }
    }
}
