<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== MEMBUAT DATA DUMMY TOP-UP ===\n\n";

// Cek apakah ada pelanggan
$pelanggan = DB::table('pelanggans')->first();

if (!$pelanggan) {
    echo "❌ Tidak ada data pelanggan. Silakan buat pelanggan terlebih dahulu.\n";
    exit;
}

echo "✓ Menggunakan Pelanggan ID: {$pelanggan->id_pelanggan}\n";
echo "  Nama: {$pelanggan->nama_pelanggan}\n\n";

// Buat 3 data dummy dengan kode unik
$dummyData = [
    ['nominal' => 100000, 'status' => 'pending'],
    ['nominal' => 250000, 'status' => 'sukses'],
    ['nominal' => 50000, 'status' => 'pending'],
];

foreach ($dummyData as $data) {
    $kode_unik = rand(100, 999);
    $total_transfer = $data['nominal'] + $kode_unik;
    
    DB::table('dompet_pelanggan')->insert([
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'nominal' => $data['nominal'],
        'kode_unik' => $kode_unik,
        'total_transfer' => $total_transfer,
        'bank_tujuan' => rand(0, 1) ? 'DANA' : 'BCA',
        'status' => $data['status'],
        'waktu_request' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✓ Dibuat: Nominal Rp " . number_format($data['nominal'], 0, ',', '.') . 
         " + Kode {$kode_unik} = Total Rp " . number_format($total_transfer, 0, ',', '.') . 
         " (Status: {$data['status']})\n";
}

echo "\n✅ Selesai! Data dummy berhasil dibuat.\n";
echo "Silakan refresh halaman dompet untuk melihat hasilnya.\n";
