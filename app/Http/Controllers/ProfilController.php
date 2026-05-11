<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $user    = Auth::guard('pelanggan')->user();
        $alamats = Alamat::where('id_pelanggan', $user->id_pelanggan)->orderBy('is_utama', 'desc')->get();
        return view('pelanggan.profil', compact('user', 'alamats'));
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'no_wa'          => 'required|string|max:20',
        ]);

        $pelanggan = Auth::guard('pelanggan')->user();
        $pelanggan->update([
            'nama_pelanggan' => $request->nama_pelanggan,
            'no_wa'          => '62' . ltrim($request->no_wa, '0'),
        ]);

        return back()->with('success', 'Profil berhasil diupdate');
    }

    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $pelanggan = Auth::guard('pelanggan')->user();

        try {
            $file     = $request->file('foto');
            $ext      = strtolower($file->getClientOriginalExtension() ?: $file->extension());
            $filename = 'profil_' . $pelanggan->id_pelanggan . '_' . time() . '.' . $ext;
            $path     = $file->storeAs('profil', $filename, 'public');

            $oldPath = $pelanggan->foto;
            $pelanggan->update(['foto' => $path]);

            if ($oldPath && $oldPath !== $path && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        } catch (\Throwable $e) {
            Log::error('Upload foto pelanggan gagal', ['message' => $e->getMessage(), 'pelanggan_id' => $pelanggan->id_pelanggan]);
            return back()->with('error', 'Gagal menyimpan foto. Coba lagi.');
        }

        return back()->with('success', 'Foto profil berhasil diperbarui');
    }

    public function storeAlamat(Request $request)
    {
        $request->validate([
            'label_alamat'   => 'required|string|max:100',
            'nama_penerima'  => 'required|string|max:100',
            'no_wa_penerima' => 'required|string|max:20',
            'alamat_lengkap' => 'required|string',
        ]);

        $pelanggan_id = Auth::guard('pelanggan')->id();

        if ($request->boolean('is_utama')) {
            Alamat::where('id_pelanggan', $pelanggan_id)->update(['is_utama' => false]);
        }

        Alamat::create([
            'id_pelanggan'   => $pelanggan_id,
            'label_alamat'   => $request->label_alamat,
            'nama_penerima'  => $request->nama_penerima,
            'no_wa_penerima' => $request->no_wa_penerima,
            'alamat_lengkap' => $request->alamat_lengkap,
            'is_utama'       => $request->boolean('is_utama'),
        ]);

        return back()->with('success', 'Alamat berhasil ditambah');
    }
}
