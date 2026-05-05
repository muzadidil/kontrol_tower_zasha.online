<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MitraController;

Route::get('/', function () {
    return view('admin.dashboard');
});

Route::get('/admin/mitra', [MitraController::class, 'index']);
