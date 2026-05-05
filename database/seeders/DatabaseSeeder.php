<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mitra;
use App\Models\Pelanggan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Pelanggan::create([
            'nama_lengkap' => 'Budi Santoso',
            'nomor_wa' => '081234567890',
            'alamat_utama' => 'Jl. Merdeka No. 1, Jakarta',
            'saldo_pelanggan' => 100000
        ]);

        Mitra::create([
            'nama_panggilan' => 'Pak Asep',
            'usia' => 35,
            'kategori' => 'Teknisi',
            'deskripsi_singkat' => 'Ahli servis AC dan kulkas',
            'nomor_wa' => '089876543210',
            'saldo_mitra' => 50000,
            'tarif_per_jam' => 50000,
            'tarif_per_hari' => 300000,
            'status' => 'Aktif'
        ]);
    }
}
