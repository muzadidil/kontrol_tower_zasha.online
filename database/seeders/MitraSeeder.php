<?php

namespace Database\Seeders;

use App\Models\Mitra;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MitraSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = ['Kuli', 'Teknisi', 'Jasa'];
        
        for ($i = 1; $i <= 10; $i++) {
            $cat = $kategori[array_rand($kategori)];
            Mitra::create([
                'id' => 'ZSH-MTR-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nama_panggilan' => 'Mitra ' . $i,
                'usia' => rand(20, 50),
                'kategori' => $cat,
                'deskripsi_singkat' => 'Deskripsi untuk ' . $cat,
                'nomor_wa' => '0812345678' . $i,
                'saldo' => rand(100000, 500000),
                'tarif_per_jam' => rand(10000, 50000),
                'tarif_per_hari' => rand(100000, 500000),
                'status' => 'Aktif',
            ]);
        }
    }
}
