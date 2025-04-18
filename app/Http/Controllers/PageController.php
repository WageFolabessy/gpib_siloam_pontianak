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
        $renungan = Renungan::orderBy('created_at', 'desc')->paginate(6); // Mengambil halaman pertama
        return view('pages.renungan', compact('renungan'));
    }

    public function getRenunganPage(Request $request): JsonResponse
    {
        try {
            // Ambil nomor halaman dari query string (?page=X)
            // Default ke halaman 1 jika tidak ada parameter 'page'
            $page = $request->query('page', 1);

            $renungan = Renungan::orderBy('created_at', 'desc')
                ->paginate(6, ['*'], 'page', $page);

            // Pastikan view 'pages.renungan.renungan-card' ada
            // Render partial view HANYA untuk kartu-kartu renungan
            $html = view('pages.renungan.renungan-card', compact('renungan'))->render();

            // Dapatkan URL halaman berikutnya DARI KONTEKS route ini
            $nextPageUrl = $renungan->hasMorePages() ? $renungan->nextPageUrl() : null;

            // Kembalikan response JSON
            return response()->json([
                'html' => $html,
                'nextPageUrl' => $nextPageUrl // URL ini sekarang akan benar -> /get-renungan-page?page=X+1
            ]);
        } catch (\Exception $e) {
            // Log error jika gagal
            Log::error('Error getRenunganPage AJAX: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());

            // Kembalikan response error JSON
            // Memberikan detail error lebih banyak saat debugging (jika APP_DEBUG true)
            $errorData = ['error' => 'Gagal memuat data renungan.'];
            if (config('app.debug')) {
                $errorData['details'] = $e->getMessage();
                $errorData['trace'] = $e->getTraceAsString(); // Hati-hati dengan informasi sensitif
            }
            return response()->json($errorData, 500);
        }
    }

    public function detailRenungan(Renungan $renungan): View
    {
        // Gunakan Carbon instance langsung jika kolom di-cast sebagai tanggal di model
        $diupload = optional($renungan->updated_at)->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm');
        // Atau jika tidak di-cast:
        // $diupload = \Carbon\Carbon::parse($renungan->updated_at)->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm');

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
