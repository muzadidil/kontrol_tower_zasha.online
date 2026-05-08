<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminVerificationController extends Controller
{
    public function index()
    {
        $list_driver = DB::table('mitra_jastip')->where('status_verifikasi', 'Pending')->get();
        $list_mitra = DB::table('mitra')
            ->where('status_verifikasi', 'Pending')
            ->orWhereNotNull('plat_pengajuan')
            ->get();

        return view('admin.verification.index', compact('list_driver', 'list_mitra'));
    }

    public function approve(Request $request)
    {
        $id = $request->target_id;
        $type = $request->account_type;

        if ($type == 'driver') {
            $d = DB::table('mitra_jastip')->where('id_driver', $id)->first();
            
            DB::table('mitra_jastip')->where('id_driver', $id)->update([
                'nama_asli' => $d->nama_asli_baru ?? $d->nama_asli,
                'no_wa' => $d->no_wa_baru ?? $d->no_wa,
                'plat_nomor' => $d->plat_pengajuan ?? $d->plat_nomor,
                'nama_asli_baru' => null,
                'no_wa_baru' => null,
                'plat_pengajuan' => null,
                'status_verifikasi' => 'Verified',
                'status_plat' => 'Normal'
            ]);
        } else {
            $d = DB::table('mitra')->where('id_mitra', $id)->first();
            
            DB::table('mitra')->where('id_mitra', $id)->update([
                'nama_asli' => $d->nama_asli_baru ?? $d->nama_asli,
                'no_wa' => $d->no_wa_baru ?? $d->no_wa,
                'jenis_kendaraan' => $d->kendaraan_baru ?? $d->jenis_kendaraan,
                'warna_kendaraan' => $d->warna_baru ?? $d->warna_kendaraan,
                'plat_nomor' => $d->plat_pengajuan ?? $d->plat_nomor,
                'alamat' => $d->alamat_pengajuan ?? $d->alamat,
                'lat_mitra' => ($d->lat_pengajuan != 0) ? $d->lat_pengajuan : $d->lat_mitra,
                'lng_mitra' => ($d->lng_pengajuan != 0) ? $d->lng_pengajuan : $d->lng_mitra,
                'nama_asli_baru' => null,
                'no_wa_baru' => null,
                'kendaraan_baru' => null,
                'warna_baru' => null,
                'plat_pengajuan' => null,
                'alamat_pengajuan' => null,
                'lat_pengajuan' => 0,
                'lng_pengajuan' => 0,
                'status_verifikasi' => 'Verified'
            ]);
        }

        return redirect()->back()->with('notif_verif', "Sukses! Akun #$id Berhasil diverifikasi.");
    }
}
