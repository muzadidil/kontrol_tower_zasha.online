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
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('pelanggan_id');
            $table->string('mitra_id');
            $table->enum('tipe_waktu', ['Instan', 'Terjadwal']);
            $table->dateTime('jadwal_pelaksanaan')->nullable();
            $table->enum('status', ['Pending', 'Menuju Lokasi', 'Dikerjakan', 'Menunggu Konfirmasi', 'Selesai', 'Ditolak']);
            $table->enum('metode_pembayaran', ['COD', 'Transfer', 'Saldo']);
            $table->integer('durasi_kerja');
            $table->decimal('total_biaya', 15, 2);
            $table->decimal('komisi_zasha', 15, 2);
            $table->text('keterangan_kerja')->nullable();
            $table->timestamps();

            $table->foreign('pelanggan_id')->references('id')->on('pelanggans')->onDelete('cascade');
            $table->foreign('mitra_id')->references('id')->on('mitras')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
