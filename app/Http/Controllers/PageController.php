<?php

namespace App\Http\Controllers;

use App\Models\JadwalIbadah;
use App\Models\Renungan;
use App\Models\Pendeta;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PageController extends Controller
{
    public function beranda(): View
    {
        $renungan = Renungan::orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        return view('pages.beranda', compact('renungan'));
    }

    public function jadwalIbadah(): View
    {
        $jadwalIbadah = JadwalIbadah::orderBy('kategori', 'asc')
            ->orderByRaw("CASE hari WHEN 'Minggu' THEN 1 WHEN 'Senin' THEN 2 WHEN 'Selasa' THEN 3 WHEN 'Rabu' THEN 4 WHEN 'Kamis' THEN 5 WHEN 'Jumat' THEN 6 WHEN 'Sabtu' THEN 7 ELSE 8 END ASC")
            ->orderBy('jam', 'asc')
            ->paginate(15);

        return view('pages.jadwal-ibadah', compact('jadwalIbadah'));
    }

    public function renungan(): View
    {
        $renungan = Renungan::orderBy('created_at', 'desc')
            ->paginate(9);

        return view('pages.renungan', compact('renungan'));
    }

    public function getRenungan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'offset' => ['required', 'integer', 'min:0'],
            'limit' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $renungan = Renungan::orderBy('created_at', 'desc')
            ->skip($validated['offset'])
            ->take($validated['limit'])
            ->get();

        return response()->json([
            'data' => $renungan,
            'hasMore' => $renungan->count() === (int)$validated['limit']
        ]);
    }

    public function detailRenungan(Renungan $renungan): View
    {
        $diupload = $renungan->updated_at?->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm');

        $prevRenungan = Renungan::where('created_at', '<', $renungan->created_at)
            ->orderBy('created_at', 'desc')
            ->first(['id', 'slug', 'judul']);

        $nextRenungan = Renungan::where('created_at', '>', $renungan->created_at)
            ->orderBy('created_at', 'asc')
            ->first(['id', 'slug', 'judul']);

        return view('pages.detail-renungan', compact('renungan', 'diupload', 'prevRenungan', 'nextRenungan'));
    }

    public function info(): View
    {
        $pengurus = Pendeta::orderBy('kategori', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        return view('pages.info', compact('pengurus'));
    }
}
