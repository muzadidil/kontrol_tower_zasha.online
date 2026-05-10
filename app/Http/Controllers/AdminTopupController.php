<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Mitra;

class AdminTopupController extends Controller
{
    public function index()
    {
        $topups = DB::table('topup_mitra as t')
            ->join('mitra as m', 't.id_mitra', '=', 'm.id_mitra')
            ->select('t.*', 'm.nama_asli as nama_mitra', 'm.no_wa')
            ->where('t.status_topup', 'Pending')
            ->orderBy('t.tanggal', 'desc')
            ->get();

        return view('admin.finance.topup_confirm', compact('topups'));
    }

    public function process(Request $request, $id)
    {
        $aksi   = $request->query('aksi');
        $status = ($aksi == 'setuju') ? 'Selesai' : 'Expired';

        $topup = DB::table('topup_mitra')->where('id_topup', $id)->first();

        if (!$topup || $topup->status_topup !== 'Pending') {
            return redirect()->route('admin.finance.topup.index')->with('notif', 'Transaksi tidak ditemukan atau sudah diproses.');
        }

        DB::transaction(function () use ($id, $status, $topup, $aksi) {
            DB::table('topup_mitra')->where('id_topup', $id)->update(['status_topup' => $status]);

            if ($aksi === 'setuju') {
                Mitra::where('id_mitra', $topup->id_mitra)->increment('saldo', $topup->jumlah_topup);
            }
        });

        $pesan = ($aksi == 'setuju') ? 'Top-up berhasil disetujui, saldo mitra telah ditambahkan.' : 'Top-up telah ditolak.';

        return redirect()->route('admin.finance.topup.index')->with('notif', $pesan);
    }
}
