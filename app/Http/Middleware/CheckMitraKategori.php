<?php

namespace App\Http\Middleware;

use App\Enums\MitraKategori;
use Closure;
use Illuminate\Http\Request;

class CheckMitraKategori
{
    // Usage di route: ->middleware('mitra.kategori:WFH')
    public function handle(Request $request, Closure $next, string $kategori)
    {
        $mitra = $request->session()->get('mitra');

        if (! $mitra || $mitra->kategori_kode?->value !== $kategori) {
            return redirect()->route('mitra.login')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
