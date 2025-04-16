<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class JemaatController extends Controller
{
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $query = User::query()->select(['id', 'name', 'email', 'created_at', 'updated_at'])
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function (User $user) {
                    return view('dashboard.jemaat.tombol-aksi', compact('user'));
                })
                ->editColumn('created_at', function (User $user) {
                    return $user->created_at?->isoFormat('D MMM YY, HH:mm');
                })
                ->editColumn('updated_at', function (User $user) {
                    return $user->updated_at?->diffForHumans();
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('dashboard.jemaat.index');
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();
            return response()->json(['message' => 'Jemaat berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus Jemaat (User) ID {$user->id}: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal saat menghapus data.'], 500);
        }
    }
}
