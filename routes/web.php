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
// --- TAMBAHAN BARU: Import Controller Admin Monitor ---
use App\Http\Controllers\AdminMonitorController; 

// Pelanggan Routes
Route::get('/', [PelangganController::class, 'index'])->name('pelanggan.dashboard');
Route::get('/notifikasi', function () { return "Halaman Notifikasi"; })->name('pelanggan.notifikasi');
Route::get('/pesanan', function () { return redirect()->route('pelanggan.riwayat.index'); })->name('pelanggan.pesanan');
Route::get('/dompet', function () { return "Halaman Dompet"; })->name('pelanggan.dompet');
Route::get('/profil', function () { return "Halaman Profil"; })->name('pelanggan.profil');
Route::get('/topup', function () { return "Halaman Topup"; })->name('pelanggan.topup');
Route::get('/katalog/{id_kategori}', function ($id) { return "Halaman Katalog ID: " . $id; })->name('pelanggan.katalog');

// Fitur Riwayat
Route::prefix('riwayat')->name('pelanggan.riwayat.')->group(function () {
    Route::get('/', [RiwayatController::class, 'index'])->name('index');
    Route::get('/update-status', [RiwayatController::class, 'updateStatus'])->name('update');
    Route::post('/ulasan', [RiwayatController::class, 'kirimUlasan'])->name('ulasan');
});

// Admin & Mitra Routes
Route::prefix('admin')->group(function () {
    // Dashboard Alias for Sidebar
    Route::get('/dashboard', [FinanceController::class, 'index'])->name('admin.dashboard');
    Route::get('/jastip', [AdminJastipController::class, 'index'])->name('admin.jastip');
    
    // --- TAMBAHAN BARU: Admin Monitor (Radar Mitra) ---
    Route::get('/monitor', [AdminMonitorController::class, 'index'])->name('admin.monitor');
    Route::post('/monitor/{id}/force-logout', [AdminMonitorController::class, 'forceLogout'])->name('admin.monitor.force_logout');
    // --------------------------------------------------

    // Mitra Management
    Route::prefix('mitra')->name('mitra.')->group(function () {
        Route::get('/dashboard', [MitraController::class, 'dashboard'])->name('dashboard');
        Route::resource('/', MitraController::class)->parameters(['' => 'mitra'])->names([
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'show' => 'show',
            'edit' => 'edit',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);
    });

    // Order Management
    Route::get('/order/create', [OrderController::class, 'create'])->name('admin.order.create');
    Route::post('/order', [OrderController::class, 'store'])->name('admin.order.store');
    Route::post('/order/{id}/update-status', [OrderController::class, 'updateStatus'])->name('admin.order.updateStatus');
    Route::get('/pelanggan/tracker/{id}', [OrderController::class, 'tracker'])->name('orders.tracker');
    Route::patch('/order/{id}/konfirmasi-selesai', [OrderController::class, 'konfirmasiSelesai'])->name('orders.konfirmasiSelesai');
    Route::post('/order-item/{id}/update', [OrderController::class, 'updateItem'])->name('mitra.updateItem');
    Route::post('/order/{order_id}/tambah-item-service', [MitraController::class, 'tambahItemService'])->name('mitra.tambahItemService');

    // Finance Management
    Route::get('/dashboard/finance', [FinanceController::class, 'index'])->name('admin.finance.dashboard');
    Route::get('/finance/deposit', [FinanceController::class, 'deposit'])->name('admin.finance.deposit');
    Route::post('/finance/deposit', [FinanceController::class, 'storeDeposit'])->name('admin.finance.storeDeposit');
    Route::get('/finance/withdrawal', [WithdrawalController::class, 'adminIndex'])->name('admin.finance.withdrawal');
    Route::post('/finance/withdrawal/{id}/approve', [WithdrawalController::class, 'approve'])->name('admin.finance.withdrawal.approve');
    Route::post('/finance/withdrawal/{id}/reject', [WithdrawalController::class, 'reject'])->name('admin.finance.withdrawal.reject');
});

// PPOB & Wallet
Route::post('/ppob/transaction', [PpobController::class, 'createTransaction'])->name('ppob.transaction');
Route::post('/mitra/transfer', [WalletTransferController::class, 'transfer'])->name('mitra.transfer');
Route::get('/mitra/withdrawal', [WithdrawalController::class, 'index'])->name('mitra.withdrawal');
Route::post('/mitra/withdrawal', [WithdrawalController::class, 'request'])->name('mitra.withdrawal.request');