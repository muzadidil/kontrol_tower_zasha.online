<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MitraDashboardController extends Controller
{
    private function mitra()
    {
        return Auth::guard('mitra')->user();
    }

    public function dashboard()
    {
        $mitra        = $this->mitra();
        $totalPesanan = DB::table('pesanan_mitra')->where('id_mitra', $mitra->id_mitra)->count();
        $pesananAktif = DB::table('pesanan_mitra')
                          ->where('id_mitra', $mitra->id_mitra)
                          ->whereNotIn('status_pesanan', ['Selesai', 'Batal', 'Dibatalkan'])
                          ->count();

        return view('mitra.dashboard', compact('mitra', 'totalPesanan', 'pesananAktif'));
    }

    public function pesanan()
    {
        $mitra   = $this->mitra();
        $pesanan = DB::table('pesanan_mitra')
                     ->where('id_mitra', $mitra->id_mitra)
                     ->orderByDesc('created_at')
                     ->get();

        return view('mitra.pesanan', compact('mitra', 'pesanan'));
    }

    public function saldo()
    {
        $mitra   = $this->mitra();
        $riwayat = DB::table('withdrawals')
                     ->where('mitra_id', $mitra->id_mitra)
                     ->orderByDesc('created_at')
                     ->get();

        $riwayatTopup = DB::table('topup_mitra')
                          ->where('id_mitra', $mitra->id_mitra)
                          ->orderByDesc('tanggal')
                          ->get();

        return view('mitra.saldo', compact('mitra', 'riwayat', 'riwayatTopup'));
    }

    public function topup(Request $request)
    {
        $request->validate([
            'nominal'     => 'required|numeric|min:10000|max:10000000',
            'bank_tujuan' => 'required|in:DANA,BCA',
        ]);

        $mitra = $this->mitra();

        // Bersihkan format nominal (hapus titik, koma, spasi) lalu cast ke integer
        $rawNominal = $request->input('nominal');
        $nominal    = (int) preg_replace('/[^0-9]/', '', $rawNominal);

        // Pastikan nominal valid setelah parsing
        if ($nominal < 10000) {
            return back()->withErrors(['nominal' => 'Nominal minimal Rp 10.000'])->withInput();
        }

        // Generate kode unik 3 digit (100-999) untuk identifikasi transfer
        $kode_unik = rand(100, 999);

        // Hitung total transfer = nominal + kode_unik
        $total_transfer = $nominal + $kode_unik;

        DB::table('topup_mitra')->insert([
            'id_mitra'       => $mitra->id_mitra,
            'jumlah_topup'   => $nominal,
            'kode_unik'      => $kode_unik,
            'total_transfer' => $total_transfer,
            'bank_tujuan'    => $request->input('bank_tujuan'),
            'status_topup'   => 'Pending',
            'tanggal'        => now(),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return back()
            ->with('notif_topup', 'sukses')
            ->with('data_nominal', $nominal)
            ->with('data_kode_unik', $kode_unik)
            ->with('data_transfer', $total_transfer)
            ->with('data_bank', $request->input('bank_tujuan'));
    }

    public function profil()
    {
        $mitra = $this->mitra();
        return view('mitra.profil', compact('mitra'));
    }

    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $mitra = $this->mitra();

        try {
            $file     = $request->file('foto');
            $ext      = strtolower($file->getClientOriginalExtension() ?: $file->extension());
            $filename = 'profil_' . $mitra->id_mitra . '_' . time() . '.' . $ext;
            $path     = $file->storeAs('profil-mitra', $filename, 'public');

            $oldPath = $mitra->foto_mitra;
            $mitra->update(['foto_mitra' => $path]);

            if ($oldPath && $oldPath !== $path && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        } catch (\Throwable $e) {
            Log::error('Upload foto mitra gagal', ['message' => $e->getMessage(), 'mitra_id' => $mitra->id_mitra]);
            return back()->with('error', 'Gagal menyimpan foto. Coba lagi.');
        }

        return back()->with('success', 'Foto profil berhasil diperbarui');
    }

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:online,offline',
        ]);

        $mitra = $this->mitra();
        $newStatus = $request->input('status');

        try {
            $mitra->update(['status_online' => $newStatus]);

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => $newStatus === 'online' ? 'Status diubah ke online' : 'Status diubah ke offline',
            ]);
        } catch (\Exception $e) {
            Log::error('Toggle mitra status gagal', [
                'message' => $e->getMessage(),
                'mitra_id' => $mitra->id_mitra,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status',
            ], 500);
        }
    }
}
