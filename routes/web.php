<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MitraController;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('admin.dashboard');
});

Route::get('/admin/mitra', [MitraController::class, 'index']);
Route::get('/admin/order/create', [OrderController::class, 'create'])->name('admin.order.create');
Route::post('/admin/order', [OrderController::class, 'store'])->name('admin.order.store');
Route::get('/admin/order', function() { return "List Order"; })->name('admin.order.index');
