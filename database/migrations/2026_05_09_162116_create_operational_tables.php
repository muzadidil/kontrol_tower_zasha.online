<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Kategori Pekerjaan
        Schema::dropIfExists('kategori_pekerjaan');
        Schema::create('kategori_pekerjaan', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->string('nama_kategori');
            $table->string('satuan')->default('Jam');
            $table->text('svg_kategori')->nullable();
            $table->text('skema_tarif')->nullable();
            $table->timestamps();
        });

        // 2. Master Satuan
        Schema::dropIfExists('master_satuan');
        Schema::create('master_satuan', function (Blueprint $table) {
            $table->id('id_satuan');
            $table->string('nama_satuan');
            $table->timestamps();
        });

        // 3. Mitra (tabel operasional, beda dari mitras)
        Schema::dropIfExists('mitra');
        Schema::create('mitra', function (Blueprint $table) {
            $table->id('id_mitra');
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->string('nama_panggilan');
            $table->string('nama_asli')->nullable();
            $table->string('nama_asli_baru')->nullable();
            $table->string('foto_mitra')->nullable();
            $table->string('no_wa')->nullable();
            $table->string('no_wa_baru')->nullable();
            $table->string('status_mitra')->default('aktif');
            $table->string('status_verifikasi')->default('Pending');
            $table->decimal('tarif_per_jam', 15, 2)->default(0);
            $table->decimal('tarif_per_hari', 15, 2)->default(0);
            $table->decimal('biaya_service_standar', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->text('deskripsi_singkat')->nullable();
            $table->string('jenis_kendaraan')->nullable();
            $table->string('kendaraan_baru')->nullable();
            $table->string('warna_kendaraan')->nullable();
            $table->string('warna_baru')->nullable();
            $table->string('plat_nomor')->nullable();
            $table->string('plat_pengajuan')->nullable();
            $table->text('alamat')->nullable();
            $table->text('alamat_pengajuan')->nullable();
            $table->decimal('lat_mitra', 10, 7)->default(0);
            $table->decimal('lng_mitra', 10, 7)->default(0);
            $table->decimal('lat_pengajuan', 10, 7)->default(0);
            $table->decimal('lng_pengajuan', 10, 7)->default(0);
            $table->timestamps();

            $table->foreign('id_kategori')->references('id_kategori')->on('kategori_pekerjaan')->onDelete('set null');
        });

        // 4. Mitra Jastip (driver)
        Schema::dropIfExists('mitra_jastip');
        Schema::create('mitra_jastip', function (Blueprint $table) {
            $table->id('id_driver');
            $table->string('nama_driver');
            $table->string('nama_asli')->nullable();
            $table->string('nama_asli_baru')->nullable();
            $table->string('foto_driver')->nullable();
            $table->string('no_wa')->nullable();
            $table->string('no_wa_baru')->nullable();
            $table->string('status_kerja')->default('Non-aktif');
            $table->string('status_verifikasi')->default('Pending');
            $table->string('jenis_kendaraan')->nullable();
            $table->string('kendaraan_baru')->nullable();
            $table->string('warna_kendaraan')->nullable();
            $table->string('warna_baru')->nullable();
            $table->string('plat_nomor')->nullable();
            $table->string('plat_pengajuan')->nullable();
            $table->string('status_plat')->default('Normal');
            $table->timestamps();
        });

        // 5. Pesanan Mitra (jasa — dipakai RiwayatController)
        Schema::dropIfExists('pesanan_mitra');
        Schema::create('pesanan_mitra', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_mitra')->nullable();
            $table->timestamp('tanggal_pesanan')->useCurrent();
            $table->string('status_pesanan')->default('Menunggu');
            $table->string('metode_pembayaran')->default('COD');
            $table->decimal('total_pesanan', 15, 2)->default(0);
            $table->decimal('biaya_jasa', 15, 2)->default(0);
            $table->decimal('ongkir', 15, 2)->default(0);
            $table->decimal('kode_unik', 10, 0)->default(0);
            $table->integer('durasi')->default(1);
            $table->text('keterangan')->nullable();
            $table->integer('rating')->default(0);
            $table->text('ulasan')->nullable();
            $table->timestamps();

            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggans')->onDelete('cascade');
            $table->foreign('id_mitra')->references('id_mitra')->on('mitra')->onDelete('set null');
        });

        // 6. Pesanan Jastip
        Schema::dropIfExists('pesanan_jastip');
        Schema::create('pesanan_jastip', function (Blueprint $table) {
            $table->id('id_jastip');
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_mitra')->nullable();
            $table->timestamp('waktu_order')->useCurrent();
            $table->string('status_jastip')->default('Mencari Driver');
            $table->string('metode_pembayaran')->default('COD');
            $table->text('lokasi_asal')->nullable();
            $table->text('daftar_belanja')->nullable();
            $table->decimal('total_harga_barang', 12, 2)->default(0);
            $table->decimal('ongkir', 12, 2)->default(0);
            $table->decimal('total_admin_lokasi', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggans')->onDelete('cascade');
            $table->foreign('id_mitra')->references('id_driver')->on('mitra_jastip')->onDelete('set null');
        });

        // 7. Pesanan (dipakai AdminOrderMonitoringController — format lama)
        Schema::dropIfExists('pesanan');
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_mitra')->nullable();
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->string('kategori_jasa')->nullable();
            $table->timestamp('tanggal_pesanan')->useCurrent();
            $table->string('status_pesanan')->default('Menunggu');
            $table->string('metode_pembayaran')->default('COD');
            $table->decimal('total_pesanan', 15, 2)->default(0);
            $table->decimal('biaya_jasa', 15, 2)->default(0);
            $table->decimal('ongkir', 15, 2)->default(0);
            $table->decimal('kode_unik', 10, 0)->default(0);
            $table->integer('rating')->default(0);
            $table->text('ulasan')->nullable();
            $table->timestamps();

            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggans')->onDelete('cascade');
            $table->foreign('id_mitra')->references('id_mitra')->on('mitra')->onDelete('set null');
        });

        // 8. Dompet Pelanggan
        Schema::dropIfExists('dompet_pelanggan');
        Schema::create('dompet_pelanggan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pelanggan');
            $table->decimal('nominal', 15, 2);
            $table->string('status')->default('pending');
            $table->timestamp('waktu_request')->useCurrent();
            $table->timestamps();

            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggans')->onDelete('cascade');
        });

        // 9. View "pelanggan" (alias pelanggans — untuk query admin lama)
        DB::statement('DROP VIEW IF EXISTS `pelanggan`');
        DB::statement('CREATE VIEW `pelanggan` AS SELECT * FROM `pelanggans`');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('DROP VIEW IF EXISTS `pelanggan`');
        Schema::dropIfExists('dompet_pelanggan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('pesanan_jastip');
        Schema::dropIfExists('pesanan_mitra');
        Schema::dropIfExists('mitra_jastip');
        Schema::dropIfExists('mitra');
        Schema::dropIfExists('master_satuan');
        Schema::dropIfExists('kategori_pekerjaan');
        Schema::enableForeignKeyConstraints();
    }
};
