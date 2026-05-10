<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── user_sessions (single device login) ─────────────────────────────
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('user_type');         // 'mitra' atau 'pelanggan'
            $table->string('user_id');
            $table->string('device_name');        // Samsung Galaxy 12, iPhone 14, dll
            $table->string('device_id')->unique();
            $table->string('token');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->index(['user_type', 'user_id']);
        });

        // ── master_layanans (dikelola admin) ────────────────────────────────
        Schema::create('master_layanans', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori_mitra', ['TNG', 'WFH', 'JST', 'SVC']);
            $table->string('nama_layanan');
            $table->string('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── mitra_layanans (sub-layanan + tarif per mitra) ──────────────────
        Schema::create('mitra_layanans', function (Blueprint $table) {
            $table->id();
            $table->string('mitra_id');
            $table->foreignId('master_layanan_id')->constrained()->cascadeOnDelete();

            $table->decimal('tarif_per_jam', 15, 2)->nullable();
            $table->decimal('tarif_per_hari', 15, 2)->nullable();
            $table->decimal('tarif_per_km', 15, 2)->nullable();
            $table->decimal('tarif_per_unit', 15, 2)->nullable();
            $table->string('satuan_unit')->nullable();
            $table->decimal('tarif_per_project', 15, 2)->nullable();
            $table->decimal('express_multiplier', 4, 2)->default(1.50);
            $table->decimal('biaya_service_standar', 15, 2)->nullable();
            $table->decimal('tarif_bensin_per_km', 15, 2)->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('mitra_id')->references('id')->on('mitras')->cascadeOnDelete();
        });

        // ── mitra_dokumens (dokumen verifikasi) ─────────────────────────────
        Schema::create('mitra_dokumens', function (Blueprint $table) {
            $table->id();
            $table->string('mitra_id');
            $table->enum('tipe_dokumen', [
                'ktp','foto_profil','skck','sim','sertifikat',
                'portfolio_link','selfie_ktp','foto_tempat_praktek','foto_kendaraan',
            ]);
            $table->string('file_url')->nullable();
            $table->string('link_url')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->string('catatan_admin')->nullable();
            $table->timestamps();

            $table->foreign('mitra_id')->references('id')->on('mitras')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra_dokumens');
        Schema::dropIfExists('mitra_layanans');
        Schema::dropIfExists('master_layanans');
        Schema::dropIfExists('user_sessions');
    }
};
