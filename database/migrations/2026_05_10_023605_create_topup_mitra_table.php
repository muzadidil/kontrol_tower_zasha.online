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
        Schema::create('topup_mitra', function (Blueprint $table) {
            $table->bigIncrements('id_topup');
            $table->unsignedBigInteger('id_mitra');
            $table->decimal('jumlah_topup', 15, 2);
            $table->enum('status_topup', ['Pending', 'Selesai', 'Expired'])->default('Pending');
            $table->timestamp('tanggal')->useCurrent();
            $table->timestamps();

            $table->index('id_mitra');
            $table->index('status_topup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topup_mitra');
    }
};
