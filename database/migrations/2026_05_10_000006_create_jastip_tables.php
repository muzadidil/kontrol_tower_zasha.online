<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jastip_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->unsignedBigInteger('pelanggan_id');
            $table->string('mitra_id');

            $table->string('delivery_address');
            $table->decimal('delivery_lat', 10, 7);
            $table->decimal('delivery_lng', 10, 7);

            $table->decimal('total_jarak_km', 8, 2);
            $table->integer('total_stops');
            $table->decimal('tarif_per_km', 10, 2);
            $table->decimal('biaya_stop', 10, 2);
            $table->decimal('ongkos_jasa', 10, 2);
            $table->decimal('komisi_zasha', 10, 2);
            $table->decimal('pendapatan_mitra', 10, 2);

            $table->decimal('estimasi_total_barang', 15, 2);
            $table->decimal('actual_total_barang', 15, 2)->default(0);

            $table->decimal('saldo_mitra_snapshot', 15, 2);
            $table->boolean('cod_eligible');

            $table->enum('status', [
                'menunggu_mitra','ditolak','menuju_pickup','belanja',
                'menuju_pengantaran','diantar','menunggu_konfirmasi',
                'selesai','dispute',
            ])->default('menunggu_mitra');

            $table->timestamp('mitra_notified_at')->nullable();
            $table->timestamp('mitra_responded_at')->nullable();
            $table->string('alasan_penolakan')->nullable();
            $table->timestamp('auto_reject_at')->nullable();
            $table->timestamp('pickup_started_at')->nullable();
            $table->timestamp('delivery_started_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('selesai_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pelanggan_id')->references('id_pelanggan')->on('pelanggans');
            $table->foreign('mitra_id')->references('id')->on('mitras');
        });

        Schema::create('jastip_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jastip_order_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('urutan');
            $table->string('nama_lokasi');
            $table->string('alamat_lokasi');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('jarak_dari_prev_km', 8, 2)->default(0);
            $table->timestamp('tiba_at')->nullable();
            $table->timestamps();
        });

        Schema::create('jastip_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jastip_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jastip_stop_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('urutan');
            $table->string('nama_barang');
            $table->decimal('harga_perkiraan', 15, 2);
            $table->decimal('harga_asli', 15, 2)->nullable();
            $table->boolean('is_checked')->default(false);
            $table->timestamp('checked_at')->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jastip_order_items');
        Schema::dropIfExists('jastip_stops');
        Schema::dropIfExists('jastip_orders');
    }
};
