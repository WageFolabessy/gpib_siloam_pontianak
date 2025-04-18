<?php

namespace App\Http\Controllers;

use App\Models\JadwalIbadah;
use App\Models\Renungan;
use App\Models\Pendeta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $renungan = Renungan::orderBy('created_at', 'desc')->paginate(6); // Mengambil halaman pertama
        return view('pages.renungan', compact('renungan'));
    }

    public function getRenunganPage(Request $request): JsonResponse
    {
        try {
            $page = $request->query('page', 1);

            $renungan = Renungan::orderBy('created_at', 'desc')
                ->paginate(6, ['*'], 'page', $page);

            $html = view('pages.renungan.renungan-card', compact('renungan'))->render();

            $nextPageUrl = $renungan->hasMorePages() ? $renungan->nextPageUrl() : null;

            return response()->json([
                'html' => $html,
                'nextPageUrl' => $nextPageUrl
            ]);
        } catch (\Exception $e) {
            Log::error('Error getRenunganPage AJAX: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());

            $errorData = ['error' => 'Gagal memuat data renungan.'];
            if (config('app.debug')) {
                $errorData['details'] = $e->getMessage();
                $errorData['trace'] = $e->getTraceAsString();
            }
            return response()->json($errorData, 500);
        }
    }

    public function detailRenungan(Renungan $renungan): View
    {
        $diupload = optional($renungan->updated_at)->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm');

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
