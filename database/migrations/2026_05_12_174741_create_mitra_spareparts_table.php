<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitra_spareparts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mitra_id');
            $table->string('nama', 200);
            $table->string('kode', 100)->nullable();      // SKU internal mitra
            $table->string('kategori', 100)->nullable();  // contoh: Kompresor AC, Freon, Kabel
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 15, 2);              // harga jual ke pelanggan
            $table->decimal('harga_modal', 15, 2)->default(0); // optional untuk lacak margin
            $table->integer('stok')->default(0);
            $table->integer('stok_min')->default(0);      // ambang notif "stok menipis"
            $table->string('satuan', 20)->default('pcs'); // pcs / kg / meter / liter
            $table->string('foto_path')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->cascadeOnDelete();
            $table->index('mitra_id');
            $table->index(['mitra_id', 'is_aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra_spareparts');
    }
};
