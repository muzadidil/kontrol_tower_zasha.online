<?php

/**
 * Script untuk update data top-up lama yang belum memiliki kode_unik
 * Jalankan dengan: php update_existing_topup_data.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Mengupdate data top-up lama...\n\n";

// Ambil semua data yang belum punya kode_unik
$records = DB::table('dompet_pelanggan')
    ->whereNull('kode_unik')
    ->get();

echo "Ditemukan " . $records->count() . " record yang perlu diupdate.\n\n";

foreach ($records as $record) {
    // Generate kode unik untuk data lama
    $kode_unik = rand(100, 999);
    $total_transfer = $record->nominal + $kode_unik;
    
    // Update record
    DB::table('dompet_pelanggan')
        ->where('id', $record->id)
        ->update([
            'kode_unik' => $kode_unik,
            'total_transfer' => $total_transfer,
            'bank_tujuan' => 'DANA', // Default untuk data lama
        ]);
    
    echo "✓ ID {$record->id}: Nominal Rp " . number_format($record->nominal, 0, ',', '.') . 
         " → Total Transfer Rp " . number_format($total_transfer, 0, ',', '.') . 
         " (Kode: {$kode_unik})\n";
}

echo "\n✅ Selesai! Semua data lama sudah diupdate.\n";
