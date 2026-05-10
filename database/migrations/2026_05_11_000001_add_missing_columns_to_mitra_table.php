<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mitra', function (Blueprint $table) {
            if (! Schema::hasColumn('mitra', 'kategori_kode')) {
                $table->enum('kategori_kode', ['TNG', 'WFH', 'JST', 'SVC'])->nullable()->after('id_kategori');
            }
            if (! Schema::hasColumn('mitra', 'status_online')) {
                $table->enum('status_online', ['online', 'offline', 'suspended'])->default('offline')->after('status_verifikasi');
            }
            if (! Schema::hasColumn('mitra', 'lokasi_label')) {
                $table->string('lokasi_label')->nullable();
            }
            if (! Schema::hasColumn('mitra', 'vehicle_name')) {
                $table->string('vehicle_name')->nullable();
            }
            if (! Schema::hasColumn('mitra', 'vehicle_plate')) {
                $table->string('vehicle_plate')->nullable();
            }
            if (! Schema::hasColumn('mitra', 'vehicle_color')) {
                $table->string('vehicle_color')->nullable();
            }
            if (! Schema::hasColumn('mitra', 'vehicle_photo')) {
                $table->string('vehicle_photo')->nullable();
            }
            if (! Schema::hasColumn('mitra', 'sim_number')) {
                $table->string('sim_number')->nullable();
            }
            if (! Schema::hasColumn('mitra', 'sim_expiry')) {
                $table->date('sim_expiry')->nullable();
            }
            if (! Schema::hasColumn('mitra', 'rating')) {
                $table->decimal('rating', 3, 2)->default(0.00);
            }
            if (! Schema::hasColumn('mitra', 'rejection_rate')) {
                $table->decimal('rejection_rate', 5, 2)->default(0.00);
            }
            if (! Schema::hasColumn('mitra', 'timeout_streak')) {
                $table->unsignedInteger('timeout_streak')->default(0);
            }
            if (! Schema::hasColumn('mitra', 'offline_until')) {
                $table->timestamp('offline_until')->nullable();
            }
            if (! Schema::hasColumn('mitra', 'saldo_mitra')) {
                $table->decimal('saldo_mitra', 15, 2)->default(0);
            }
            if (! Schema::hasColumn('mitra', 'fcm_token')) {
                $table->string('fcm_token')->nullable();
            }
            if (! Schema::hasColumn('mitra', 'selfie_live_photo')) {
                $table->string('selfie_live_photo')->nullable();
            }
            if (! Schema::hasColumn('mitra', 'face_gimmick_passed')) {
                $table->boolean('face_gimmick_passed')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('mitra', function (Blueprint $table) {
            $cols = [
                'kategori_kode', 'status_online', 'lokasi_label',
                'vehicle_name', 'vehicle_plate', 'vehicle_color', 'vehicle_photo',
                'sim_number', 'sim_expiry',
                'rating', 'rejection_rate', 'timeout_streak', 'offline_until',
                'saldo_mitra', 'fcm_token', 'selfie_live_photo', 'face_gimmick_passed',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('mitra', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
