<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }
            if ($request->is('mitra') || $request->is('mitra/*')) {
                return route('mitra.login');
            }
            return route('login');
        });

        $middleware->alias([
            'mitra.kategori'   => \App\Http\Middleware\CheckMitraKategori::class,
            'profil.lengkap'   => \App\Http\Middleware\CheckPelangganProfil::class,
            'umur'             => \App\Http\Middleware\CheckUmurPelanggan::class,
            'single.device'    => \App\Http\Middleware\CheckSingleDevice::class,
            // Role-based access control untuk mitra
            'verified.mitra'   => \App\Http\Middleware\VerifikasiMitra::class,
            'feature'          => \App\Http\Middleware\FeatureMitra::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
