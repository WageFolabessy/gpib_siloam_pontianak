<?php

namespace App\Http\Controllers;

use App\Models\TemplateTanyaJawab;
use App\Http\Requests\StoreTemplateTanyaJawabRequest;
use App\Http\Requests\UpdateTemplateTanyaJawabRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\View\View;

class TemplateTanyaJawabController extends Controller
{
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $query = TemplateTanyaJawab::query()->select(['id', 'pertanyaan', 'created_at', 'updated_at'])
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function (TemplateTanyaJawab $templateTanyaJawab) {
                    return view('dashboard.tanya_jawab.tombol-aksi', compact('templateTanyaJawab'));
                })
                ->editColumn('created_at', function (TemplateTanyaJawab $templateTanyaJawab) {
                    return $templateTanyaJawab->created_at?->isoFormat('D MMM YY, HH:mm');
                })
                ->editColumn('updated_at', function (TemplateTanyaJawab $templateTanyaJawab) {
                    return $templateTanyaJawab->updated_at?->diffForHumans();
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('dashboard.tanya_jawab.index');
    }

    public function edit(TemplateTanyaJawab $templateTanyaJawab): JsonResponse
    {
        return response()->json(['data' => $templateTanyaJawab]);
    }

    public function show(TemplateTanyaJawab $templateTanyaJawab): JsonResponse
    {
        return response()->json(['data' => $templateTanyaJawab]);
    }

    public function store(StoreTemplateTanyaJawabRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            TemplateTanyaJawab::create($validatedData);
            return response()->json(['message' => 'Template Tanya Jawab berhasil ditambahkan.'], 201);
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan Template Tanya Jawab: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat menyimpan.'], 500);
        }
    }

    public function update(UpdateTemplateTanyaJawabRequest $request, TemplateTanyaJawab $templateTanyaJawab): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $templateTanyaJawab->update($validatedData);
            return response()->json(['message' => 'Template Tanya Jawab berhasil diperbarui.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui Template Tanya Jawab ID {$templateTanyaJawab->id}: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat memperbarui.'], 500);
        }
    }

    public function destroy(TemplateTanyaJawab $templateTanyaJawab): JsonResponse
    {
        try {
            $templateTanyaJawab->delete();
            return response()->json(['message' => 'Template Tanya Jawab berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus Template Tanya Jawab ID {$templateTanyaJawab->id}: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat menghapus.'], 500);
        }
    }
}
