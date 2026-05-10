<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPelangganProfil
{
    public function handle(Request $request, Closure $next)
    {
        $pelanggan = $request->session()->get('pelanggan');

        if (! $pelanggan || ! $pelanggan->is_profile_complete) {
            return redirect()->route('pelanggan.profil.edit')
                ->with('warning', 'Lengkapi profil Anda terlebih dahulu sebelum memesan jasa.');
        }

        if ($pelanggan->alamats()->count() === 0) {
            return redirect()->route('pelanggan.alamat.create')
                ->with('warning', 'Tambahkan minimal 1 alamat sebelum memesan jasa.');
        }

        return $next($request);
    }
}
