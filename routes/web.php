<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PelangganController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PpobController;
use App\Http\Controllers\WalletTransferController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\AdminJastipController;
use App\Http\Controllers\AdminMonitorController;
use App\Http\Controllers\KategoriPekerjaanController;
use App\Http\Controllers\AdminOrderMonitoringController;
use App\Http\Controllers\AdminArsipPesananController;
use App\Http\Controllers\AdminVerificationController;
use App\Http\Controllers\AdminMasterKategoriController;
use App\Http\Controllers\AdminTopupController;

// Pelanggan Routes
Route::get('/', [PelangganController::class, 'index'])->name('pelanggan.dashboard');
Route::get('/notifikasi', function () { return "Halaman Notifikasi"; })->name('pelanggan.notifikasi');
Route::get('/pesanan', function () { return redirect()->route('pelanggan.riwayat.index'); })->name('pelanggan.pesanan');
Route::get('/profil', function () { return "Halaman Profil"; })->name('pelanggan.profil');
Route::get('/alamat', [PelangganController::class, 'alamat'])->name('pelanggan.alamat');
Route::get('/review/{id_order}', [PelangganController::class, 'review'])->name('pelanggan.review');
Route::post('/review/kirim', [PelangganController::class, 'kirimReview'])->name('pelanggan.review.kirim');
Route::get('/dompet', [PelangganController::class, 'dompet'])->name('pelanggan.dompet');
Route::post('/dompet/topup', [PelangganController::class, 'topup'])->name('pelanggan.dompet.topup');
Route::get('/invoice/{id}', [PelangganController::class, 'invoice'])->name('pelanggan.invoice');
Route::get('/invoice/cek-status/{id}', [PelangganController::class, 'cekStatus']);
Route::get('/katalog/{id_kategori}', [PelangganController::class, 'katalog'])->name('pelanggan.katalog');
Route::get('/topup', function () { return "Halaman Topup"; })->name('pelanggan.topup');

// Fitur Riwayat
Route::prefix('riwayat')->name('pelanggan.riwayat.')->group(function () {
    Route::get('/', [RiwayatController::class, 'index'])->name('index');
    Route::get('/update-status', [RiwayatController::class, 'updateStatus'])->name('update');
    Route::post('/ulasan', [RiwayatController::class, 'kirimUlasan'])->name('ulasan');
});

// Admin & Mitra Routes
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [FinanceController::class, 'index'])->name('admin.dashboard');
    
    // Monitoring & Orders
    Route::get('/orders', [AdminOrderMonitoringController::class, 'index'])->name('admin.orders.index');
    Route::post('/orders/update-status', [AdminOrderMonitoringController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::get('/orders/arsip', [AdminArsipPesananController::class, 'index'])->name('admin.orders.arsip');
    Route::post('/orders/arsip/update', [AdminArsipPesananController::class, 'updateStatus'])->name('admin.orders.arsip.update');

    // Verifikasi & Master Data
    Route::get('/verification', [AdminVerificationController::class, 'index'])->name('admin.verification.index');
    Route::post('/verification/approve', [AdminVerificationController::class, 'approve'])->name('admin.verification.approve');
    Route::get('/master-kategori', [AdminMasterKategoriController::class, 'index'])->name('admin.master.kategori');
    Route::post('/master-kategori/store', [AdminMasterKategoriController::class, 'store'])->name('admin.master.kategori.store');
    Route::delete('/master-kategori/{id}', [AdminMasterKategoriController::class, 'destroy'])->name('admin.master.kategori.destroy');
    Route::post('/master-kategori/unit', [AdminMasterKategoriController::class, 'storeUnit'])->name('admin.master.kategori.unit.store');
    Route::delete('/master-kategori/unit/{id}', [AdminMasterKategoriController::class, 'destroyUnit'])->name('admin.master.kategori.unit.destroy');

    // Finance Management
    Route::get('/dashboard/finance', [FinanceController::class, 'index'])->name('admin.finance.dashboard');
    Route::get('/finance/deposit', [FinanceController::class, 'deposit'])->name('admin.finance.deposit');
    Route::post('/finance/deposit', [FinanceController::class, 'storeDeposit'])->name('admin.finance.storeDeposit');
    Route::get('/finance/withdrawal', [WithdrawalController::class, 'adminIndex'])->name('admin.finance.withdrawal');
    Route::post('/finance/withdrawal/{id}/approve', [WithdrawalController::class, 'approve'])->name('admin.finance.withdrawal.approve');
    Route::post('/finance/withdrawal/{id}/reject', [WithdrawalController::class, 'reject'])->name('admin.finance.withdrawal.reject');
    
    // Konfirmasi Topup (YANG BARU)
    Route::get('/finance/topup', [AdminTopupController::class, 'index'])->name('admin.finance.topup.index');
    Route::get('/finance/topup/process/{id}', [AdminTopupController::class, 'process'])->name('admin.finance.topup.process');

    // Others
    Route::get('/jastip', [AdminJastipController::class, 'index'])->name('admin.jastip');
    Route::get('/kategori', [KategoriPekerjaanController::class, 'index'])->name('admin.kategori.index');
    Route::get('/monitor', [AdminMonitorController::class, 'index'])->name('admin.monitor');
    Route::post('/monitor/{id}/force-logout', [AdminMonitorController::class, 'forceLogout'])->name('admin.monitor.force_logout');

    Route::prefix('mitra')->name('mitra.')->group(function () {
        Route::get('/dashboard', [MitraController::class, 'dashboard'])->name('dashboard');
        Route::resource('/', MitraController::class)->parameters(['' => 'mitra'])->names([
            'index' => 'index', 'create' => 'create', 'store' => 'store', 
            'show' => 'show', 'edit' => 'edit', 'update' => 'update', 'destroy' => 'destroy',
        ]);
    });
});

Route::post('/ppob/transaction', [PpobController::class, 'createTransaction'])->name('ppob.transaction');
Route::post('/mitra/transfer', [WalletTransferController::class, 'transfer'])->name('mitra.transfer');
Route::get('/mitra/withdrawal', [WithdrawalController::class, 'index'])->name('mitra.withdrawal');
Route::post('/mitra/withdrawal', [WithdrawalController::class, 'request'])->name('mitra.withdrawal.request');
