<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitra_portfolios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mitra_id');
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->string('file_path')->nullable();  // path image atau pdf
            $table->string('link_url', 500)->nullable(); // link external (github, behance, dll)
            $table->string('kategori', 100)->nullable(); // contoh: Desain UI, Coding, Voice Over
            $table->boolean('is_featured')->default(false);
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->cascadeOnDelete();
            $table->index('mitra_id');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra_portfolios');
    }
};
