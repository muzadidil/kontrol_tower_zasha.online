<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlamatController extends Controller
{
    public function index()
    {
        $id      = Auth::guard('pelanggan')->id();
        $alamats = Alamat::where('id_pelanggan', $id)->orderBy('is_utama', 'desc')->get();
        return view('pelanggan.alamat.index', compact('alamats'));
    }

    public function store(Request $request)
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

        return back()->with('success', 'Alamat berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'label_alamat'   => 'required|string|max:100',
            'nama_penerima'  => 'required|string|max:100',
            'no_wa_penerima' => 'required|string|max:20',
            'alamat_lengkap' => 'required|string',
        ]);

        $pelanggan_id = Auth::guard('pelanggan')->id();
        $alamat       = Alamat::where('id', $id)->where('id_pelanggan', $pelanggan_id)->firstOrFail();

        if ($request->boolean('is_utama')) {
            Alamat::where('id_pelanggan', $pelanggan_id)->update(['is_utama' => false]);
        }

        $alamat->update($request->only([
            'label_alamat', 'nama_penerima', 'no_wa_penerima', 'alamat_lengkap', 'is_utama',
        ]));

        return back()->with('success', 'Alamat berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $alamat = Alamat::where('id', $id)
            ->where('id_pelanggan', Auth::guard('pelanggan')->id())
            ->firstOrFail();
        $alamat->delete();
        return back()->with('success', 'Alamat berhasil dihapus!');
    }

    public function setUtama($id)
    {
        $pelanggan_id = Auth::guard('pelanggan')->id();
        Alamat::where('id_pelanggan', $pelanggan_id)->update(['is_utama' => false]);
        Alamat::where('id', $id)->where('id_pelanggan', $pelanggan_id)->update(['is_utama' => true]);
        return back()->with('success', 'Alamat utama berhasil diubah!');
    }
}
