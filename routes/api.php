<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KatalogController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\PpobApiController;
use App\Http\Controllers\Api\NotifikasiController;
use App\Http\Controllers\Api\Pelanggan\WfhApiController as PelangganWfhApi;
use App\Http\Controllers\Api\Mitra\WfhApiController as MitraWfhApi;

// ── Public ──────────────────────────────────────────────────
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register/pelanggan', [AuthController::class, 'registerPelanggan']);

Route::get('/katalog/kategori', [KatalogController::class, 'kategori']);
Route::get('/katalog/{kategori}/layanan', [KatalogController::class, 'layananByKategori']);
Route::get('/katalog/layanan/{masterLayanan}/mitra', [KatalogController::class, 'mitraByLayanan']);
Route::get('/mitra/{mitra}/detail', [KatalogController::class, 'mitraDetail']);

// ── Authenticated routes ────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/fcm-token', [NotifikasiController::class, 'updateFcmToken']);

    // Wallet
    Route::get('/wallet/saldo', [WalletController::class, 'saldo']);
    Route::post('/wallet/topup', [WalletController::class, 'topup']);
    Route::get('/wallet/topup/riwayat', [WalletController::class, 'riwayatTopup']);
    Route::post('/wallet/transfer', [WalletController::class, 'transferMitra']);
    Route::post('/wallet/withdrawal', [WalletController::class, 'requestWithdrawal']);

    // PPOB
    Route::get('/ppob', [PpobApiController::class, 'index']);
    Route::post('/ppob/transaksi', [PpobApiController::class, 'transaksi']);

    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index']);
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead']);

    // ── Pelanggan endpoints ─────────────────────────────────
    Route::prefix('pelanggan')->group(function () {
        Route::prefix('wfh')->group(function () {
            Route::get('/', [PelangganWfhApi::class, 'index']);
            Route::post('/', [PelangganWfhApi::class, 'store']);
            Route::get('/{wfhOrder}', [PelangganWfhApi::class, 'show']);
            Route::post('/{wfhOrder}/konfirmasi', [PelangganWfhApi::class, 'konfirmasi']);
            Route::post('/{wfhOrder}/dispute', [PelangganWfhApi::class, 'dispute']);
        });
    });

    // ── Mitra endpoints ─────────────────────────────────────
    Route::prefix('mitra')->group(function () {
        Route::prefix('wfh')->group(function () {
            Route::get('/', [MitraWfhApi::class, 'index']);
            Route::get('/{wfhOrder}', [MitraWfhApi::class, 'show']);
            Route::post('/{wfhOrder}/terima', [MitraWfhApi::class, 'terima']);
            Route::post('/{wfhOrder}/tolak', [MitraWfhApi::class, 'tolak']);
            Route::post('/{wfhOrder}/kirim-file', [MitraWfhApi::class, 'kirimFile']);
        });
    });
});
