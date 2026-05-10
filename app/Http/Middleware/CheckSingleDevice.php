<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;

class CheckSingleDevice
{
    public function handle(Request $request, Closure $next)
    {
        $session  = $request->session();
        $userType = $session->get('auth_type');  // 'mitra' atau 'pelanggan'
        $userId   = $session->get('auth_id');
        $deviceId = $session->get('device_id');

        if (! $userType || ! $userId || ! $deviceId) {
            return $next($request);
        }

        $activeSession = UserSession::where('user_type', $userType)
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        if ($activeSession && $activeSession->device_id !== $deviceId) {
            $session->flush();
            return redirect()->route("{$userType}.login")
                ->with('error', 'Akun Anda sedang aktif di perangkat lain. Silakan minta izin dari perangkat tersebut.');
        }

        if ($activeSession) {
            $activeSession->update(['last_active_at' => now()]);
        }

        return $next($request);
    }
}
