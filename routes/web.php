<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\JadwalIbadahController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PendetaController;
use App\Http\Controllers\RenunganController;
use App\Http\Controllers\TemplateTanyaJawabController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:users')->group(function () {
    Route::get('/login_jemaat', function () {
        return view('pages.login');
    })->name('pages.login');

    Route::get('/register_jemaat', function () {
        return view('pages.register');
    })->name('pages.register');

    Route::get('/request_password', function () {
        return view('pages.request_password');
    })->name('pages.request_password');

    Route::post('/request_password', [PasswordResetController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset_password', [PasswordResetController::class, 'reset'])
        ->name('password.update');

    Route::post('/register_jemaat', [PageController::class, 'register'])->name('register_jemaat');
    Route::post('/login_jemaat', [PageController::class, 'login'])->name('login_jemaat');
});

Route::controller(PageController::class)->group(function () {
    Route::get('/get-renungan/{offset}/{limit}', 'getRenungan');

    Route::get('', 'beranda')->name('beranda');
    Route::get('/jadwal-ibadah', 'jadwalIbadah')->name('jadwal-ibadah');

    Route::get('/renungan', 'renungan')->name('renungan');
    Route::get('/renungan/{slug}', 'detailRenungan')->name('detail-renungan');

    Route::get('/info', 'info')->name('info');
});

Route::middleware('auth:users')->group(function () {
    Route::post('/jemaat_logout', [PageController::class, 'logout'])->name('jemaat_logout');
    Route::get('/profil', [PageController::class, 'profilPage'])->name('profil');
    Route::put('/profil/update', [PageController::class, 'updateProfile'])->name('profil.update');
});

Route::post('send-user-message', [ChatController::class, 'sendUserMessage'])
    ->name('chat.user.send');

Route::middleware('auth:admin_users')->group(function () {
    Route::post('send-admin-message', [ChatController::class, 'sendAdminMessage'])
        ->name('chat.admin.send');

    Route::get('admin/messages/{userId}', [ChatController::class, 'getUserMessages'])
        ->name('chat.admin.getMessages');

    Route::get('get-chat-users', [ChatController::class, 'getChatUsers'])
        ->name('chat.users');

    Route::get('dashboard/chat', [ChatController::class, 'index'])
        ->name('dashboard.chat');
});

Route::get('user/messages', [ChatController::class, 'getMessagesForUser'])
    ->name('chat.user.getMessages');

Route::get('template_tanya_jawab', [ChatController::class, 'templateTanyaJawab']);

Route::post('send-admin-template', [ChatController::class, 'sendAdminTemplate'])
    ->name('chat.admin.template');

Route::middleware('guest:admin_users')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'index'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
});

Route::middleware(['auth:admin_users'])->prefix('dashboard')->group(function () {
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('', function () {
        return view('dashboard.index');
    })->name('dashboard.index');

    Route::get('renungan', function () {
        return view('dashboard.renungan.index');
    })->name('dashboard.renungan');

    Route::get('jadwal_ibadah', function () {
        return view('dashboard.jadwal_ibadah.index');
    })->name('dashboard.jadwal_ibadah');

    Route::get('tanya_jawab', function () {
        return view('dashboard.tanya_jawab.index');
    })->name('dashboard.tanya_jawab');

    Route::get('pendeta', function () {
        return view('dashboard.pendeta.index');
    })->name('dashboard.pendeta');

    Route::get('admin', function () {
        return view('dashboard.admin.index');
    })->name('dashboard.admin');

    Route::get('jemaat', function () {
        return view('dashboard.jemaat.index');
    })->name('dashboard.jemaat');

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
    Route::controller(TemplateTanyaJawabController::class)->group(function () {
        Route::get('/tanya_jawab/tanya_jawabTable', 'index');
        Route::post('/tanya_jawab/simpan_tanya_jawab', 'store');
        Route::get('/tanya_jawab/edit_tanya_jawab/{id}', 'edit');
        Route::get('/tanya_jawab/detail_tanya_jawab/{id}', 'show');
        Route::post('/tanya_jawab/update_tanya_jawab/{id}', 'update');
        Route::delete('/tanya_jawab/hapus_tanya_jawab/{id}', 'destroy');
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

        Route::get('/admin/jemaatTable', 'getAllJemaat');
        Route::delete('/admin/hapus_jemaat/{id}', 'destroyJemaat');
    });
});
