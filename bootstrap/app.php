<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function (Request $request) {
            // Jika URL mengarah ke dashboard (atau sub-path-nya), arahkan ke halaman admin login
            if ($request->is('dashboard') || $request->is('dashboard/*')) {
                return route('admin.login');
            }
            // Defaultnya untuk pengguna jemaat
            return route('pages.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
