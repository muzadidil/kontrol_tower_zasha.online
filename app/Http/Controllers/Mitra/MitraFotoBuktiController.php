<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\OrderFotoBukti;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MitraFotoBuktiController extends Controller
{
    /**
     * Halaman galeri foto bukti per tracking_id (semua tipe).
     */
    public function index(OrderTracking $tracking)
    {
        $this->authorize_($tracking);

        $tracking->load('mitra:id_mitra,nama_panggilan');
        $foto = OrderFotoBukti::where('tracking_id', $tracking->id)
            ->orderBy('tipe')
            ->orderBy('created_at')
            ->get()
            ->groupBy('tipe');

        return view('mitra.foto-bukti.index', compact('tracking', 'foto'));
    }

    /**
     * Upload foto bukti (multiple files dalam satu request).
     */
    public function upload(Request $request, OrderTracking $tracking)
    {
        $this->authorize_($tracking);
        $mitra = Auth::guard('mitra')->user();

        $data = $request->validate([
            'tipe'      => 'required|in:sebelum,proses,sesudah',
            'foto'      => 'required|array|min:1|max:10',
            'foto.*'    => 'image|mimes:jpg,jpeg,png,webp|max:8192',
            'catatan'   => 'nullable|string|max:500',
        ]);

        $uploaded = 0;
        foreach ($request->file('foto') as $file) {
            $path = $file->store(
                "foto-bukti/{$mitra->id_mitra}/tracking-{$tracking->id}",
                'public'
            );

            OrderFotoBukti::create([
                'tracking_id' => $tracking->id,
                'mitra_id'    => $mitra->id_mitra,
                'tipe'        => $data['tipe'],
                'file_path'   => $path,
                'catatan'     => $data['catatan'] ?? null,
            ]);
            $uploaded++;
        }

        return redirect()->route('mitra.foto-bukti.index', $tracking->id)
            ->with('success', "{$uploaded} foto berhasil diupload.");
    }

    /**
     * Hapus foto.
     */
    public function destroy(OrderFotoBukti $foto)
    {
        $mitraId = Auth::guard('mitra')->user()->id_mitra;
        if ((int) $foto->mitra_id !== (int) $mitraId) {
            abort(403, 'Anda tidak punya akses ke foto ini.');
        }

        if ($foto->file_path && Storage::disk('public')->exists($foto->file_path)) {
            Storage::disk('public')->delete($foto->file_path);
        }
        $foto->delete();

        return back()->with('success', 'Foto dihapus.');
    }

    /**
     * Pastikan tracking milik mitra yang login.
     */
    private function authorize_(OrderTracking $tracking): void
    {
        $mitraId = Auth::guard('mitra')->user()->id_mitra;
        if ((int) $tracking->mitra_id !== (int) $mitraId) {
            abort(403, 'Anda tidak punya akses ke order ini.');
        }
    }
}
