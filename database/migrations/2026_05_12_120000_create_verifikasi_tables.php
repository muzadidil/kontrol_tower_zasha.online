<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Syarat verifikasi per role (banyak verifikasi per role).
        Schema::create('role_verifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('verifikasi_key', 64);
            $table->boolean('wajib')->default(true);
            $table->timestamps();

            $table->unique(['role_id', 'verifikasi_key']);
            $table->index('verifikasi_key');
        });

        // Dokumen yang diupload mitra (status per dokumen).
        Schema::create('mitra_verifikasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mitra_id');
            $table->string('verifikasi_key', 64);
            $table->enum('status', ['pending', 'approved', 'ditolak'])->default('pending');
            $table->string('file_path')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->cascadeOnDelete();
            $table->unique(['mitra_id', 'verifikasi_key']);
            $table->index('status');
        });

        // Kolom mitra.status_verifikasi sudah ada sebagai varchar.
        // Tidak alter ke enum karena varchar lebih fleksibel & data existing tetap valid.
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra_verifikasi');
        Schema::dropIfExists('role_verifikasi');
    }
};
