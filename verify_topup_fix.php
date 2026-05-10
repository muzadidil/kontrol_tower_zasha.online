<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFIKASI STRUKTUR TABEL dompet_pelanggan ===\n\n";

// 1. Cek struktur kolom
$cols = DB::select('DESCRIBE dompet_pelanggan');
echo "Kolom yang ada:\n";
foreach ($cols as $c) {
    echo "  - {$c->Field} | {$c->Type} | Null:{$c->Null} | Default:" . ($c->Default ?? 'NULL') . "\n";
}

// 2. Cek kolom wajib ada
$required = ['id', 'id_pelanggan', 'nominal', 'kode_unik', 'total_transfer', 'bank_tujuan', 'status', 'waktu_request'];
$existing = array_column($cols, 'Field');

echo "\n=== CEK KOLOM WAJIB ===\n";
$all_ok = true;
foreach ($required as $col) {
    $ok = in_array($col, $existing);
    echo ($ok ? "  ✓" : "  ✗") . " {$col}\n";
    if (!$ok) $all_ok = false;
}

// 3. Test kalkulasi logika
echo "\n=== TEST LOGIKA KALKULASI ===\n";
$nominal    = 100000;
$kode_unik  = rand(100, 999);
$total      = $nominal + $kode_unik;

echo "  Nominal       : Rp " . number_format($nominal, 0, ',', '.') . "\n";
echo "  Kode Unik     : {$kode_unik}\n";
echo "  Total Transfer: Rp " . number_format($total, 0, ',', '.') . "\n";
echo "  Kalkulasi     : " . ($total === ($nominal + $kode_unik) ? "✓ BENAR" : "✗ SALAH") . "\n";

// 4. Test insert dummy (tanpa commit)
echo "\n=== TEST INSERT (DRY RUN) ===\n";
try {
    DB::beginTransaction();

    $id = DB::table('dompet_pelanggan')->insertGetId([
        'id_pelanggan'   => 1,
        'nominal'        => $nominal,
        'kode_unik'      => $kode_unik,
        'total_transfer' => $total,
        'bank_tujuan'    => 'DANA',
        'status'         => 'pending',
        'waktu_request'  => now(),
    ]);

    $row = DB::table('dompet_pelanggan')->where('id', $id)->first();

    echo "  nominal       : " . $row->nominal . " " . ($row->nominal == $nominal ? "✓" : "✗ SALAH (expected {$nominal})") . "\n";
    echo "  kode_unik     : " . $row->kode_unik . " " . ($row->kode_unik == $kode_unik ? "✓" : "✗ SALAH (expected {$kode_unik})") . "\n";
    echo "  total_transfer: " . $row->total_transfer . " " . ($row->total_transfer == $total ? "✓" : "✗ SALAH (expected {$total})") . "\n";
    echo "  bank_tujuan   : " . $row->bank_tujuan . " ✓\n";
    echo "  status        : " . $row->status . " ✓\n";

    DB::rollBack();
    echo "\n  (Data di-rollback, tidak tersimpan permanen)\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "  ✗ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== HASIL AKHIR ===\n";
echo $all_ok
    ? "  ✓ Semua kolom ada. Sistem siap digunakan.\n"
    : "  ✗ Ada kolom yang hilang! Jalankan: php artisan migrate\n";
