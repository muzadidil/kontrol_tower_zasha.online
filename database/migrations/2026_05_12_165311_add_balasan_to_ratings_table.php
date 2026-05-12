<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            if (!Schema::hasColumn('ratings', 'balasan_mitra')) {
                $table->text('balasan_mitra')->nullable()->after('ulasan');
            }
            if (!Schema::hasColumn('ratings', 'balasan_at')) {
                $table->timestamp('balasan_at')->nullable()->after('balasan_mitra');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            if (Schema::hasColumn('ratings', 'balasan_at')) {
                $table->dropColumn('balasan_at');
            }
            if (Schema::hasColumn('ratings', 'balasan_mitra')) {
                $table->dropColumn('balasan_mitra');
            }
        });
    }
};
