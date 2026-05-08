<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminMonitorController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data mitra
        $mitras = DB::table('mitra')
            ->orderBy('last_ping', 'desc')
            ->get();

        // 2. Lempar data ke view
        return view('admin.monitor', compact('mitras'));
    }

    public function forceLogout($id)
    {
        // 1. Logika reset token dan status
        DB::table('mitra')->where('id_mitra', $id)->update([
            'token_login' => null,
            'status_kerja' => 'istirahat'
        ]);

        // 2. Redirect kembali dengan flash session
        return redirect()->route('admin.monitor')->with('pesan', 'berhasil_reset');
    }
}
