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

        if (($mitra->status_verifikasi ?? null) !== 'verified') {
            // Kalau request expect JSON (API), kasih response 403.
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
