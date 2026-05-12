<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\MitraVerifikasi;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MitraVerifikasiController extends Controller
{
    /**
     * Halaman status verifikasi mitra.
     * Mitra yang belum verified diarahkan ke sini oleh middleware verified.mitra.
     */
    public function status()
    {
        $mitra = Auth::guard('mitra')->user();
        if (!$mitra) {
            return redirect()->route('mitra.login');
        }

        $mitra->load(['role.verifikasiKeys', 'verifikasiDokumens']);

        $requiredKeys = collect();
        if ($mitra->role) {
            $requiredKeys = $mitra->role->verifikasiKeys;
        }

        $uploaded = $mitra->verifikasiDokumens->keyBy('verifikasi_key');

        return view('mitra.verifikasi.status', compact('mitra', 'requiredKeys', 'uploaded'));
    }

    /**
     * Upload file dokumen verifikasi atau update link (untuk github/portfolio link).
     */
    public function upload(Request $request)
    {
        $mitra = Auth::guard('mitra')->user();
        if (!$mitra) {
            return redirect()->route('mitra.login');
        }

        $request->validate([
            'verifikasi_key' => 'required|string|in:' . implode(',', array_keys(Role::ALL_VERIFIKASI)),
            'file'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'link'           => 'nullable|url|max:500',
        ]);

        $key = $request->input('verifikasi_key');

        if (!$request->hasFile('file') && !$request->filled('link')) {
            return back()->with('error', 'Wajib upload file atau isi link.');
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store(
                "verifikasi/{$mitra->id_mitra}",
                'public'
            );
        } elseif ($request->filled('link')) {
            $filePath = $request->input('link');
        }

        // Replace existing record bila ada (reset ke pending agar admin review ulang).
        $existing = MitraVerifikasi::where('mitra_id', $mitra->id_mitra)
            ->where('verifikasi_key', $key)
            ->first();

        if ($existing) {
            if ($existing->file_path
                && !filter_var($existing->file_path, FILTER_VALIDATE_URL)
                && Storage::disk('public')->exists($existing->file_path)
            ) {
                Storage::disk('public')->delete($existing->file_path);
            }

            $existing->update([
                'file_path' => $filePath,
                'status'    => MitraVerifikasi::STATUS_PENDING,
                'catatan'   => null,
            ]);
        } else {
            MitraVerifikasi::create([
                'mitra_id'       => $mitra->id_mitra,
                'verifikasi_key' => $key,
                'file_path'      => $filePath,
                'status'         => MitraVerifikasi::STATUS_PENDING,
            ]);
        }

        $label = Role::ALL_VERIFIKASI[$key] ?? $key;
        return back()->with('success', "Dokumen {$label} berhasil dikirim. Menunggu review admin.");
    }
}
