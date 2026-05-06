<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->renameColumn('saldo_mitra', 'saldo');
        });

        Schema::table('pelanggans', function (Blueprint $table) {
            $table->renameColumn('saldo_pelanggan', 'saldo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->renameColumn('saldo', 'saldo_mitra');
        });

        Schema::table('pelanggans', function (Blueprint $table) {
            $table->renameColumn('saldo', 'saldo_pelanggan');
        });
    }
};
