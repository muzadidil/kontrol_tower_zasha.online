<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenaga_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->unsignedBigInteger('pelanggan_id');
            $table->string('mitra_id');
            $table->foreignId('mitra_layanan_id')->constrained('mitra_layanans');

            $table->enum('tipe_waktu', ['instan', 'terjadwal']);
            $table->timestamp('jadwal_at')->nullable();

            $table->enum('tipe_durasi', ['jam', 'hari']);
            $table->decimal('durasi', 8, 2);
            $table->decimal('tarif', 15, 2);
            $table->decimal('total_biaya', 15, 2);
            $table->decimal('komisi_zasha', 15, 2);
            $table->decimal('pendapatan_mitra', 15, 2);

            $table->enum('metode_pembayaran', ['cod', 'transfer', 'saldo']);
            $table->decimal('saldo_mitra_snapshot', 15, 2);
            $table->boolean('cod_eligible');

            $table->string('alamat_pelanggan');
            $table->decimal('pelanggan_lat', 10, 7)->nullable();
            $table->decimal('pelanggan_lng', 10, 7)->nullable();
            $table->text('keterangan_kerja')->nullable();

            $table->enum('status', [
                'menunggu_mitra','ditolak','menuju_lokasi',
                'dikerjakan','menunggu_konfirmasi','selesai','dispute',
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
    }

    public function down(): void
    {
        Schema::dropIfExists('tenaga_orders');
    }
};
