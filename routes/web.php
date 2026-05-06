<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PpobController;

Route::get('/', [DashboardController::class, 'index']);

// Mitra Routes
Route::prefix('admin/mitra')->name('mitra.')->group(function () {
    Route::get('/dashboard', [MitraController::class, 'dashboard'])->name('dashboard');
    Route::resource('/', MitraController::class)->parameters(['' => 'mitra']);
});

Route::get('/admin/order/create', [OrderController::class, 'create'])->name('admin.order.create');
Route::post('/admin/order', [OrderController::class, 'store'])->name('admin.order.store');
Route::post('/admin/order/{id}/update-status', [OrderController::class, 'updateStatus'])->name('admin.order.updateStatus');
Route::get('/admin/pelanggan/tracker/{id}', [OrderController::class, 'tracker'])->name('orders.tracker');
Route::patch('/admin/order/{id}/konfirmasi-selesai', [OrderController::class, 'konfirmasiSelesai'])->name('orders.konfirmasiSelesai');
Route::post('/admin/order-item/{id}/update', [OrderController::class, 'updateItem'])->name('mitra.updateItem');
Route::post('/admin/order/{order_id}/tambah-item-service', [MitraController::class, 'tambahItemService'])->name('mitra.tambahItemService');

Route::post('/ppob/transaction', [PpobController::class, 'createTransaction'])->name('ppob.transaction');
Route::get('/admin/dashboard/finance', [FinanceController::class, 'index'])->name('admin.finance.dashboard');
Route::get('/admin/finance/deposit', [FinanceController::class, 'deposit'])->name('admin.finance.deposit');
Route::post('/admin/finance/deposit', [FinanceController::class, 'storeDeposit'])->name('admin.finance.storeDeposit');
