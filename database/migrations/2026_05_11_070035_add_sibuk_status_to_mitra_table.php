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
        DB::statement("ALTER TABLE mitra MODIFY COLUMN status_online ENUM('online','offline','suspended','sibuk') NOT NULL DEFAULT 'offline'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE mitra MODIFY COLUMN status_online ENUM('online','offline','suspended') NOT NULL DEFAULT 'offline'");
    }
};
