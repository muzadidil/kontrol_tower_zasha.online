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
        Schema::create('mitras', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nama_panggilan');
            $table->integer('usia');
            $table->enum('kategori', ['Kuli', 'Teknisi', 'Jasa']);
            $table->text('deskripsi_singkat');
            $table->string('nomor_wa');
            $table->decimal('saldo_mitra', 15, 2)->default(0);
            $table->decimal('tarif_per_jam', 15, 2);
            $table->decimal('tarif_per_hari', 15, 2);
            $table->enum('status', ['Aktif', 'Non-aktif']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitras');
    }
};
