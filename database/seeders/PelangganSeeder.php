<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Pelanggan::create([
                'id' => 'ZSH-PLG-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nama_lengkap' => 'Pelanggan ' . $i,
                'nomor_wa' => '0812345678' . $i,
                'alamat_utama' => 'Jl. Contoh No. ' . $i,
                'saldo' => 500000,
            ]);
        }
    }
}
