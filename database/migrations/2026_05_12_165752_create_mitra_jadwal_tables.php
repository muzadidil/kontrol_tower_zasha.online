<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jam kerja per hari (Senin-Minggu)
        Schema::create('mitra_jadwal_harian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mitra_id');
            $table->tinyInteger('hari'); // 0=Minggu, 1=Senin, ... 6=Sabtu
            $table->boolean('is_libur')->default(false);
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();
            $table->timestamps();

            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->cascadeOnDelete();
            $table->unique(['mitra_id', 'hari']);
        });

        // Tanggal libur spesifik (cuti / off-day)
        Schema::create('mitra_jadwal_libur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mitra_id');
            $table->date('tanggal');
            $table->string('keterangan', 200)->nullable();
            $table->timestamps();

            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->cascadeOnDelete();
            $table->unique(['mitra_id', 'tanggal']);
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra_jadwal_libur');
        Schema::dropIfExists('mitra_jadwal_harian');
    }
};
