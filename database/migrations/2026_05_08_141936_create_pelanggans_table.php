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
        Schema::create('pelanggans', function (Blueprint $table) {
            // ID Utama
            $table->id('id_pelanggan');
            
            // Identitas Login (Multi-Auth)
            $table->string('nama_pelanggan');
            $table->string('email')->unique();
            $table->string('password');
            
            // Informasi Kontak & Profil
            $table->string('no_hp')->unique()->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto_profil')->nullable();
            
            // Fitur Khusus Zasha
            $table->string('kode_zasha')->unique(); // Contoh: ZSH-2024-001
            $table->integer('is_verif')->default(0); // 0: Belum, 1: Sudah
            $table->decimal('saldo', 12, 2)->default(0); // Dompet Pelanggan
            
            // Pengaturan Laravel
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};