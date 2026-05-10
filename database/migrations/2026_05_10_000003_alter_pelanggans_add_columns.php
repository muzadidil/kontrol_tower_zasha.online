<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // password sudah ada di pelanggans — skip
            $table->string('nama_panggilan')->nullable();
            $table->date('birth_date')->nullable();
            $table->decimal('rating_pelanggan', 3, 2)->default(5.00);
            $table->boolean('is_profile_complete')->default(false);
            $table->string('fcm_token')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn([
                'nama_panggilan','birth_date',
                'rating_pelanggan','is_profile_complete','fcm_token',
            ]);
        });
    }
};
