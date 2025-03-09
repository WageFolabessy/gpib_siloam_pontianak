<?php

namespace App\Http\Controllers;

use App\Models\JadwalIbadah;
use App\Models\Renungan;
use App\Models\Pendeta;

class PageController extends Controller
{
    public function beranda()
    {
        $renungan = Renungan::take(3)->orderBy('created_at', 'desc')->get(); // Mengambil 3 renungan pertama
        return view('pages.beranda', compact('renungan'));
    }

    public function jadwalIbadah()
    {
        $jadwalIbadah = JadwalIbadah::get();
        return view('pages.jadwal-ibadah', compact('jadwalIbadah'));
    }
    
    public function renungan()
    {
        $renungan = Renungan::take(3)->orderBy('created_at', 'desc')->get(); // Mengambil 3 renungan pertama
        return view('pages.renungan', compact('renungan'));
    }

    public function getRenungan($offset, $limit)
    {
        $renungan = Renungan::skip($offset)->take($limit)->orderBy('created_at', 'desc')->get();
        return response()->json($renungan);
    }

    public function detailRenungan($slug)
    {
        $renungan = Renungan::where('slug', $slug)->firstOrFail();
        $diupload = $renungan->updated_at->isoFormat('dddd, D MMMM YYYY, HH.mm');

        // mendapatkan next dan previous renungan
        $prevRenungan = Renungan::where('id', '<', $renungan->id)->orderBy('created_at', 'desc')->first();
        $nextRenungan = Renungan::where('id', '>', $renungan->id)->orderBy('created_at')->first();

        return view('pages.detail-renungan', compact('renungan', 'diupload', 'prevRenungan', 'nextRenungan'));
    }

    public function info()
    {
        $pengurus = Pendeta::get();
        return view('pages.info', compact('pengurus'));
    }
}
