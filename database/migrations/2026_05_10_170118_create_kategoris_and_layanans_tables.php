<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('sub_nama', 100)->nullable();
            $table->string('kode', 50)->unique();
            $table->text('thumbnail')->nullable();
            $table->tinyInteger('server_id')->default(0);
            $table->string('tipe', 20)->default('game');
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index('nama');
            $table->index('status');
        });

        Schema::create('layanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategoris')->cascadeOnDelete();
            $table->string('layanan', 200);
            $table->string('provider_id', 50)->unique();
            $table->string('provider', 30)->default('digiflazz');
            $table->bigInteger('harga');
            $table->tinyInteger('profit')->default(0);
            $table->string('status', 20)->default('available');
            $table->timestamps();

            $table->index(['kategori_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanans');
        Schema::dropIfExists('kategoris');
    }
};
