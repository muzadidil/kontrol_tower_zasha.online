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
        Schema::table('dompet_pelanggan', function (Blueprint $table) {
            $table->integer('kode_unik')->nullable()->after('nominal');
            $table->decimal('total_transfer', 15, 2)->nullable()->after('kode_unik');
            $table->string('bank_tujuan')->nullable()->after('total_transfer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dompet_pelanggan', function (Blueprint $table) {
            $table->dropColumn(['kode_unik', 'total_transfer', 'bank_tujuan']);
        });
    }
};
