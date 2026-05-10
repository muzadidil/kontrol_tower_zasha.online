<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMonitorController extends Controller
{
    public function index(Request $request)
    {
        // Ambil mitra + data session terakhir (jika ada)
        $mitras = DB::table('mitra as m')
            ->leftJoin('user_sessions as us', function ($join) {
                $join->on(DB::raw('CAST(us.user_id AS UNSIGNED)'), '=', 'm.id_mitra')
                     ->where('us.user_type', '=', 'mitra')
                     ->where('us.is_active', '=', true);
            })
            ->select(
                'm.id_mitra',
                'm.nama_asli as nama_mitra',
                'm.nama_panggilan',
                'm.no_wa',
                'm.status_mitra',
                'm.status_verifikasi',
                'm.updated_at',
                'us.last_active_at',
                'us.device_name',
                'us.device_id',
                'us.token as session_token'
            )
            ->orderByRaw('COALESCE(us.last_active_at, m.updated_at) desc')
            ->get();

        return view('admin.monitor.monitor', compact('mitras'));
    }

    public function forceLogout($id)
    {
        // Deactivate semua session aktif untuk mitra ini
        DB::table('user_sessions')
            ->where('user_type', 'mitra')
            ->where('user_id', (string) $id)
            ->update(['is_active' => false]);

        // Hapus token Sanctum jika ada
        DB::table('personal_access_tokens')
            ->where('tokenable_type', \App\Models\Mitra::class)
            ->where('tokenable_id', $id)
            ->delete();

        return redirect()->route('admin.monitor')->with('pesan', 'berhasil_reset');
    }
}
