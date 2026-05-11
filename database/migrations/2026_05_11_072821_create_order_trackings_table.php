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
        Schema::create('order_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('order_type');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('mitra_id');
            $table->unsignedBigInteger('pelanggan_id');
            $table->enum('status', [
                'pending',
                'accepted',
                'menuju_lokasi',
                'di_lokasi',
                'dikerjakan',
                'selesai_mitra',
                'selesai',
                'belum_selesai',
                'ditolak_mitra',
                'dibatalkan',
            ])->default('pending');
            $table->text('pesan_tolak')->nullable();
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->decimal('harga_modal', 15, 2)->default(0);
            $table->decimal('komisi_zasha', 15, 2)->default(0);
            $table->enum('escrow_status', ['held', 'released', 'refunded'])->default('held');
            $table->timestamps();

            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->cascadeOnDelete();
            $table->foreign('pelanggan_id')->references('id_pelanggan')->on('pelanggans')->cascadeOnDelete();
            $table->index(['order_type', 'order_id']);
            $table->index(['mitra_id', 'status']);
            $table->index(['pelanggan_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_trackings');
    }
};
