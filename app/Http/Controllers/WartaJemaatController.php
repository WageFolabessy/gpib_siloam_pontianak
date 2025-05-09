<?php

namespace App\Http\Controllers;

use App\Models\WartaJemaat;
use App\Http\Requests\StoreWartaJemaatRequest;
use App\Http\Requests\UpdateWartaJemaatRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\View\View;
use Illuminate\Support\Str;


class WartaJemaatController extends Controller
{
    protected string $storagePath = 'warta_pdfs';

    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $query = WartaJemaat::query()
                ->select(['id', 'judul', 'tanggal_terbit', 'file_pdf_path', 'is_published', 'created_at', 'updated_at'])
                ->orderBy('tanggal_terbit', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function (WartaJemaat $warta) {
                    return view('dashboard.warta_jemaat.tombol-aksi', compact('warta'));
                })
                ->editColumn('tanggal_terbit', function (WartaJemaat $warta) {
                    return $warta->tanggal_terbit?->isoFormat('dddd, D MMMM YYYY');
                })
                ->addColumn('status_publish', function (WartaJemaat $warta) {
                    return $warta->is_published ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-warning">Draft</span>';
                })
                ->addColumn('file_info', function (WartaJemaat $warta) {
                    if ($warta->file_pdf_path && Storage::disk('public')->exists($warta->file_pdf_path)) {
                        $url = Storage::url($warta->file_pdf_path);
                        $fileName = basename($warta->file_pdf_path);
                        return '<a href="' . $url . '" target="_blank" title="' . $fileName . '"><i class="fas fa-file-pdf text-danger"></i> ' . Str::limit($fileName, 25) . '</a>';
                    }
                    return '<span class="text-muted">Tidak ada file</span>';
                })
                ->editColumn('created_at', function (WartaJemaat $warta) {
                    return $warta->created_at?->isoFormat('D MMM YYYY, HH:mm');
                })
                ->editColumn('updated_at', function (WartaJemaat $warta) {
                    return $warta->updated_at?->isoFormat('D MMM YYYY, HH:mm');
                })
                ->rawColumns(['aksi', 'status_publish', 'file_info'])
                ->make(true);
        }
        return view('dashboard.warta_jemaat.index');
    }

    public function edit(WartaJemaat $wartaJemaat): JsonResponse
    {
        if ($wartaJemaat->file_pdf_path && Storage::disk('public')->exists($wartaJemaat->file_pdf_path)) {
            $wartaJemaat->current_file_name = basename($wartaJemaat->file_pdf_path);
            $wartaJemaat->current_file_url = Storage::url($wartaJemaat->file_pdf_path);
        }
        return response()->json(['data' => $wartaJemaat]);
    }

    public function store(StoreWartaJemaatRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $filePath = null;

        if ($request->hasFile('file_pdf')) {
            $filePath = $request->file('file_pdf')->store($this->storagePath, 'public');
            if (!$filePath) {
                return response()->json(['message' => 'Gagal mengunggah file PDF.'], 500);
            }
        }

        try {
            $wartaData = $validatedData;
            $wartaData['file_pdf_path'] = $filePath;
            $wartaData['is_published'] = $request->boolean('is_published');

            WartaJemaat::create($wartaData);

            return response()->json(['message' => 'Warta Jemaat berhasil ditambahkan.'], 201);
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan Warta Jemaat: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            return response()->json(['message' => 'Terjadi kesalahan internal saat menyimpan. ' . $e->getMessage()], 500);
        }
    }

    public function update(UpdateWartaJemaatRequest $request, WartaJemaat $wartaJemaat): JsonResponse
    {
        $validatedData = $request->validated();
        $currentFilePath = $wartaJemaat->file_pdf_path;

        if ($request->hasFile('file_pdf')) {
            $newFilePath = $request->file('file_pdf')->store($this->storagePath, 'public');
            if (!$newFilePath) {
                return response()->json(['message' => 'Gagal mengunggah file PDF baru.'], 500);
            }
            if ($currentFilePath && Storage::disk('public')->exists($currentFilePath)) {
                Storage::disk('public')->delete($currentFilePath);
            }
            $validatedData['file_pdf_path'] = $newFilePath;
        } else {
            $validatedData['file_pdf_path'] = $currentFilePath;
        }

        try {
            $updateData = $validatedData;
            if ($request->has('is_published')) {
                $updateData['is_published'] = $request->boolean('is_published');
            } else {
                $updateData['is_published'] = false;
            }


            $wartaJemaat->update($updateData);

            return response()->json(['message' => 'Warta Jemaat berhasil diperbarui.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui Warta Jemaat ID {$wartaJemaat->id}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan internal saat memperbarui. ' . $e->getMessage()], 500);
        }
    }

    public function destroy(WartaJemaat $wartaJemaat): JsonResponse
    {
        try {
            $filePath = $wartaJemaat->file_pdf_path;
            $wartaJemaat->delete();

            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return response()->json(['message' => 'Warta Jemaat berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus Warta Jemaat ID {$wartaJemaat->id}: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat menghapus.'], 500);
        }
    }
}
