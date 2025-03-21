<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $data = AdminUser::orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($data) {
                return view('dashboard.admin.tombol-aksi')->with('data', $data);
            })
            ->editColumn('created_at', function ($admin) {
                return $admin->created_at->isoFormat('dddd, D MMMM YYYY, HH.mm');
            })
            ->editColumn('updated_at', function ($admin) {
                return $admin->updated_at->isoFormat('dddd, D MMMM YYYY, HH.mm');
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'username' => 'required||unique:admin_users,username',
            'password' => 'required|min:8',
        ], [
            'username.required' => 'username wajib diisi.',
            'username.unique' => 'username sudah digunakan.',
            'password.min' => 'Password wajib memiliki minimal 8 karakter.',
        ]);

        try {
            // Simpan data ke dalam database
            AdminUser::create([
                'username' => $validatedData['username'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Berhasil menyimpan
            return response()->json(['message' => 'Admin berhasil ditambahkan'], 200);
        } catch (\Exception $e) {
            // Tangkap dan log error jika terjadi
            Log::error($e->getMessage());
            return response()->json(['errors' => 'Terjadi kesalahan saat menyimpan data'], 422);
        }
    }

    public function edit(int $id)
    {
        $data = AdminUser::findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        // Find the existing record
        $data = AdminUser::findOrFail($id);

        $validatedData = $request->validate([
            'username' => 'required|unique:users,username,' . $data->id,
            'password' => 'nullable|min:8',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah ada.',
            'password.min' => 'Password wajib memiliki minimal 8 karakter.',
        ]);

        // Update the record
        $data->username = $validatedData['username'];
        if($request->has('password')){
            $data->password = Hash::make($validatedData['password']);
        }

        // Save changes
        $data->save();

        return response()->json(['message' => 'Admin berhasil diupdate'], 200);
    }

    public function destroy($id)
    {
        try {
            $admin = AdminUser::findOrFail($id);
            $admin->delete();

            return response()->json(['message' => 'Admin berhasil dihapus'], 200);
        } catch (\Exception $e) {
            // Handle error
            Log::error($e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data'], 500);
        }
    }

    public function getAllJemaat()
    {
        $data = User::orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($data) {
                return view('dashboard.jemaat.tombol-aksi')->with('data', $data);
            })
            ->editColumn('created_at', function ($admin) {
                return $admin->created_at->isoFormat('dddd, D MMMM YYYY, HH.mm');
            })
            ->editColumn('updated_at', function ($admin) {
                return $admin->updated_at->isoFormat('dddd, D MMMM YYYY, HH.mm');
            })
            ->make(true);
    }

    public function destroyJemaat($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['message' => 'Jemaat berhasil dihapus'], 200);
        } catch (\Exception $e) {
            // Handle error
            Log::error($e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data'], 500);
        }
    }
}
