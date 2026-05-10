<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->enum('kategori_kode', ['TNG', 'WFH', 'JST', 'SVC'])->after('kategori')->nullable();
            $table->enum('status_verifikasi', ['pending_document','pending_review','active','rejected','suspended'])
                  ->default('pending_document')->after('status');
            $table->enum('status_online', ['online','offline','suspended'])->default('online')->after('status_verifikasi');

            $table->decimal('lokasi_lat', 10, 7)->nullable();
            $table->decimal('lokasi_lng', 10, 7)->nullable();
            $table->string('lokasi_label')->nullable();

            $table->string('vehicle_name')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->string('vehicle_photo')->nullable();

            $table->string('sim_number')->nullable();
            $table->date('sim_expiry')->nullable();

            $table->decimal('rating', 3, 2)->default(0.00);
            $table->decimal('rejection_rate', 5, 2)->default(0.00);
            $table->unsignedInteger('timeout_streak')->default(0);
            $table->timestamp('offline_until')->nullable();

            // password sudah ada di mitras — skip
            $table->string('fcm_token')->nullable();
            $table->string('selfie_live_photo')->nullable();
            $table->boolean('face_gimmick_passed')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->dropColumn([
                'kategori_kode','status_verifikasi','status_online',
                'lokasi_lat','lokasi_lng','lokasi_label',
                'vehicle_name','vehicle_plate','vehicle_color','vehicle_photo',
                'sim_number','sim_expiry',
                'rating','rejection_rate','timeout_streak','offline_until',
                'fcm_token','selfie_live_photo','face_gimmick_passed',
            ]);
        });
    }
};
