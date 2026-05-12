<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_foto_bukti', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tracking_id');
            $table->unsignedBigInteger('mitra_id');
            $table->enum('tipe', ['sebelum', 'proses', 'sesudah']);
            $table->string('file_path');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('tracking_id')->references('id')->on('order_trackings')->cascadeOnDelete();
            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->cascadeOnDelete();
            $table->index(['tracking_id', 'tipe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_foto_bukti');
    }
};
