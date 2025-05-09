<?php

namespace App\Http\Controllers;

use App\Models\TataIbadah;
use App\Http\Requests\StoreTataIbadahRequest;
use App\Http\Requests\UpdateTataIbadahRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\View\View;

class TataIbadahController extends Controller
{
    protected string $storagePath = 'tata_ibadah_pdfs';

    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $query = TataIbadah::query()
                ->select(['id', 'judul', 'tanggal_terbit', 'file_pdf_path', 'is_published', 'created_at', 'updated_at'])
                ->orderBy('tanggal_terbit', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function (TataIbadah $tataIbadah) {
                    return view('dashboard.tata_ibadah.tombol-aksi', compact('tataIbadah'));
                })
                ->editColumn('tanggal_terbit', function (TataIbadah $tataIbadah) {
                    return $tataIbadah->tanggal_terbit?->isoFormat('dddd, D MMMM YYYY');
                })
                ->addColumn('status_publish', function (TataIbadah $tataIbadah) {
                    return $tataIbadah->is_published ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-warning">Draft</span>';
                })
                ->addColumn('file_info', function (TataIbadah $tataIbadah) {
                    if ($tataIbadah->file_pdf_path && Storage::disk('public')->exists($tataIbadah->file_pdf_path)) {
                        $url = Storage::url($tataIbadah->file_pdf_path);
                        $fileName = basename($tataIbadah->file_pdf_path);
                        return '<a href="' . $url . '" target="_blank" title="' . $fileName . '"><i class="fas fa-file-pdf text-danger"></i> ' . Str::limit($fileName, 25) . '</a>';
                    }
                    return '<span class="text-muted">Tidak ada file</span>';
                })
                ->editColumn('created_at', function (TataIbadah $tataIbadah) {
                    return $tataIbadah->created_at?->isoFormat('D MMM YYYY, HH:mm');
                })
                ->editColumn('updated_at', function (TataIbadah $tataIbadah) {
                    return $tataIbadah->updated_at?->isoFormat('D MMM YYYY, HH:mm');
                })
                ->rawColumns(['aksi', 'status_publish', 'file_info'])
                ->make(true);
        }
        return view('dashboard.tata_ibadah.index');
    }

    public function edit(TataIbadah $tataIbadah): JsonResponse
    {
        if ($tataIbadah->file_pdf_path && Storage::disk('public')->exists($tataIbadah->file_pdf_path)) {
            $tataIbadah->current_file_name = basename($tataIbadah->file_pdf_path);
            $tataIbadah->current_file_url = Storage::url($tataIbadah->file_pdf_path);
        }
        return response()->json(['data' => $tataIbadah]);
    }

    public function store(StoreTataIbadahRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $filePath = null;

        if ($request->hasFile('file_pdf')) {
            $filePath = $request->file('file_pdf')->store($this->storagePath, 'public');
            if (!$filePath) {
                return response()->json(['message' => 'Gagal mengunggah file PDF Tata Ibadah.'], 500);
            }
        }

        try {
            $tataIbadahData = $validatedData;
            $tataIbadahData['file_pdf_path'] = $filePath;
            $tataIbadahData['is_published'] = $request->boolean('is_published');

            TataIbadah::create($tataIbadahData);

            return response()->json(['message' => 'Tata Ibadah berhasil ditambahkan.'], 201);
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan Tata Ibadah: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            return response()->json(['message' => 'Terjadi kesalahan internal saat menyimpan Tata Ibadah. ' . $e->getMessage()], 500);
        }
    }

    public function update(UpdateTataIbadahRequest $request, TataIbadah $tataIbadah): JsonResponse
    {
        $validatedData = $request->validated();
        $currentFilePath = $tataIbadah->file_pdf_path;

        if ($request->hasFile('file_pdf')) {
            $newFilePath = $request->file('file_pdf')->store($this->storagePath, 'public');
            if (!$newFilePath) {
                return response()->json(['message' => 'Gagal mengunggah file PDF Tata Ibadah baru.'], 500);
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

            $tataIbadah->update($updateData);

            return response()->json(['message' => 'Tata Ibadah berhasil diperbarui.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui Tata Ibadah ID {$tataIbadah->id}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan internal saat memperbarui Tata Ibadah. ' . $e->getMessage()], 500);
        }
    }

    public function destroy(TataIbadah $tataIbadah): JsonResponse
    {
        try {
            $filePath = $tataIbadah->file_pdf_path;
            $tataIbadah->delete();

            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return response()->json(['message' => 'Tata Ibadah berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus Tata Ibadah ID {$tataIbadah->id}: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat menghapus Tata Ibadah.'], 500);
        }
    }
}
