<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MitraController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PpobController;

Route::get('/', function () {
    return view('admin.dashboard');
});

Route::get('/admin/mitra/dashboard', [MitraController::class, 'dashboard'])->name('admin.mitra.dashboard');
Route::get('/admin/mitra', [MitraController::class, 'index']);
Route::get('/admin/order/create', [OrderController::class, 'create'])->name('admin.order.create');
Route::post('/admin/order', [OrderController::class, 'store'])->name('admin.order.store');
Route::post('/admin/order/{id}/update-status', [OrderController::class, 'updateStatus'])->name('admin.order.updateStatus');
Route::get('/admin/pelanggan/tracker/{id}', [OrderController::class, 'tracker'])->name('orders.tracker');
Route::patch('/admin/order/{id}/konfirmasi-selesai', [OrderController::class, 'konfirmasiSelesai'])->name('orders.konfirmasiSelesai');
Route::post('/admin/order-item/{id}/update', [OrderController::class, 'updateItem'])->name('mitra.updateItem');
Route::post('/admin/order/{order_id}/tambah-item-service', [MitraController::class, 'tambahItemService'])->name('mitra.tambahItemService');

Route::post('/ppob/transaction', [PpobController::class, 'createTransaction'])->name('ppob.transaction');
Route::post('/api/ppob/webhook', [PpobController::class, 'webhook'])->name('ppob.webhook');
