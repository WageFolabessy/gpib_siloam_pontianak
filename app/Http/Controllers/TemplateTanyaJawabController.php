<?php

namespace App\Http\Controllers;

use App\Models\TemplateTanyaJawab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TemplateTanyaJawabController extends Controller
{
    public function index()
    {
        $data = TemplateTanyaJawab::orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($data) {
                return view('dashboard.tanya_jawab.tombol-aksi')->with('data', $data);
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
            'pertanyaan' => 'required',
            'jawaban' => 'required',
        ], [
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'jawaban.required' => 'Jawaban wajid diisi.',
        ]);

        try {
            // Simpan data ke dalam database
            TemplateTanyaJawab::create([
                'pertanyaan' => $validatedData['pertanyaan'],
                'jawaban' => $validatedData['jawaban'],
            ]);

            // Berhasil menyimpan
            return response()->json(['message' => 'Tempate tanya jawab berhasil ditambahkan'], 200);
        } catch (\Exception $e) {
            // Tangkap dan log error jika terjadi
            Log::error($e->getMessage());
            return response()->json(['errors' => 'Terjadi kesalahan saat menyimpan data'], 422);
        }
    }

    public function edit(int $id)
    {
        $data = TemplateTanyaJawab::findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function show(int $id)
    {
        $data = TemplateTanyaJawab::findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        // Find the existing record
        $data = TemplateTanyaJawab::findOrFail($id);

        $validatedData = $request->validate([
            'pertanyaan' => 'required',
            'jawaban' => 'required',
        ], [
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'jawaban.required' => 'Jawaban wajid diisi.',
        ]);

        // Update the record
        $data->pertanyaan = $validatedData['pertanyaan'];
        $data->jawaban = $validatedData['jawaban'];

        // Save changes
        $data->save();

        return response()->json(['message' => 'Tempate tanya jawab berhasil diupdate'], 200);
    }

    public function destroy($id)
    {
        try {
            $tanyaJawab = TemplateTanyaJawab::findOrFail($id);
            $tanyaJawab->delete();

            return response()->json(['message' => 'Tempate tanya jawab berhasil dihapus'], 200);
        } catch (\Exception $e) {
            // Handle error
            Log::error($e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data'], 500);
        }
    }
}
