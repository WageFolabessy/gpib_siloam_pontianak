<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Opsi default untuk guard dan broker password.
    | Default guard diubah ke 'web' untuk konsistensi Laravel.
    | Default password broker tetap 'users' untuk user biasa (Jemaat).
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Mendefinisikan guard 'web' untuk Jemaat (user biasa) dan
    | 'admin_users' untuk Admin. Keduanya menggunakan session driver.
    */

    'guards' => [
        'web' => [
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
    | Mendefinisikan bagaimana data user diambil berdasarkan model Eloquent.
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
    | Konfigurasi untuk broker reset password.
    | Broker 'admin_users' sekarang direkomendasikan menggunakan tabel terpisah.
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
            'table' => env('AUTH_ADMIN_PASSWORD_RESET_TOKEN_TABLE', 'admin_password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
