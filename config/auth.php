<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Option default untuk guard dan broker password. Jika tidak diubah di
    | environment, maka nilai defaultnya adalah 'web' dan 'users'.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'users'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Guard digunakan untuk mendefinisikan bagaimana pengguna diotentikasi
    | untuk setiap request. Di sini kita mendefinisikan guard untuk web dan admin.
    |
    */

    'guards' => [
        'users' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'admin_users' => [
            'driver' => 'session',
            'provider' => 'admin_users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Provider mendefinisikan cara untuk mengambil data dari database atau
    | sumber penyimpanan lainnya. Di sini kita mendefinisikan provider untuk
    | pengguna biasa dan admin.
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'admin_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\AdminUser::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Konfigurasi ini mengatur perilaku fitur reset password, termasuk table
    | penyimpanan token reset, durasi token, dan throttle.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        'admin_users' => [
            'provider' => 'admin_users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Durasi dalam detik sebelum pengguna harus mengonfirmasi ulang passwordnya.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];
