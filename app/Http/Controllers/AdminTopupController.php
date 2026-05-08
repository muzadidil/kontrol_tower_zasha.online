<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTopupController extends Controller
{
    public function index()
    {
        $topups = DB::table('topup_mitra as t')
            ->join('mitra as m', 't.id_mitra', '=', 'm.id_mitra')
            ->select('t.*', 'm.nama_mitra', 'm.no_wa')
            ->where('t.status_topup', 'Pending')
            ->orderBy('t.tanggal', 'desc')
            ->get();

        return view('admin.finance.topup_confirm', compact('topups'));
    }

    public function process(Request $request, $id)
    {
        $aksi = $request->query('aksi');
        $status = ($aksi == 'setuju') ? 'Selesai' : 'Expired';

        DB::table('topup_mitra')
            ->where('id_topup', $id)
            ->update(['status_topup' => $status]);

        $pesan = ($aksi == 'setuju') ? 'Top-up berhasil disetujui!' : 'Top-up telah ditolak.';
        
        return redirect()->route('admin.finance.topup.index')->with('notif', $pesan);
    }
}
