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
        Schema::create('ppob_transactions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id');
            $table->string('user_type');
            $table->string('sku');
            $table->string('target_number');
            $table->decimal('price', 15, 2);
            $table->decimal('selling_price', 15, 2);
            $table->string('status')->default('Pending');
            $table->string('ref_id')->unique();
            $table->string('sn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppob_transactions');
    }
};
