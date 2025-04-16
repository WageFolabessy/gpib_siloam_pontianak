<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $query = AdminUser::query()->select(['id', 'username', 'created_at', 'updated_at'])
                ->orderBy('username', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function (AdminUser $admin) {
                    if ($admin->id === Auth::guard('admin_users')->id()) {
                        return '<span class="text-muted fst-italic">Anda</span>';
                    }
                    return view('dashboard.admin.tombol-aksi', compact('admin'));
                })
                ->editColumn('created_at', function (AdminUser $admin) {
                    return $admin->created_at?->isoFormat('D MMM YY, HH:mm');
                })
                ->editColumn('updated_at', function (AdminUser $admin) {
                    return $admin->updated_at?->diffForHumans();
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('dashboard.admin.index');
    }

    public function edit(AdminUser $admin): JsonResponse
    {
        return response()->json(['data' => $admin->only(['id', 'username'])]);
    }

    public function store(StoreAdminRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            AdminUser::create($validatedData);
            return response()->json(['message' => 'Admin berhasil ditambahkan.'], 201);
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan Admin: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat menyimpan.'], 500);
        }
    }

    public function update(UpdateAdminRequest $request, AdminUser $admin): JsonResponse
    {
        $validatedData = $request->validated();
        $updateData = [
            'username' => $validatedData['username'],
        ];

        if (!empty($validatedData['password'])) {
            $updateData['password'] = $validatedData['password'];
        }

        try {
            $admin->update($updateData);
            return response()->json(['message' => 'Admin berhasil diperbarui.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui Admin ID {$admin->id}: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat memperbarui.'], 500);
        }
    }

    public function destroy(AdminUser $admin): JsonResponse
    {
        if ($admin->id === Auth::guard('admin_users')->id()) {
            return response()->json(['message' => 'Anda tidak dapat menghapus akun Anda sendiri.'], 403);
        }

        try {
            $admin->delete();
            return response()->json(['message' => 'Admin berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus Admin ID {$admin->id}: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat menghapus.'], 500);
        }
    }
}
