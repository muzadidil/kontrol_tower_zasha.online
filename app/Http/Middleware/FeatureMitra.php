<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Pastikan mitra punya akses ke fitur tertentu via role-nya.
 * Pakai di route: ->middleware('feature:order-jastip')
 * Mitra tanpa fitur akan dapat HTTP 403 (atau JSON 403 untuk API).
 */
class FeatureMitra
{
    public function handle(Request $request, Closure $next, string $feature)
    {
        $mitra = Auth::guard('mitra')->user();

        if (!$mitra) {
            return redirect()->route('mitra.login');
        }

        if (!$mitra->hasFeature($feature)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Fitur '{$feature}' tidak tersedia untuk role Anda.",
                ], 403);
            }
            abort(403, "Fitur '{$feature}' tidak tersedia untuk role Anda.");
        }

        return $next($request);
    }
}
