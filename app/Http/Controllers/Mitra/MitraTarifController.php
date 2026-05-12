<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\TarifMitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MitraTarifController extends Controller
{
    /** Daftar semua tarif milik mitra yang login. */
    public function index()
    {
        $mitra = Auth::guard('mitra')->user();
        $tarifs = TarifMitra::where('mitra_id', $mitra->id_mitra)
            ->orderByDesc('is_aktif')
            ->orderBy('keterangan')
            ->get();

        $komisiPersen = Setting::komisiPersen();

        return view('mitra.tarif.index', compact('mitra', 'tarifs', 'komisiPersen'));
    }

    /** Form tambah tarif baru. */
    public function create()
    {
        $mitra = Auth::guard('mitra')->user();
        $komisiPersen = Setting::komisiPersen();
        return view('mitra.tarif.create', compact('mitra', 'komisiPersen'));
    }

    /** Simpan tarif baru. */
    public function store(Request $request)
    {
        $mitra = Auth::guard('mitra')->user();

        $data = $request->validate([
            'keterangan' => 'required|string|max:255',
            'nominal'    => 'required|numeric|min:1000|max:99999999',
            'satuan'     => 'required|string|max:50',
            'is_aktif'   => 'nullable|boolean',
        ]);

        TarifMitra::create([
            'mitra_id'   => $mitra->id_mitra,
            'keterangan' => $data['keterangan'],
            'nominal'    => $data['nominal'],
            'satuan'     => $data['satuan'],
            'is_aktif'   => $request->boolean('is_aktif', true),
        ]);

        return redirect()->route('mitra.tarif.index')
            ->with('success', 'Tarif berhasil ditambahkan.');
    }

    /** Form edit tarif. */
    public function edit(TarifMitra $tarif)
    {
        $this->authorizeTarif($tarif);
        $mitra = Auth::guard('mitra')->user();
        $komisiPersen = Setting::komisiPersen();
        return view('mitra.tarif.edit', compact('mitra', 'tarif', 'komisiPersen'));
    }

    /** Update tarif. */
    public function update(Request $request, TarifMitra $tarif)
    {
        $this->authorizeTarif($tarif);

        $data = $request->validate([
            'keterangan' => 'required|string|max:255',
            'nominal'    => 'required|numeric|min:1000|max:99999999',
            'satuan'     => 'required|string|max:50',
            'is_aktif'   => 'nullable|boolean',
        ]);

        $tarif->update([
            'keterangan' => $data['keterangan'],
            'nominal'    => $data['nominal'],
            'satuan'     => $data['satuan'],
            'is_aktif'   => $request->boolean('is_aktif', false),
        ]);

        return redirect()->route('mitra.tarif.index')
            ->with('success', 'Tarif berhasil diperbarui.');
    }

    /** Toggle status aktif/nonaktif (quick action). */
    public function toggle(TarifMitra $tarif)
    {
        $this->authorizeTarif($tarif);
        $tarif->update(['is_aktif' => !$tarif->is_aktif]);
        return back()->with('success', 'Status tarif diperbarui.');
    }

    /** Hapus tarif. */
    public function destroy(TarifMitra $tarif)
    {
        $this->authorizeTarif($tarif);
        $tarif->delete();
        return redirect()->route('mitra.tarif.index')
            ->with('success', 'Tarif berhasil dihapus.');
    }

    /** Pastikan tarif milik mitra yang login. */
    private function authorizeTarif(TarifMitra $tarif): void
    {
        $mitraId = Auth::guard('mitra')->user()->id_mitra;
        if ((int) $tarif->mitra_id !== (int) $mitraId) {
            abort(403, 'Anda tidak punya akses ke tarif ini.');
        }
    }
}
