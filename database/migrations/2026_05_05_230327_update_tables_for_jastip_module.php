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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_harga_barang', 12, 2)->default(0);
            $table->decimal('ongkos_jastip', 12, 2)->default(0);
            $table->decimal('jarak_km', 8, 2)->default(0);
            $table->integer('jumlah_titik')->default(0);
        });

        Schema::table('mitras', function (Blueprint $table) {
            $table->decimal('tarif_per_km', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_harga_barang', 'ongkos_jastip', 'jarak_km', 'jumlah_titik']);
        });

        Schema::table('mitras', function (Blueprint $table) {
            $table->dropColumn('tarif_per_km');
        });
    }
};
