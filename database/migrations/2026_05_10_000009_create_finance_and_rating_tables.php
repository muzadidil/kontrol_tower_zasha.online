<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Ratings (dua arah) ──────────────────────────────────────────────
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->enum('order_type', ['wfh','jastip','tenaga','service']);
            $table->unsignedBigInteger('order_id');
            $table->string('penilai_id');
            $table->enum('tipe_penilai', ['pelanggan','mitra']);
            $table->string('dinilai_id');
            $table->enum('tipe_dinilai', ['pelanggan','mitra']);
            $table->tinyInteger('bintang');
            $table->text('ulasan')->nullable();
            $table->string('foto_url')->nullable();
            $table->boolean('is_anonim')->default(true);
            $table->timestamps();
        });

        // ── Disputes (shared semua modul) ───────────────────────────────────
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->enum('order_type', ['wfh','jastip','tenaga','service']);
            $table->unsignedBigInteger('order_id');
            $table->string('raised_by_id');
            $table->enum('raised_by_type', ['pelanggan','mitra']);
            $table->text('complaint_description');
            $table->enum('status', ['open','under_review','resolved'])->default('open');
            $table->enum('resolution', ['release_to_mitra','refund_to_customer','partial_refund','refund_commission'])->nullable();
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // ── Wallet Transfers (mitra ke mitra saja) ──────────────────────────
        Schema::dropIfExists('wallet_transfers');
        Schema::create('wallet_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('dari_mitra_id');
            $table->string('ke_mitra_id');
            $table->decimal('jumlah', 15, 2);
            $table->string('catatan')->nullable();
            $table->enum('status', ['berhasil', 'gagal'])->default('berhasil');
            $table->timestamps();

            $table->foreign('dari_mitra_id')->references('id')->on('mitras');
            $table->foreign('ke_mitra_id')->references('id')->on('mitras');
        });

        // ── Withdrawal Requests (mitra tarik saldo) ─────────────────────────
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->string('mitra_id');
            $table->decimal('jumlah', 15, 2);
            $table->string('nama_rekening');
            $table->string('nomor_rekening');
            $table->string('nama_bank');
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->string('catatan_admin')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('mitra_id')->references('id')->on('mitras');
        });

        // ── Topup Requests (via Tokopay) ────────────────────────────────────
        Schema::create('topup_requests', function (Blueprint $table) {
            $table->id();
            $table->string('user_type');
            $table->string('user_id');
            $table->decimal('jumlah', 15, 2);
            $table->string('tokopay_ref')->nullable();
            $table->enum('status', ['pending','success','failed'])->default('pending');
            $table->json('tokopay_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // ── PPOB Transactions (via Digiflazz) ──────────────────────────────
        Schema::dropIfExists('ppob_transactions');
        Schema::create('ppob_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('user_type');
            $table->string('user_id');
            $table->enum('jenis_produk', ['pulsa', 'paket_data', 'token_listrik']);
            $table->string('nomor_tujuan');
            $table->string('kode_produk');
            $table->string('nama_produk');
            $table->decimal('harga_modal', 15, 2);
            $table->decimal('harga_jual', 15, 2);
            $table->decimal('margin_zasha', 15, 2);
            $table->string('digiflazz_ref')->nullable();
            $table->string('sn')->nullable();
            $table->enum('status', ['pending','sukses','gagal'])->default('pending');
            $table->json('digiflazz_response')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppob_transactions');
        Schema::dropIfExists('topup_requests');
        Schema::dropIfExists('withdrawal_requests');
        Schema::dropIfExists('wallet_transfers');
        Schema::dropIfExists('disputes');
        Schema::dropIfExists('ratings');
    }
};
