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
        Schema::create('mitra_notifikasis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mitra_id');
            $table->unsignedBigInteger('tracking_id')->nullable();
            $table->enum('tipe', ['order_masuk', 'order_update', 'sistem']);
            $table->string('judul');
            $table->text('pesan')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_push_sent')->default(false);
            $table->timestamps();

            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->cascadeOnDelete();
            $table->foreign('tracking_id')->references('id')->on('order_trackings')->nullOnDelete();
            $table->index(['mitra_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra_notifikasis');
    }
};
