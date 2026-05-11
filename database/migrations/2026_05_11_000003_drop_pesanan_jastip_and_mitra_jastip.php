<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop pesanan_jastip dulu karena ada FK ke mitra_jastip
        Schema::dropIfExists('pesanan_jastip');
        Schema::dropIfExists('mitra_jastip');
    }

    public function down(): void
    {
        throw new \RuntimeException('Migration ini tidak dapat di-rollback. Restore dari backup jika perlu.');
    }
};
