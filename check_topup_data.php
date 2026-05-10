<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DATA DOMPET PELANGGAN ===\n\n";

$records = DB::table('dompet_pelanggan')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

if ($records->isEmpty()) {
    echo "Tidak ada data.\n";
} else {
    foreach ($records as $record) {
        echo "ID: {$record->id}\n";
        echo "Pelanggan ID: {$record->id_pelanggan}\n";
        echo "Nominal: " . ($record->nominal ?? 'NULL') . "\n";
        echo "Kode Unik: " . ($record->kode_unik ?? 'NULL') . "\n";
        echo "Total Transfer: " . ($record->total_transfer ?? 'NULL') . "\n";
        echo "Bank Tujuan: " . ($record->bank_tujuan ?? 'NULL') . "\n";
        echo "Status: {$record->status}\n";
        echo "Waktu: {$record->waktu_request}\n";
        echo str_repeat("-", 50) . "\n\n";
    }
}

echo "\nTotal records: " . DB::table('dompet_pelanggan')->count() . "\n";
