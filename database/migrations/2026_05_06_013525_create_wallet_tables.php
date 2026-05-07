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
        Schema::create('wallet_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('sender_mitra_id');
            $table->string('receiver_mitra_id');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('sender_mitra_id')->references('id')->on('mitras');
            $table->foreign('receiver_mitra_id')->references('id')->on('mitras');
        });

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->string('mitra_id');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_name');
            $table->decimal('nominal', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('mitra_id')->references('id')->on('mitras');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transfers');
        Schema::dropIfExists('withdrawals');
    }
};
