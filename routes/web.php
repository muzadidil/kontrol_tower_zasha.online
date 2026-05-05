<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MitraController;
use App\Http\Controllers\OrderController;

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
