<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_dokumen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tracking_id');
            $table->unsignedBigInteger('mitra_id');
            $table->string('judul', 200);
            $table->string('file_path');
            $table->string('mime_type', 100)->nullable();
            $table->integer('size_bytes')->default(0);
            $table->text('catatan')->nullable();
            $table->boolean('is_final')->default(false); // tandai sebagai versi final
            $table->timestamps();

            $table->foreign('tracking_id')->references('id')->on('order_trackings')->cascadeOnDelete();
            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->cascadeOnDelete();
            $table->index('tracking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_dokumen');
    }
};
