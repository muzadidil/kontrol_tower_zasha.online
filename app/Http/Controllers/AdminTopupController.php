<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Mitra;
use App\Models\Pelanggan;

class AdminTopupController extends Controller
{
    /**
     * Tampilkan semua topup pending (mitra + pelanggan).
     */
    public function index()
    {
        // Topup Mitra
        $topupMitra = DB::table('topup_mitra as t')
            ->join('mitra as m', 't.id_mitra', '=', 'm.id_mitra')
            ->select(
                't.id_topup as id',
                DB::raw("'mitra' as tipe_user"),
                'm.nama_asli as nama_user',
                'm.no_wa',
                't.jumlah_topup as nominal',
                't.kode_unik',
                't.total_transfer',
                't.bank_tujuan',
                't.status_topup as status',
                't.tanggal as waktu'
            )
            ->where('t.status_topup', 'Pending')
            ->orderBy('t.tanggal', 'desc')
            ->get();

        // Topup Pelanggan
        $topupPelanggan = DB::table('dompet_pelanggan as d')
            ->join('pelanggans as p', 'd.id_pelanggan', '=', 'p.id_pelanggan')
            ->select(
                'd.id',
                DB::raw("'pelanggan' as tipe_user"),
                'p.nama_pelanggan as nama_user',
                'p.no_wa',
                'd.nominal',
                'd.kode_unik',
                'd.total_transfer',
                'd.bank_tujuan',
                'd.status',
                'd.waktu_request as waktu'
            )
            ->where('d.status', 'pending')
            ->orderBy('d.waktu_request', 'desc')
            ->get();

        // Gabungkan dan urutkan berdasarkan waktu terbaru
        $topups = $topupMitra->merge($topupPelanggan)->sortByDesc('waktu')->values();

        return view('admin.finance.topup_confirm', compact('topups'));
    }

    /**
     * Proses konfirmasi topup mitra (setuju/tolak).
     */
    public function processMitra(Request $request, $id)
    {
        $aksi   = $request->query('aksi');
        $status = ($aksi == 'setuju') ? 'Selesai' : 'Expired';

        $topup = DB::table('topup_mitra')->where('id_topup', $id)->first();

        if (!$topup || $topup->status_topup !== 'Pending') {
            return redirect()->route('admin.finance.topup.index')
                ->with('notif', 'Transaksi mitra tidak ditemukan atau sudah diproses.');
        }

        DB::transaction(function () use ($id, $status, $topup, $aksi) {
            DB::table('topup_mitra')->where('id_topup', $id)->update([
                'status_topup' => $status,
                'updated_at'   => now(),
            ]);

            if ($aksi === 'setuju') {
                // Tambah saldo mitra (gunakan total_transfer termasuk kode unik)
                $saldoMasuk = $topup->total_transfer ?? $topup->jumlah_topup;
                Mitra::where('id_mitra', $topup->id_mitra)->increment('saldo', $saldoMasuk);
            }
        });

        $saldoMasuk = $topup->total_transfer ?? $topup->jumlah_topup;
        $pesan = ($aksi == 'setuju')
            ? 'Top-up MITRA berhasil disetujui, saldo telah ditambahkan Rp ' . number_format($saldoMasuk, 0, ',', '.') . '.'
            : 'Top-up mitra telah ditolak.';

        return redirect()->route('admin.finance.topup.index')->with('notif', $pesan);
    }

    /**
     * Proses konfirmasi topup pelanggan (setuju/tolak).
     */
    public function processPelanggan(Request $request, $id)
    {
        $aksi   = $request->query('aksi');
        $status = ($aksi == 'setuju') ? 'sukses' : 'batal';

        $topup = DB::table('dompet_pelanggan')->where('id', $id)->first();

        if (!$topup || $topup->status !== 'pending') {
            return redirect()->route('admin.finance.topup.index')
                ->with('notif', 'Transaksi pelanggan tidak ditemukan atau sudah diproses.');
        }

        DB::transaction(function () use ($id, $status, $topup, $aksi) {
            DB::table('dompet_pelanggan')->where('id', $id)->update([
                'status'     => $status,
                'updated_at' => now(),
            ]);

            if ($aksi === 'setuju') {
                // Tambah saldo pelanggan (gunakan total_transfer termasuk kode unik)
                $saldoMasuk = $topup->total_transfer ?? $topup->nominal;
                Pelanggan::where('id_pelanggan', $topup->id_pelanggan)->increment('saldo', $saldoMasuk);
            }
        });

        $saldoMasuk = $topup->total_transfer ?? $topup->nominal;
        $pesan = ($aksi == 'setuju')
            ? 'Top-up PELANGGAN berhasil disetujui, saldo telah ditambahkan Rp ' . number_format($saldoMasuk, 0, ',', '.') . '.'
            : 'Top-up pelanggan telah ditolak.';

        return redirect()->route('admin.finance.topup.index')->with('notif', $pesan);
    }

    /**
     * Backward-compatible: redirect old process route.
     */
    public function process(Request $request, $id)
    {
        return $this->processMitra($request, $id);
    }
}
