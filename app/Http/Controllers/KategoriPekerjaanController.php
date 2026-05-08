<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriPekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $id_edit = $request->query('edit');
        $kategori_edit = null;

        if ($id_edit) {
            $kategori_edit = DB::table('kategori_pekerjaan')->where('id_kategori', $id_edit)->first();
        }

        $semua_kategori = DB::table('kategori_pekerjaan')->orderBy('id_kategori', 'desc')->get();

        return view('admin.kategori.index', compact('semua_kategori', 'kategori_edit'));
    }

    public function store(Request $request)
    {
        $data = [
            'nama_kategori' => $request->nama,
            'svg_kategori' => $request->svg,
            'warna_tema' => $request->warna,
            'status' => $request->status,
        ];

        if ($request->id_kategori) {
            DB::table('kategori_pekerjaan')->where('id_kategori', $request->id_kategori)->update($data);
            $pesan = "Data berhasil diupdate!";
        } else {
            DB::table('kategori_pekerjaan')->insert($data);
            $pesan = "Data berhasil ditambahkan!";
        }

        return redirect()->route('admin.kategori.index')->with('pesan_notif', $pesan);
    }

    public function destroy($id)
    {
        DB::table('kategori_pekerjaan')->where('id_kategori', $id)->delete();
        return redirect()->route('admin.kategori.index')->with('pesan_notif', 'Kategori berhasil dihapus.');
    }
}
