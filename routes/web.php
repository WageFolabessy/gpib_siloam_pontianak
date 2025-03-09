<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\JadwalIbadahController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PendetaController;
use App\Http\Controllers\RenunganController;
use Illuminate\Support\Facades\Route;

Route::controller(PageController::class)->group(function () {
    Route::get('/get-renungan/{offset}/{limit}', 'getRenungan');

    Route::get('', 'beranda')->name('beranda');
    Route::get('/jadwal-ibadah', 'jadwalIbadah')->name('jadwal-ibadah');

    Route::get('/renungan', 'renungan')->name('renungan');
    Route::get('/renungan/{slug}', 'detailRenungan')->name('detail-renungan');

    Route::get('/info', 'info')->name('info');
});

Route::get('/login', [AdminAuthController::class, 'index'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login']);
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('', function () {
        return view('dashboard.index');
    })->name('dashboard.index');

    Route::get('renungan', function () {
        return view('dashboard.renungan.index');
    })->name('dashboard.renungan');

    Route::get('jadwal_ibadah', function () {
        return view('dashboard.jadwal_ibadah.index');
    })->name('dashboard.jadwal_ibadah');

    Route::get('pendeta', function () {
        return view('dashboard.pendeta.index');
    })->name('dashboard.pendeta');

    Route::get('admin', function () {
        return view('dashboard.admin.index');
    })->name('dashboard.admin');

    Route::controller(RenunganController::class)->group(function () {
        Route::get('/renungan/renunganTable', 'index');
        Route::post('/renungan/simpan_renungan', 'store');
        Route::get('/renungan/edit_renungan/{id}', 'edit');
        Route::post('/renungan/update_renungan/{id}', 'update');
        Route::delete('/renungan/hapus_renungan/{id}', 'destroy');
    });
    Route::controller(JadwalIbadahController::class)->group(function () {
        Route::get('/jadwal_ibadah/jadwal_ibadahTable', 'index');
        Route::post('/jadwal_ibadah/simpan_jadwal_ibadah', 'store');
        Route::get('/jadwal_ibadah/edit_jadwal_ibadah/{id}', 'edit');
        Route::post('/jadwal_ibadah/update_jadwal_ibadah/{id}', 'update');
        Route::delete('/jadwal_ibadah/hapus_jadwal_ibadah/{id}', 'destroy');
    });
    Route::controller(PendetaController::class)->group(function () {
        Route::get('/pendeta/pendetaTable', 'index');
        Route::post('/pendeta/simpan_pendeta', 'store');
        Route::get('/pendeta/edit_pendeta/{id}', 'edit');
        Route::post('/pendeta/update_pendeta/{id}', 'update');
        Route::delete('/pendeta/hapus_pendeta/{id}', 'destroy');
    });
    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/adminTable', 'index');
        Route::post('/admin/simpan_admin', 'store');
        Route::get('/admin/edit_admin/{id}', 'edit');
        Route::post('/admin/update_admin/{id}', 'update');
        Route::delete('/admin/hapus_admin/{id}', 'destroy');
    });
});
