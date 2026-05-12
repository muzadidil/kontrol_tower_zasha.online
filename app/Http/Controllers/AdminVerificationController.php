<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\MitraVerifikasi;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminVerificationController extends Controller
{
    public function index()
    {
        // Mitra Jastip (driver) — filter via kategori_kode = 'JST'
        $list_driver = DB::table('mitra')
            ->where('kategori_kode', 'JST')
            ->where('status_verifikasi', 'pending_review')
            ->get();

        // Mitra non-jastip dengan pengajuan perubahan data
        $list_mitra = DB::table('mitra')
            ->where(function ($q) {
                $q->where('status_verifikasi', 'pending_review')
                  ->orWhereNotNull('plat_pengajuan');
            })
            ->where(function ($q) {
                $q->where('kategori_kode', '!=', 'JST')
                  ->orWhereNull('kategori_kode');
            })
            ->get();

        // Mitra dengan dokumen pending di mitra_verifikasi (sistem role-based)
        $list_dokumen = Mitra::whereHas('verifikasiDokumens', function ($q) {
                $q->where('status', MitraVerifikasi::STATUS_PENDING);
            })
            ->with(['role:id,name,icon,icon_color', 'verifikasiDokumens'])
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($mitra) {
                $mitra->pending_count = $mitra->verifikasiDokumens
                    ->where('status', MitraVerifikasi::STATUS_PENDING)
                    ->count();
                return $mitra;
            });

        return view('admin.verification.index', compact('list_driver', 'list_mitra', 'list_dokumen'));
    }

    /**
     * Halaman review semua dokumen verifikasi untuk satu mitra.
     */
    public function reviewDokumen(Mitra $mitra)
    {
        $mitra->load(['role.verifikasiKeys', 'verifikasiDokumens']);

        $requiredKeys = $mitra->role
            ? $mitra->role->verifikasiKeys
            : collect();

        $uploaded = $mitra->verifikasiDokumens->keyBy('verifikasi_key');

        return view('admin.verification.review', compact('mitra', 'requiredKeys', 'uploaded'));
    }

    /**
     * Approve atau tolak satu dokumen verifikasi mitra.
     */
    public function actionDokumen(Request $request, MitraVerifikasi $dokumen)
    {
        $data = $request->validate([
            'action'  => 'required|in:approve,tolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $dokumen->update([
            'status'  => $data['action'] === 'approve'
                ? MitraVerifikasi::STATUS_APPROVED
                : MitraVerifikasi::STATUS_DITOLAK,
            'catatan' => $data['catatan'] ?? null,
        ]);

        // Cek apakah semua dokumen wajib mitra sudah approved → aktifkan mitra
        $this->autoActivateIfReady($dokumen->mitra);

        $label = Role::ALL_VERIFIKASI[$dokumen->verifikasi_key] ?? $dokumen->verifikasi_key;
        $msg = $data['action'] === 'approve'
            ? "Dokumen {$label} disetujui."
            : "Dokumen {$label} ditolak.";

        return back()->with('notif_verif', $msg);
    }

    /**
     * Auto-aktivasi mitra bila semua dokumen wajib sudah disetujui.
     */
    private function autoActivateIfReady(Mitra $mitra): void
    {
        $mitra->load(['role.verifikasiKeys', 'verifikasiDokumens']);

        if (!$mitra->role) {
            return;
        }

        $wajibKeys = $mitra->role->verifikasiKeys
            ->where('wajib', true)
            ->pluck('verifikasi_key');

        if ($wajibKeys->isEmpty()) {
            return;
        }

        $approved = $mitra->verifikasiDokumens
            ->where('status', MitraVerifikasi::STATUS_APPROVED)
            ->pluck('verifikasi_key');

        $allApproved = $wajibKeys->every(fn($k) => $approved->contains($k));

        if ($allApproved) {
            $current = $mitra->status_verifikasi;
            $isAlreadyActive = ($current instanceof \App\Enums\MitraStatus && $current->isActive())
                || (is_string($current) && in_array($current, ['active', 'verified'], true));

            if (!$isAlreadyActive) {
                $mitra->update(['status_verifikasi' => 'active']);
            }
        }
    }

    public function approve(Request $request)
    {
        $request->validate([
            'target_id'    => 'required|integer',
            'account_type' => 'required|in:driver,mitra',
        ]);

        $id = (int) $request->target_id;

        try {
            $d = DB::table('mitra')->where('id_mitra', $id)->first();
            if (! $d) {
                return back()->with('notif_verif', "Mitra #{$id} tidak ditemukan.");
            }

            DB::table('mitra')->where('id_mitra', $id)->update([
                'nama_asli'         => $d->nama_asli_baru ?? $d->nama_asli,
                'no_wa'             => $d->no_wa_baru ?? $d->no_wa,
                'jenis_kendaraan'   => $d->kendaraan_baru ?? $d->jenis_kendaraan,
                'warna_kendaraan'   => $d->warna_baru ?? $d->warna_kendaraan,
                'plat_nomor'        => $d->plat_pengajuan ?? $d->plat_nomor,
                'alamat'            => $d->alamat_pengajuan ?? $d->alamat,
                'lat_mitra'         => ($d->lat_pengajuan != 0) ? $d->lat_pengajuan : $d->lat_mitra,
                'lng_mitra'         => ($d->lng_pengajuan != 0) ? $d->lng_pengajuan : $d->lng_mitra,
                'nama_asli_baru'    => null,
                'no_wa_baru'        => null,
                'kendaraan_baru'    => null,
                'warna_baru'        => null,
                'plat_pengajuan'    => null,
                'alamat_pengajuan'  => null,
                'lat_pengajuan'     => 0,
                'lng_pengajuan'     => 0,
                'status_verifikasi' => 'active',
            ]);

            return redirect()->back()->with('notif_verif', "Sukses! Akun #{$id} berhasil diverifikasi.");
        } catch (\Exception $e) {
            Log::error('Admin verify approve error', [
                'message'      => $e->getMessage(),
                'target_id'    => $id,
                'account_type' => $request->account_type,
            ]);
            return back()->with('notif_verif', 'Terjadi kesalahan saat verifikasi.');
        }
    }
}
