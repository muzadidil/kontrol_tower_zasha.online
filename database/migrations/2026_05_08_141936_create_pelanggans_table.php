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
        Schema::disableForeignKeyConstraints();

        // Drop FK lama di tabel orders yang referensi pelanggans.id (skema lama varchar)
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function ($table) {
                try { $table->dropForeign(['pelanggan_id']); } catch (\Exception $e) {}
            });
        }

        Schema::dropIfExists('pelanggans');

        Schema::create('pelanggans', function (Blueprint $table) {
            // ID Utama - Jika menggunakan nama custom, pastikan di Model diset $primaryKey
            $table->id('id_pelanggan'); 
            
            // Identitas Login (Multi-Auth)
            $table->string('nama_pelanggan');
            $table->string('email')->unique();
            $table->string('password');
            
            // Informasi Kontak & Profil - Disinkronkan dengan Konsep Lama Bapak
            $table->string('no_wa')->unique()->nullable(); // Sebelumnya no_hp, saya ubah ke no_wa sesuai kodingan profil Bapak
            $table->date('tgl_lahir')->nullable();         // Ditambahkan karena ada di logika validasi umur 18+ Bapak
            $table->string('foto')->nullable();            // Sebelumnya foto_profil, saya singkat foto sesuai kodingan profil Bapak
            
            // Fitur Khusus Zasha
            $table->string('kode_zasha')->unique();        // Contoh: ZSH-2026-001
            $table->decimal('saldo', 12, 2)->default(0);   // Dompet Pelanggan
            
            /** 
             * Status Verifikasi:
             * Bapak sempat bilang "tidak ada verifikasi", tapi di kodingan profil ada logika 
             * "Satpam Otomatis" untuk cek kelengkapan data. Jadi kolom ini tetap berguna 
             * sebagai penanda profil sudah lengkap (1) atau belum (0).
             */
            $table->integer('status_verifikasi')->default(0); 
            
            // Pengaturan Laravel
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};