<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUmurPelanggan
{
    // Usage: ->middleware('umur:18') atau ->middleware('umur:10')
    public function handle(Request $request, Closure $next, int $minUmur)
    {
        $pelanggan = $request->session()->get('pelanggan');

        if (! $pelanggan || $pelanggan->umur < $minUmur) {
            $msg = $minUmur >= 18
                ? 'Anda harus berusia minimal 18 tahun untuk menggunakan layanan ini.'
                : 'Anda harus berusia minimal 10 tahun untuk menggunakan layanan ini.';

            return redirect()->back()->with('error', $msg);
        }

        return $next($request);
    }
}
