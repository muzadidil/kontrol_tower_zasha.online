<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMasterKategoriController extends Controller
{
    public function index()
    {
        $categories = DB::table('kategori_pekerjaan')->orderBy('id_kategori', 'desc')->get();
        $units = DB::table('master_satuan')->orderBy('nama_satuan', 'asc')->get();

        return view('admin.kategori.master', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'satuan'        => 'required|string|max:100',
            'svg_kategori'  => 'nullable|string',
            'id_edit'       => 'nullable|integer',
            'f_label'       => 'nullable|array',
            'f_label.*'     => 'nullable|string|max:100',
            'f_value'       => 'nullable|array',
            'f_value.*'     => 'nullable|numeric',
        ]);

        // Olah Skema Tarif ke JSON
        $skema = [];
        if ($request->has('f_label')) {
            foreach ($request->f_label as $i => $label) {
                if (!empty($label)) {
                    $skema[] = ['label' => $label, 'default' => $request->f_value[$i] ?? ''];
                }
            }
        }

        $data = [
            'nama_kategori' => $request->nama_kategori,
            'satuan' => $request->satuan,
            'svg_kategori' => $request->svg_kategori,
            'skema_tarif' => json_encode($skema)
        ];

        if ($request->id_edit) {
            DB::table('kategori_pekerjaan')->where('id_kategori', $request->id_edit)->update($data);
        } else {
            DB::table('kategori_pekerjaan')->insert($data);
        }

        return redirect()->route('admin.master.kategori')->with('pesan', 'Kategori berhasil disimpan!');
    }

    public function destroy($id)
    {
        DB::table('kategori_pekerjaan')->where('id_kategori', $id)->delete();
        return redirect()->back()->with('pesan', 'Kategori dihapus.');
    }

    public function storeUnit(Request $request)
    {
        DB::table('master_satuan')->insert(['nama_satuan' => $request->nama_satuan]);
        return redirect()->back();
    }

    public function destroyUnit($id)
    {
        DB::table('master_satuan')->where('id_satuan', $id)->delete();
        return redirect()->back();
    }
}
