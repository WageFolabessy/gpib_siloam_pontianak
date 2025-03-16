<?php

namespace App\Http\Controllers;

use App\Models\JadwalIbadah;
use App\Models\Renungan;
use App\Models\Pendeta;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

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

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password wajib memiliki minimal 8 karakter.',
        ]);

        try {
            User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            return redirect()->route('pages.login')
                ->with('success', 'Registrasi berhasil. Silakan login.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['message' => 'Terjadi kesalahan saat menyimpan data']);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('users')->attempt($credentials)) {
            return redirect()->route('beranda');
        }

        return redirect()->back()->withErrors(['message' => 'Email atau password salah']);
    }

    public function logout()
    {
        Auth::guard('users')->logout();

        return redirect()->route('beranda');
    }

    public function profilPage()
    {
        $user = Auth::user();

        return view('pages.profil', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $data = Auth::user();

        $user = User::findOrFail($data->id);

        $validatedData = $request->validate([
            'name'  => 'required|max:80',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8'
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password wajib memiliki minimal 8 karakter.',
        ]);

        $user->name  = $validatedData['name'];
        $user->email = $validatedData['email'];

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('profil')->with('success', 'Profil berhasil diperbaharui.');
    }
}
