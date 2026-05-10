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
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pelanggan');
            $table->string('judul', 150);
            $table->text('pesan');
            $table->string('tipe', 50)->default('info'); // pesanan, topup, info
            $table->string('url', 255)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index('id_pelanggan');
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
