<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jastip_orders', function (Blueprint $table) {
            $table->dropForeign('jastip_orders_mitra_id_foreign');
            $table->dropColumn('mitra_id');
        });
        Schema::table('jastip_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('mitra_id')->nullable()->after('id');
            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->nullOnDelete();
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign('service_orders_mitra_id_foreign');
            $table->dropColumn('mitra_id');
        });
        Schema::table('service_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('mitra_id')->nullable()->after('id');
            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->nullOnDelete();
        });

        Schema::table('tenaga_orders', function (Blueprint $table) {
            $table->dropForeign('tenaga_orders_mitra_id_foreign');
            $table->dropColumn('mitra_id');
        });
        Schema::table('tenaga_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('mitra_id')->nullable()->after('id');
            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->nullOnDelete();
        });

        Schema::table('wfh_orders', function (Blueprint $table) {
            $table->dropForeign('wfh_orders_mitra_id_foreign');
            $table->dropColumn('mitra_id');
        });
        Schema::table('wfh_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('mitra_id')->nullable()->after('id');
            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->nullOnDelete();
        });

        Schema::table('mitra_layanans', function (Blueprint $table) {
            $table->dropForeign('mitra_layanans_mitra_id_foreign');
            $table->dropColumn('mitra_id');
        });
        Schema::table('mitra_layanans', function (Blueprint $table) {
            $table->unsignedBigInteger('mitra_id')->nullable()->after('id');
            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->cascadeOnDelete();
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropForeign('withdrawals_mitra_id_foreign');
            $table->dropColumn('mitra_id');
        });
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->unsignedBigInteger('mitra_id')->nullable()->after('id');
            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->nullOnDelete();
        });

        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropForeign('withdrawal_requests_mitra_id_foreign');
            $table->dropColumn('mitra_id');
        });
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('mitra_id')->nullable()->after('id');
            $table->foreign('mitra_id')->references('id_mitra')->on('mitra')->nullOnDelete();
        });

        Schema::table('wallet_transfers', function (Blueprint $table) {
            $table->dropForeign('wallet_transfers_dari_mitra_id_foreign');
            $table->dropForeign('wallet_transfers_ke_mitra_id_foreign');
            $table->dropColumn(['dari_mitra_id', 'ke_mitra_id']);
        });
        Schema::table('wallet_transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('dari_mitra_id')->nullable()->after('id');
            $table->unsignedBigInteger('ke_mitra_id')->nullable()->after('dari_mitra_id');
            $table->foreign('dari_mitra_id')->references('id_mitra')->on('mitra')->nullOnDelete();
            $table->foreign('ke_mitra_id')->references('id_mitra')->on('mitra')->nullOnDelete();
        });

        Schema::dropIfExists('mitras');
    }

    public function down(): void
    {
        throw new \RuntimeException('This migration cannot be rolled back safely. Restore from backup.');
    }
};
