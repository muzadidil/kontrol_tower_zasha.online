<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Cek apakah mitra yang sedang login punya fitur tertentu.
     *
     * Usage di Blade:
     *   @if(\App\Providers\AppServiceProvider::mitraHasFeature('order-jastip'))
     *       <a href="...">Order Jastip</a>
     *   @endif
     *
     * Atau lebih clean pakai auth() langsung di view:
     *   @if(auth('mitra')->user()?->hasFeature('order-jastip'))
     *       <a href="...">Order Jastip</a>
     *   @endif
     */
    public static function mitraHasFeature(string $key): bool
    {
        $mitra = Auth::guard('mitra')->user();
        return $mitra && method_exists($mitra, 'hasFeature') && $mitra->hasFeature($key);
    }

    /**
     * Cek apakah mitra yang sedang login sudah verified.
     *
     * Usage di Blade:
     *   @if(\App\Providers\AppServiceProvider::mitraIsVerified())
     *       <!-- konten khusus mitra terverifikasi -->
     *   @endif
     */
    public static function mitraIsVerified(): bool
    {
        $mitra = Auth::guard('mitra')->user();
        return $mitra && ($mitra->status_verifikasi ?? null) === 'verified';
    }
}
