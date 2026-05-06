<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Mitra;
use App\Models\Pelanggan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $mitras = Mitra::all();
        $pelanggans = Pelanggan::all();

        for ($i = 1; $i <= 10; $i++) {
            $mitra = $mitras->random();
            $pelanggan = $pelanggans->random();
            $biaya = rand(50000, 300000);
            
            Order::create([
                'id' => 'ZSH-ORD-' . Str::uuid(),
                'pelanggan_id' => $pelanggan->id,
                'mitra_id' => $mitra->id,
                'tipe_waktu' => 'Instan',
                'status' => 'Selesai',
                'metode_pembayaran' => 'Saldo',
                'durasi_kerja' => rand(1, 5),
                'total_biaya' => $biaya,
                'komisi_zasha' => $biaya * 0.1,
            ]);
        }
    }
}
