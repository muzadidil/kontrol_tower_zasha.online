<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('wfh_orders');
        Schema::create('wfh_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->unsignedBigInteger('pelanggan_id');
            $table->string('mitra_id');
            $table->foreignId('mitra_layanan_id')->constrained('mitra_layanans');

            $table->text('brief_description');
            $table->string('reference_url')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);

            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->decimal('komisi_zasha', 15, 2);
            $table->decimal('pendapatan_mitra', 15, 2);

            $table->enum('tipe_order', ['reguler', 'express']);
            $table->timestamp('deadline_at');

            $table->enum('status', [
                'pending_pembayaran','menunggu_mitra','ditolak',
                'dikerjakan','file_terkirim','menunggu_konfirmasi',
                'dispute','selesai','refund',
            ])->default('pending_pembayaran');

            $table->timestamp('mitra_notified_at')->nullable();
            $table->timestamp('mitra_responded_at')->nullable();
            $table->string('alasan_penolakan')->nullable();
            $table->timestamp('auto_reject_at')->nullable();

            $table->string('result_file_url')->nullable();
            $table->timestamp('file_submitted_at')->nullable();
            $table->timestamp('auto_release_at')->nullable();

            $table->timestamp('selesai_at')->nullable();
            $table->enum('trigger_selesai', ['manual','auto_release','dispute_resolved'])->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pelanggan_id')->references('id_pelanggan')->on('pelanggans');
            $table->foreign('mitra_id')->references('id')->on('mitras');
        });

        Schema::create('escrow_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wfh_order_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['hold','release_to_mitra','refund_to_customer','commission']);
            $table->decimal('amount', 15, 2);
            $table->string('keterangan')->nullable();
            $table->enum('triggered_by', ['customer','auto_release','admin','dispute'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escrow_ledgers');
        Schema::dropIfExists('wfh_orders');
    }
};
