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
            $table->decimal('biaya_service_standar', 15, 2)->default(0);
            $table->decimal('tarif_bensin_per_km_service', 15, 2)->default(0);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('tipe_item', ['jasa', 'sparepart'])->default('jasa');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('biaya_bensin_service', 15, 2)->default(0);
            // $table->integer('jarak_km') already exists in jastip migration as decimal, changing to integer might cause issues if not handled carefully, leaving as is or using existing one.
        });
    }

    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->dropColumn(['biaya_service_standar', 'tarif_bensin_per_km_service']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('tipe_item');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('biaya_bensin_service');
        });
    }
};
