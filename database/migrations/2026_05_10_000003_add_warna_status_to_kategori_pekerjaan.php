<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategori_pekerjaan', function (Blueprint $table) {
            $table->string('warna_tema')->default('primary')->after('svg_kategori');
            $table->string('status')->default('aktif')->after('warna_tema');
        });
    }

    public function down(): void
    {
        Schema::table('kategori_pekerjaan', function (Blueprint $table) {
            $table->dropColumn(['warna_tema', 'status']);
        });
    }
};
