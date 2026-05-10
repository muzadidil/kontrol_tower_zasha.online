<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah 'game' & 'voucher' ke enum jenis_produk untuk Game Top-up & Voucher
        DB::statement("ALTER TABLE ppob_transactions MODIFY jenis_produk ENUM('pulsa', 'paket_data', 'token_listrik', 'game', 'voucher') NOT NULL");
    }

    public function down(): void
    {
        // Rollback ke enum lama (PERHATIAN: data dengan jenis 'game'/'voucher' akan rusak)
        DB::statement("ALTER TABLE ppob_transactions MODIFY jenis_produk ENUM('pulsa', 'paket_data', 'token_listrik') NOT NULL");
    }
};
