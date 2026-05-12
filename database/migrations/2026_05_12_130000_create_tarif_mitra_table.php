<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_mitra', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mitra_id');
            $table->string('keterangan', 255);
            $table->decimal('nominal', 15, 2);          // tarif yang diterima mitra (sebelum komisi)
            $table->string('satuan', 50)->default('per item'); // per jam, per hari, per item, dll
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->cascadeOnDelete();
            $table->index('mitra_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_mitra');
    }
};
