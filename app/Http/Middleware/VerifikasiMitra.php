<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Pastikan mitra sudah verified sebelum akses fitur utama.
 * Mitra dengan status_verifikasi != 'verified' akan di-redirect
 * ke halaman status verifikasi.
 *
 * Skip middleware ini di route verifikasi sendiri pakai:
 *   ->withoutMiddleware('verified.mitra')
 */
class VerifikasiMitra
{
    public function handle(Request $request, Closure $next)
    {
        $mitra = Auth::guard('mitra')->user();

        if (!$mitra) {
            return redirect()->route('mitra.login');
        }

        // Status mitra dari MitraStatus enum — yang "aktif" adalah 'active'.
        // Kalau cast ke enum gagal (data legacy), fall back ke string compare.
        $status = $mitra->status_verifikasi;
        $isActive = false;
        if ($status instanceof \App\Enums\MitraStatus) {
            $isActive = $status->isActive();
        } elseif (is_string($status)) {
            $isActive = in_array($status, ['active', 'verified'], true);
        }

        if (!$isActive) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun belum diverifikasi. Tunggu admin approve dokumen Anda.',
                ], 403);
            }
            return redirect()->route('mitra.verifikasi.status')
                ->with('warning', 'Akun Anda masih dalam proses verifikasi.');
        }

        return $next($request);
    }
}
