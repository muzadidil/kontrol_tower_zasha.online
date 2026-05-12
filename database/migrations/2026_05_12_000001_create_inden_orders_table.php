<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inden_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->unsignedBigInteger('pelanggan_id');
            $table->string('mitra_id');  // FK ke mitras.id (bukan id_mitra)

            $table->date('tanggal_pelaksanaan');
            $table->enum('tipe_durasi', ['jam', 'hari']);
            $table->decimal('durasi', 8, 2);
            $table->decimal('tarif', 15, 2);

            $table->decimal('total_biaya', 15, 2);
            $table->decimal('dp_amount', 15, 2);
            $table->decimal('pelunasan_amount', 15, 2);
            $table->decimal('komisi_zasha', 15, 2);
            $table->decimal('pendapatan_mitra', 15, 2);

            $table->enum('metode_pembayaran', ['saldo', 'transfer', 'cod']);
            $table->string('alamat_pelanggan');
            $table->decimal('pelanggan_lat', 10, 7)->nullable();
            $table->decimal('pelanggan_lng', 10, 7)->nullable();
            $table->text('keterangan_kerja')->nullable();

            $table->enum('status', [
                'menunggu_mitra', 'ditolak', 'menunggu_dp', 'dp_dibayar',
                'dikerjakan', 'menunggu_pelunasan', 'selesai', 'dispute',
            ])->default('menunggu_mitra');

            $table->timestamp('mitra_responded_at')->nullable();
            $table->string('alasan_penolakan')->nullable();
            $table->timestamp('auto_reject_at')->nullable();
            $table->timestamp('dp_paid_at')->nullable();
            $table->timestamp('pelunasan_paid_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamp('auto_konfirmasi_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pelanggan_id')->references('id_pelanggan')->on('pelanggans');
            // FK untuk mitra_id dikomen karena struktur kolom di tabel mitras menggunakan id_mitra bukan id
            // $table->foreign('mitra_id')->references('id_mitra')->on('mitras');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inden_orders');
    }
};
