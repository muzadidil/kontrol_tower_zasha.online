<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{MasterLayanan, Mitra, MitraLayanan};
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function kategori()
    {
        return response()->json([
            ['kode' => 'TNG', 'nama' => 'Jasa Tenaga'],
            ['kode' => 'WFH', 'nama' => 'WFH / Digital'],
            ['kode' => 'JST', 'nama' => 'Jastip / Kurir'],
            ['kode' => 'SVC', 'nama' => 'Service / Teknisi'],
        ]);
    }

    public function layananByKategori(string $kategori)
    {
        $layanans = MasterLayanan::where('kategori_mitra', $kategori)
            ->where('is_active', true)
            ->get();
        return response()->json($layanans);
    }

    public function mitraByLayanan(MasterLayanan $masterLayanan)
    {
        $mitraLayanans = MitraLayanan::where('master_layanan_id', $masterLayanan->id)
            ->where('is_active', true)
            ->with('mitra')
            ->get();
        return response()->json($mitraLayanans);
    }

    public function mitraDetail(Mitra $mitra)
    {
        $mitra->load('layanans.masterLayanan');
        return response()->json($mitra);
    }
}
