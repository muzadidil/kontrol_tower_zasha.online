<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Icon: bisa Bootstrap Icon class (mis. "bi-tools") atau path gambar
            $table->string('icon', 100)->nullable()->after('description');
            $table->string('icon_color', 20)->nullable()->after('icon')->default('#005aa9');
            // Status rilis: draft (belum aktif) vs active (siap dipakai)
            $table->boolean('is_active')->default(false)->after('is_default');
        });

        // Default 4 role bawaan -> langsung active
        \DB::table('roles')->where('is_default', true)->update(['is_active' => true]);
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['icon', 'icon_color', 'is_active']);
        });
    }
};
