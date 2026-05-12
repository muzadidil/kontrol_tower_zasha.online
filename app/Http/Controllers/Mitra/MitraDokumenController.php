<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\OrderDokumen;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MitraDokumenController extends Controller
{
    /**
     * List dokumen per tracking_id (urut: final dulu, lalu terbaru).
     */
    public function index(OrderTracking $tracking)
    {
        $this->authorize_($tracking);

        $tracking->load('mitra:id_mitra,nama_panggilan');
        $dokumens = OrderDokumen::where('tracking_id', $tracking->id)
            ->orderByDesc('is_final')
            ->orderByDesc('created_at')
            ->get();

        return view('mitra.dokumen.index', compact('tracking', 'dokumens'));
    }

    /**
     * Upload dokumen baru.
     */
    public function upload(Request $request, OrderTracking $tracking)
    {
        $this->authorize_($tracking);
        $mitra = Auth::guard('mitra')->user();

        $data = $request->validate([
            'judul'    => 'required|string|max:200',
            'file'     => 'required|file|max:25600', // 25MB max
            'catatan'  => 'nullable|string|max:500',
            'is_final' => 'nullable|boolean',
        ]);

        $file = $request->file('file');
        $path = $file->store(
            "dokumen-order/{$mitra->id_mitra}/tracking-{$tracking->id}",
            'public'
        );

        OrderDokumen::create([
            'tracking_id' => $tracking->id,
            'mitra_id'    => $mitra->id_mitra,
            'judul'       => $data['judul'],
            'file_path'   => $path,
            'mime_type'   => $file->getClientMimeType(),
            'size_bytes'  => $file->getSize(),
            'catatan'     => $data['catatan'] ?? null,
            'is_final'    => $request->boolean('is_final'),
        ]);

        return redirect()->route('mitra.dokumen.index', $tracking->id)
            ->with('success', "Dokumen \"{$data['judul']}\" berhasil diupload.");
    }

    /**
     * Toggle status final.
     */
    public function toggleFinal(OrderDokumen $dokumen)
    {
        $this->authorizeDokumen($dokumen);
        $dokumen->update(['is_final' => !$dokumen->is_final]);
        return back()->with('success', 'Status final diperbarui.');
    }

    /**
     * Hapus dokumen.
     */
    public function destroy(OrderDokumen $dokumen)
    {
        $this->authorizeDokumen($dokumen);

        if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }
        $dokumen->delete();

        return back()->with('success', 'Dokumen dihapus.');
    }

    private function authorize_(OrderTracking $tracking): void
    {
        $mitraId = Auth::guard('mitra')->user()->id_mitra;
        if ((int) $tracking->mitra_id !== (int) $mitraId) {
            abort(403, 'Anda tidak punya akses ke order ini.');
        }
    }

    private function authorizeDokumen(OrderDokumen $dokumen): void
    {
        $mitraId = Auth::guard('mitra')->user()->id_mitra;
        if ((int) $dokumen->mitra_id !== (int) $mitraId) {
            abort(403, 'Anda tidak punya akses ke dokumen ini.');
        }
    }
}
