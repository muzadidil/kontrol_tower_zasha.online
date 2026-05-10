<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->unsignedBigInteger('pelanggan_id');
            $table->string('mitra_id');
            $table->foreignId('mitra_layanan_id')->constrained('mitra_layanans');

            $table->decimal('biaya_service_standar', 15, 2);
            $table->decimal('jarak_km', 8, 2)->default(0);
            $table->decimal('biaya_bensin', 15, 2)->default(0);
            $table->decimal('estimasi_awal', 15, 2);

            $table->decimal('total_jasa', 15, 2)->default(0);
            $table->decimal('total_sparepart', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->decimal('komisi_zasha', 15, 2)->default(0);
            $table->decimal('pendapatan_mitra', 15, 2)->default(0);

            $table->enum('metode_pembayaran', ['cod', 'transfer', 'saldo'])->nullable();
            $table->decimal('saldo_mitra_snapshot', 15, 2);
            $table->boolean('cod_eligible');

            $table->string('alamat_pelanggan');
            $table->decimal('pelanggan_lat', 10, 7)->nullable();
            $table->decimal('pelanggan_lng', 10, 7)->nullable();
            $table->text('keluhan_pelanggan')->nullable();

            $table->enum('status', [
                'menunggu_mitra','ditolak','menuju_lokasi','diagnosa',
                'menunggu_konfirmasi_harga','dikerjakan',
                'menunggu_konfirmasi','selesai','dispute',
            ])->default('menunggu_mitra');

            $table->timestamp('mitra_notified_at')->nullable();
            $table->timestamp('mitra_responded_at')->nullable();
            $table->string('alasan_penolakan')->nullable();
            $table->timestamp('auto_reject_at')->nullable();
            $table->timestamp('berangkat_at')->nullable();
            $table->timestamp('mulai_kerja_at')->nullable();
            $table->timestamp('selesai_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pelanggan_id')->references('id_pelanggan')->on('pelanggans');
            $table->foreign('mitra_id')->references('id')->on('mitras');
        });

        Schema::create('service_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained()->cascadeOnDelete();
            $table->enum('tipe', ['jasa', 'sparepart']);
            $table->string('nama_item');
            $table->decimal('harga', 15, 2);
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_order_items');
        Schema::dropIfExists('service_orders');
    }
};
