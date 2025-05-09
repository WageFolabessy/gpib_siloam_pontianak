<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\JadwalIbadahController;
use App\Http\Controllers\JemaatController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PendetaController;
use App\Http\Controllers\RenunganController;
use App\Http\Controllers\TemplateTanyaJawabController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\WartaJemaatController;
use Illuminate\Support\Facades\Route;

Route::controller(UserAuthController::class)->group(function () {
    // Login
    Route::get('/login', 'showLoginForm')->name('pages.login')->middleware('guest:web');
    Route::post('/login', 'login')->name('login_jemaat')->middleware('guest:web');
    // Logout
    Route::post('/logout', 'logout')->name('jemaat_logout')->middleware('auth:web');
    // Forgot Password
    Route::get('/lupa-password', 'showLinkRequestForm')->name('password.request')->middleware('guest:web');
    Route::post('/lupa-password', 'sendResetLinkEmail')->name('password.email')->middleware('guest:web');
    // Reset Password
    Route::get('/reset-password/{token}', 'showResetForm')->name('password.reset')->middleware('guest:web');
    Route::post('/reset-password', 'reset')->name('password.update')->middleware('guest:web');
    // Register
    Route::get('/register', 'showRegistrationForm')->name('pages.register')->middleware('guest:web');
    Route::post('/register', 'register')->name('register_jemaat')->middleware('guest:web');

    Route::get('/profil', 'showProfileForm')->name('profil')->middleware('auth:web');
    Route::put('/profil', 'updateProfile')->name('profil.update')->middleware('auth:web');
});

Route::controller(PageController::class)->group(function () {
    Route::get('/get-renungan/{offset}/{limit}', 'getRenungan');

    Route::get('', 'beranda')->name('beranda');
    Route::get('/jadwal-ibadah', 'jadwalIbadah')->name('jadwal-ibadah');

    Route::get('/get-renungan-page', 'getRenunganPage')->name('renungan.loadmore');
    Route::get('/renungan', 'renungan')->name('renungan');
    Route::get('/renungan/{renungan:slug}', 'detailRenungan')->name('detail-renungan');

    Route::get('/info', 'info')->name('info');
});


// Chat
Route::get('/chat-templates', [ChatController::class, 'templateTanyaJawab'])
    ->name('chat.templates');

Route::middleware(['auth:web'])
    ->prefix('chat')
    ->name('user.chat.')
    ->group(function () {

        Route::get('/my-history', [ChatController::class, 'getMyChatHistory'])
            ->name('history');

        Route::post('/send-user', [ChatController::class, 'storeUserMessage'])
            ->name('send');

        Route::post('/mark-read', [ChatController::class, 'markUserMessagesAsRead'])
            ->name('markread');
    });

Route::middleware(['auth:admin_users'])
    ->prefix('dashboard/chat')
    ->name('admin.chat.')
    ->group(function () {

        Route::get('/', [ChatController::class, 'index'])
            ->name('index');

        Route::get('/users-datatable', [ChatController::class, 'getChatUsersForDataTable'])
            ->name('users.datatable');

        Route::get('/history/{user}', [ChatController::class, 'getChatHistoryForAdmin'])
            ->name('history');

        Route::post('/send-admin', [ChatController::class, 'storeAdminMessage'])
            ->name('send.admin');

        Route::post('/send-template', [ChatController::class, 'sendAdminTemplateMessage'])
            ->name('send.template');

        Route::post('/mark-read', [ChatController::class, 'markAdminMessagesAsRead'])
            ->name('markread');
    });

// End Chat

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

    Route::get('warta_jemaat', function () {
        return view('dashboard.warta_jemaat.index');
    })->name('dashboard.warta_jemaat');

    Route::get('admin', function () {
        return view('dashboard.admin.index');
    })->name('dashboard.admin');

    Route::get('jemaat', function () {
        return view('dashboard.jemaat.index');
    })->name('dashboard.jemaat');

    Route::controller(RenunganController::class)->group(function () {
        Route::get('/renungan/renunganTable', 'index');
        Route::post('/renungan/simpan_renungan', 'store');
        Route::get('/renungan/edit_renungan/{renungan}', 'edit');
        Route::put('/renungan/update_renungan/{renungan}', 'update');
        Route::delete('/renungan/hapus_renungan/{renungan}', 'destroy');
    });
    Route::controller(JadwalIbadahController::class)->group(function () {
        Route::get('/jadwal_ibadah/jadwal_ibadahTable', 'index');
        Route::post('/jadwal_ibadah/simpan_jadwal_ibadah', 'store');
        Route::get('/jadwal_ibadah/edit_jadwal_ibadah/{jadwalIbadah}', 'edit');
        Route::put('/jadwal_ibadah/update_jadwal_ibadah/{jadwalIbadah}', 'update');
        Route::delete('/jadwal_ibadah/hapus_jadwal_ibadah/{jadwalIbadah}', 'destroy');
    });
    Route::controller(TemplateTanyaJawabController::class)->group(function () {
        Route::get('/tanya_jawab/tanya_jawabTable', 'index');
        Route::post('/tanya_jawab/simpan_tanya_jawab', 'store');
        Route::get('/tanya_jawab/edit_tanya_jawab/{templateTanyaJawab}', 'edit');
        Route::get('/tanya_jawab/detail_tanya_jawab/{templateTanyaJawab}', 'show');
        Route::put('/tanya_jawab/update_tanya_jawab/{templateTanyaJawab}', 'update');
        Route::delete('/tanya_jawab/hapus_tanya_jawab/{templateTanyaJawab}', 'destroy');
    });
    Route::controller(PendetaController::class)->group(function () {
        Route::get('/pendeta/pendetaTable', 'index');
        Route::post('/pendeta/simpan_pendeta', 'store');
        Route::get('/pendeta/edit_pendeta/{pendeta}', 'edit');
        Route::put('/pendeta/update_pendeta/{pendeta}', 'update');
        Route::delete('/pendeta/hapus_pendeta/{pendeta}', 'destroy');
    });
    Route::controller(WartaJemaatController::class)->group(function () {
        Route::get('/warta_jemaat/wartaJemaatTable', 'index')->name('index');
        Route::post('/warta_jemaat/simpan_warta_jemaat', 'store')->name('store');
        Route::get('/warta_jemaat/edit_warta_jemaat/{wartaJemaat}', 'edit')->name('edit');
        Route::put('/warta_jemaat/update_warta_jemaat/{wartaJemaat}', 'update')->name('update');
        Route::delete('/warta_jemaat/hapus_warta_jemaat/{wartaJemaat}', 'destroy')->name('destroy');
    });
    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/adminTable', 'index');
        Route::post('/admin/simpan_admin', 'store');
        Route::get('/admin/edit_admin/{admin}', 'edit');
        Route::put('/admin/update_admin/{admin}', 'update');
        Route::delete('/admin/hapus_admin/{admin}', 'destroy');
    });
    Route::controller(JemaatController::class)->group(function () {
        Route::get('/jemaat/jemaatTable', 'index');
        Route::delete('/jemaat/hapus_jemaat/{user}', 'destroy');
    });
});
