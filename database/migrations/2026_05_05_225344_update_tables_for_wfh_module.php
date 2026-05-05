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
        Schema::table('mitras', function (Blueprint $table) {
            $table->string('portfolio_link')->nullable();
            $table->boolean('is_wfh')->default(false);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->text('link_briefing')->nullable();
            $table->string('link_referensi')->nullable();
            $table->string('link_hasil_kerja')->nullable();
            $table->datetime('deadline')->nullable();
            $table->integer('kuantitas')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->dropColumn(['portfolio_link', 'is_wfh']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['link_briefing', 'link_referensi', 'link_hasil_kerja', 'deadline', 'kuantitas']);
        });
    }
};
