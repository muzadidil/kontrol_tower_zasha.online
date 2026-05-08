<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        $alamat = Alamat::where('id_pelanggan', $pelanggan->id)->get();
        return view('pelanggan.profil', compact('pelanggan', 'alamat'));
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $pelanggan = Auth::guard('pelanggan')->user();
        $pelanggan->update($request->only(['nama_pelanggan', 'email']));

        return back()->with('success', 'Profil berhasil diupdate');
    }

    public function uploadFoto(Request $request)
    {
        $request->validate(['foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048']);
        
        $pelanggan = Auth::guard('pelanggan')->user();
        
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('profil', 'public');
            $pelanggan->update(['foto' => $path]);
        }

        return back()->with('success', 'Foto berhasil diupload');
    }

    public function storeAlamat(Request $request)
    {
        $request->validate([
            'alamat' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        Alamat::create([
            'id_pelanggan' => Auth::guard('pelanggan')->id(),
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_utama' => $request->has('is_utama') ? 1 : 0,
        ]);

        return back()->with('success', 'Alamat berhasil ditambah');
    }
}
