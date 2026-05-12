<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel master role mitra
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();           // mis. "Mitra TNG"
            $table->string('description')->nullable();  // mis. "Tenaga Kerja"
            $table->boolean('is_default')->default(false); // role bawaan sistem (tidak bisa dihapus)
            $table->timestamps();
        });

        // Tabel many-to-many: role <-> features
        Schema::create('role_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('feature_key', 64);
            $table->timestamps();

            $table->unique(['role_id', 'feature_key']);
            $table->index('feature_key');
        });

        // Tambah role_id ke tabel mitra
        Schema::table('mitra', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('kategori_kode');
            $table->foreign('role_id')->references('id')->on('roles')->nullOnDelete();
            $table->index('role_id');
        });
    }

    public function down(): void
    {
        Schema::table('mitra', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropIndex(['role_id']);
            $table->dropColumn('role_id');
        });
        Schema::dropIfExists('role_features');
        Schema::dropIfExists('roles');
    }
};
