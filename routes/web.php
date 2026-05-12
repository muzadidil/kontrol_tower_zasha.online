<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\AlamatController;
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
use App\Http\Controllers\AdminOrderMonitoringController;
use App\Http\Controllers\AdminArsipPesananController;
use App\Http\Controllers\AdminVerificationController;
use App\Http\Controllers\AdminTopupController;
use App\Http\Controllers\Auth\MitraLoginController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Mitra\MitraDashboardController;
use App\Http\Controllers\Mitra\MitraOrderController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Pelanggan\PelangganOrderTrackingController;
use App\Http\Controllers\Pelanggan\WfhController as PelangganWfhController;
use App\Http\Controllers\Mitra\WfhController as MitraWfhController;
use App\Http\Controllers\Admin\WfhController as AdminWfhController;
use App\Http\Controllers\Pelanggan\JastipController as PelangganJastipController;
use App\Http\Controllers\Mitra\JastipController as MitraJastipController;
use App\Http\Controllers\Admin\JastipController as AdminJastipNewController;
use App\Http\Controllers\Pelanggan\TenagaController as PelangganTenagaController;
use App\Http\Controllers\Mitra\TenagaController as MitraTenagaController;
use App\Http\Controllers\Admin\TenagaController as AdminTenagaController;
use App\Http\Controllers\Pelanggan\ServiceController as PelangganServiceController;
use App\Http\Controllers\Mitra\ServiceController as MitraServiceController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\PpobController as AdminPpobController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\PushNotificationController;

// Authentication Routes (public)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register'])->name('register.submit');
Route::match(['get','post'], '/logout', [LoginController::class, 'logout'])->name('logout');

// Katalog publik (tidak perlu login)
Route::get('/katalog/{id_kategori}', [PelangganController::class, 'katalog'])->name('pelanggan.katalog');

// Pelanggan Routes (butuh login)
Route::middleware('auth:pelanggan')->group(function () {
    Route::get('/', [PelangganController::class, 'index'])->name('pelanggan.dashboard');
    Route::get('/notifikasi', [PelangganController::class, 'notifikasi'])->name('pelanggan.notifikasi');
    Route::get('/pesanan', function () { return redirect()->route('pelanggan.riwayat.index'); })->name('pelanggan.pesanan');
    Route::get('/profil', [ProfilController::class, 'index'])->name('pelanggan.profil');
    Route::post('/profil/update', [ProfilController::class, 'updateProfil'])->name('pelanggan.profil.update');
    Route::post('/profil/foto', [ProfilController::class, 'uploadFoto'])->name('pelanggan.profil.foto');
    Route::post('/profil/alamat', [ProfilController::class, 'storeAlamat'])->name('profil.alamat.store');
    // Buku Alamat
    Route::get('/alamat', [AlamatController::class, 'index'])->name('pelanggan.alamat');
    Route::post('/alamat', [AlamatController::class, 'store'])->name('pelanggan.alamat.store');
    Route::put('/alamat/{id}', [AlamatController::class, 'update'])->name('pelanggan.alamat.update');
    Route::delete('/alamat/{id}', [AlamatController::class, 'destroy'])->name('pelanggan.alamat.destroy');
    Route::post('/alamat/{id}/utama', [AlamatController::class, 'setUtama'])->name('pelanggan.alamat.utama');

    // Katalog & Detail Mitra
    Route::get('/detail/mitra/{id}', [PelangganController::class, 'detailMitra'])->name('pelanggan.detail.mitra');
    Route::get('/detail/jastip/{id}', [PelangganController::class, 'detailJastip'])->name('pelanggan.detail.jastip');
    Route::post('/pesan', [PelangganController::class, 'simpanPesanan'])->name('pelanggan.pesan');

    // Review
    Route::get('/review/{id_order}', [PelangganController::class, 'review'])->name('pelanggan.review');
    Route::post('/review/kirim', [PelangganController::class, 'kirimReview'])->name('pelanggan.review.kirim');
    Route::get('/dompet', [PelangganController::class, 'dompet'])->name('pelanggan.dompet');
    Route::post('/dompet/topup', [PelangganController::class, 'topup'])->name('pelanggan.dompet.topup');
    Route::get('/invoice/{id}', [PelangganController::class, 'invoice'])->name('pelanggan.invoice');
    Route::get('/invoice/cek-status/{id}', [PelangganController::class, 'cekStatus']);
    Route::get('/topup', function () { return "Halaman Topup"; })->name('pelanggan.topup');

    // WFH Orders
    Route::prefix('wfh')->name('pelanggan.wfh.')->middleware(['profil.lengkap'])->group(function () {
        Route::get('/', [PelangganWfhController::class, 'index'])->name('index');
        Route::get('/order', [PelangganWfhController::class, 'create'])->name('create');
        Route::post('/order', [PelangganWfhController::class, 'store'])->name('store');
        Route::get('/{wfhOrder}', [PelangganWfhController::class, 'show'])->name('show');
        Route::post('/{wfhOrder}/konfirmasi', [PelangganWfhController::class, 'konfirmasi'])->name('konfirmasi');
        Route::post('/{wfhOrder}/dispute', [PelangganWfhController::class, 'dispute'])->name('dispute');
    });

    // Jastip Orders
    Route::prefix('jastip')->name('pelanggan.jastip.')->middleware(['profil.lengkap'])->group(function () {
        Route::get('/', [PelangganJastipController::class, 'index'])->name('index');
        Route::get('/order', [PelangganJastipController::class, 'create'])->name('create');
        Route::post('/order', [PelangganJastipController::class, 'store'])->name('store');
        Route::get('/{jastipOrder}', [PelangganJastipController::class, 'show'])->name('show');
        Route::post('/{jastipOrder}/konfirmasi', [PelangganJastipController::class, 'konfirmasi'])->name('konfirmasi');
        Route::post('/{jastipOrder}/dispute', [PelangganJastipController::class, 'dispute'])->name('dispute');
    });

    // Tenaga Orders
    Route::prefix('tenaga')->name('pelanggan.tenaga.')->middleware(['profil.lengkap'])->group(function () {
        Route::get('/', [PelangganTenagaController::class, 'index'])->name('index');
        Route::get('/order', [PelangganTenagaController::class, 'create'])->name('create');
        Route::post('/order', [PelangganTenagaController::class, 'store'])->name('store');
        Route::get('/{tenagaOrder}', [PelangganTenagaController::class, 'show'])->name('show');
        Route::post('/{tenagaOrder}/konfirmasi', [PelangganTenagaController::class, 'konfirmasi'])->name('konfirmasi');
        Route::post('/{tenagaOrder}/dispute', [PelangganTenagaController::class, 'dispute'])->name('dispute');
    });

    // Inden Orders
    Route::prefix('inden')->name('pelanggan.inden.')->middleware(['profil.lengkap'])->group(function () {
        Route::post('/order', [\App\Http\Controllers\Pelanggan\IndenController::class, 'store'])->name('store');
        Route::get('/{indenOrder}', [\App\Http\Controllers\Pelanggan\IndenController::class, 'show'])->name('show');
        Route::post('/{indenOrder}/bayar-dp', [\App\Http\Controllers\Pelanggan\IndenController::class, 'bayarDp'])->name('bayar-dp');
        Route::post('/{indenOrder}/bayar-pelunasan', [\App\Http\Controllers\Pelanggan\IndenController::class, 'bayarPelunasan'])->name('bayar-pelunasan');
        Route::post('/{indenOrder}/konfirmasi', [\App\Http\Controllers\Pelanggan\IndenController::class, 'konfirmasi'])->name('konfirmasi');
        Route::post('/{indenOrder}/dispute', [\App\Http\Controllers\Pelanggan\IndenController::class, 'dispute'])->name('dispute');
    });

    // Topup pelanggan (Tokopay)
    Route::prefix('topup-saldo')->name('pelanggan.topup.')->group(function () {
        Route::get('/', [TopupController::class, 'pelangganForm'])->name('form');
        Route::post('/', [TopupController::class, 'pelangganStore'])->name('store');
        Route::get('/riwayat', [TopupController::class, 'pelangganIndex'])->name('index');
    });

    // PPOB & Game Top-Up: REMOVED — fitur dihapus saat cleanup tabel kategoris/layanans

    // Service Orders
    Route::prefix('service')->name('pelanggan.service.')->middleware(['profil.lengkap'])->group(function () {
        Route::get('/', [PelangganServiceController::class, 'index'])->name('index');
        Route::get('/order', [PelangganServiceController::class, 'create'])->name('create');
        Route::post('/order', [PelangganServiceController::class, 'store'])->name('store');
        Route::get('/{serviceOrder}', [PelangganServiceController::class, 'show'])->name('show');
        Route::post('/{serviceOrder}/approve-harga', [PelangganServiceController::class, 'approveHarga'])->name('approve-harga');
        Route::post('/{serviceOrder}/konfirmasi', [PelangganServiceController::class, 'konfirmasi'])->name('konfirmasi');
        Route::post('/{serviceOrder}/dispute', [PelangganServiceController::class, 'dispute'])->name('dispute');
    });

    // Fitur Riwayat
    Route::prefix('riwayat')->name('pelanggan.riwayat.')->group(function () {
        Route::get('/', [RiwayatController::class, 'index'])->name('index');
        Route::get('/update-status', [RiwayatController::class, 'updateStatus'])->name('update');
        Route::post('/ulasan', [RiwayatController::class, 'kirimUlasan'])->name('ulasan');
    });

    // Order Tracking
    Route::get('/order-tracking/{tracking}', [PelangganOrderTrackingController::class, 'trackOrder'])->name('order-tracking');
    Route::get('/order-tracking/{tracking}/status', [PelangganOrderTrackingController::class, 'getStatus'])->name('order-tracking.status');
    Route::post('/order-tracking/selesai', [PelangganOrderTrackingController::class, 'confirmSelesai'])->name('order-tracking.selesai');
    Route::post('/order-tracking/belum-selesai', [PelangganOrderTrackingController::class, 'belumSelesai'])->name('order-tracking.belum-selesai');
});

// Mitra Auth Routes
Route::get('/mitra/login', [MitraLoginController::class, 'showLoginForm'])->name('mitra.login');
Route::post('/mitra/login', [MitraLoginController::class, 'login'])->name('mitra.login.submit');
Route::get('/mitra/register', [MitraLoginController::class, 'showRegisterForm'])->name('mitra.register');
Route::post('/mitra/register', [MitraLoginController::class, 'register'])->name('mitra.register.submit');
Route::match(['get','post'], '/mitra/logout', [MitraLoginController::class, 'logout'])->name('mitra.logout');

// Mitra Dashboard Routes (butuh login mitra)
Route::middleware('auth:mitra')->prefix('mitra')->name('mitra.')->group(function () {
    Route::get('/dashboard', [MitraDashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('/toggle-status', [MitraDashboardController::class, 'toggleStatus'])->name('toggle-status');

    // Order Tracking API (polling untuk notifikasi)
    Route::get('/api/notifikasi', [MitraOrderController::class, 'getNotifikasi'])->name('api.notifikasi');
    Route::get('/api/pending-order', [MitraOrderController::class, 'getPendingOrder'])->name('api.pending-order');

    // Order Management
    Route::post('/order/accept', [MitraOrderController::class, 'acceptOrder'])->name('order.accept');
    Route::post('/order/reject', [MitraOrderController::class, 'rejectOrder'])->name('order.reject');
    Route::post('/order/progress', [MitraOrderController::class, 'updateProgress'])->name('order.progress');

    Route::get('/pesanan', [MitraDashboardController::class, 'pesanan'])->name('pesanan');
    Route::get('/saldo', [MitraDashboardController::class, 'saldo'])->name('saldo');
    Route::post('/saldo/topup', [MitraDashboardController::class, 'topup'])->name('saldo.topup');
    Route::get('/profil', [MitraDashboardController::class, 'profil'])->name('profil');
    Route::post('/profil/foto', [MitraDashboardController::class, 'uploadFoto'])->name('profil.foto');

    // WFH Orders
    Route::prefix('wfh')->name('wfh.')->group(function () {
        Route::get('/', [MitraWfhController::class, 'index'])->name('index');
        Route::get('/{wfhOrder}', [MitraWfhController::class, 'show'])->name('show');
        Route::post('/{wfhOrder}/terima', [MitraWfhController::class, 'terima'])->name('terima');
        Route::post('/{wfhOrder}/tolak', [MitraWfhController::class, 'tolak'])->name('tolak');
        Route::post('/{wfhOrder}/kirim-file', [MitraWfhController::class, 'kirimFile'])->name('kirim-file');
    });

    // Jastip Orders
    Route::prefix('jastip')->name('jastip.')->group(function () {
        Route::get('/', [MitraJastipController::class, 'index'])->name('index');
        Route::get('/{jastipOrder}', [MitraJastipController::class, 'show'])->name('show');
        Route::post('/{jastipOrder}/terima', [MitraJastipController::class, 'terima'])->name('terima');
        Route::post('/{jastipOrder}/tolak', [MitraJastipController::class, 'tolak'])->name('tolak');
        Route::post('/stop/{stop}/tiba', [MitraJastipController::class, 'tibaStop'])->name('tiba-stop');
        Route::post('/item/{item}/check', [MitraJastipController::class, 'checklistItem'])->name('checklist-item');
        Route::post('/{jastipOrder}/mulai-antar', [MitraJastipController::class, 'mulaiAntar'])->name('mulai-antar');
        Route::post('/{jastipOrder}/diantar', [MitraJastipController::class, 'diantar'])->name('diantar');
    });

    // Topup mitra
    Route::prefix('topup-saldo')->name('topup.')->group(function () {
        Route::get('/', [TopupController::class, 'mitraForm'])->name('form');
        Route::post('/', [TopupController::class, 'mitraStore'])->name('store');
        Route::get('/riwayat', [TopupController::class, 'mitraIndex'])->name('index');
    });

    // Tenaga
    Route::prefix('tenaga')->name('tenaga.')->group(function () {
        Route::get('/', [MitraTenagaController::class, 'index'])->name('index');
        Route::get('/{tenagaOrder}', [MitraTenagaController::class, 'show'])->name('show');
        Route::post('/{tenagaOrder}/terima', [MitraTenagaController::class, 'terima'])->name('terima');
        Route::post('/{tenagaOrder}/tolak', [MitraTenagaController::class, 'tolak'])->name('tolak');
        Route::post('/{tenagaOrder}/mulai-kerja', [MitraTenagaController::class, 'mulaiKerja'])->name('mulai-kerja');
        Route::post('/{tenagaOrder}/selesai-kerja', [MitraTenagaController::class, 'selesaiKerja'])->name('selesai-kerja');
    });

    // Service
    Route::prefix('service')->name('service.')->group(function () {
        Route::get('/', [MitraServiceController::class, 'index'])->name('index');
        Route::get('/{serviceOrder}', [MitraServiceController::class, 'show'])->name('show');
        Route::post('/{serviceOrder}/terima', [MitraServiceController::class, 'terima'])->name('terima');
        Route::post('/{serviceOrder}/tolak', [MitraServiceController::class, 'tolak'])->name('tolak');
        Route::post('/{serviceOrder}/mulai-diagnosa', [MitraServiceController::class, 'mulaiDiagnosa'])->name('mulai-diagnosa');
        Route::post('/{serviceOrder}/submit-diagnosa', [MitraServiceController::class, 'submitDiagnosa'])->name('submit-diagnosa');
        Route::post('/{serviceOrder}/selesai-kerja', [MitraServiceController::class, 'selesaiKerja'])->name('selesai-kerja');
    });
});

// Push Notification Routes
Route::post('/push/subscribe', [PushNotificationController::class, 'subscribe'])->name('push.subscribe');
Route::post('/push/unsubscribe', [PushNotificationController::class, 'unsubscribe'])->name('push.unsubscribe');
Route::get('/push/public-key', [PushNotificationController::class, 'getPublicKey'])->name('push.public-key');

// Admin Auth Routes (public)
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::match(['get','post'], '/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Admin & Mitra Routes
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', [FinanceController::class, 'index'])->name('admin.dashboard');
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/api', [SettingController::class, 'updateApi'])->name('admin.settings.api');
    Route::post('/settings/operasional', [SettingController::class, 'updateOperasional'])->name('admin.settings.operasional');
    Route::post('/settings/legal', [SettingController::class, 'updateLegal'])->name('admin.settings.legal');
    Route::post('/settings/about', [SettingController::class, 'updateAbout'])->name('admin.settings.about');

    // Monitoring & Orders
    Route::get('/orders', [AdminOrderMonitoringController::class, 'index'])->name('admin.orders.index');
    Route::post('/orders/update-status', [AdminOrderMonitoringController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::get('/orders/arsip', [AdminArsipPesananController::class, 'index'])->name('admin.orders.arsip');
    Route::post('/orders/arsip/update', [AdminArsipPesananController::class, 'updateStatus'])->name('admin.orders.arsip.update');

    // Verifikasi
    Route::get('/verification', [AdminVerificationController::class, 'index'])->name('admin.verification.index');
    Route::post('/verification/approve', [AdminVerificationController::class, 'approve'])->name('admin.verification.approve');
    // Master Kategori (LEGACY - REMOVED): digantikan oleh Roles

    // Finance Management
    Route::get('/dashboard/finance', [FinanceController::class, 'index'])->name('admin.finance.dashboard');
    Route::get('/finance/deposit', [FinanceController::class, 'deposit'])->name('admin.finance.deposit');
    Route::post('/finance/deposit', [FinanceController::class, 'storeDeposit'])->name('admin.finance.storeDeposit');
    Route::get('/finance/withdrawal', [WithdrawalController::class, 'adminIndex'])->name('admin.finance.withdrawal');
    Route::post('/finance/withdrawal/{id}/approve', [WithdrawalController::class, 'approve'])->name('admin.finance.withdrawal.approve');
    Route::post('/finance/withdrawal/{id}/reject', [WithdrawalController::class, 'reject'])->name('admin.finance.withdrawal.reject');

    // Konfirmasi Topup (Mitra + Pelanggan)
    Route::get('/finance/topup', [AdminTopupController::class, 'index'])->name('admin.finance.topup.index');
    Route::get('/finance/topup/mitra/{id}', [AdminTopupController::class, 'processMitra'])->name('admin.finance.topup.mitra');
    Route::get('/finance/topup/pelanggan/{id}', [AdminTopupController::class, 'processPelanggan'])->name('admin.finance.topup.pelanggan');
    Route::get('/finance/topup/process/{id}', [AdminTopupController::class, 'process'])->name('admin.finance.topup.process');

    // Others
    Route::get('/jastip', [AdminJastipController::class, 'index'])->name('admin.jastip');
    // /kategori (LEGACY - REMOVED): digantikan oleh Roles
    Route::get('/monitor', [AdminMonitorController::class, 'index'])->name('admin.monitor');

    // Role & Feature Management
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)
        ->except(['show'])
        ->names([
            'index'   => 'admin.roles.index',
            'create'  => 'admin.roles.create',
            'store'   => 'admin.roles.store',
            'edit'    => 'admin.roles.edit',
            'update'  => 'admin.roles.update',
            'destroy' => 'admin.roles.destroy',
        ]);
    // Assign role ke mitra
    Route::post('/mitra/{mitra}/role', [\App\Http\Controllers\Admin\RoleController::class, 'assignToMitra'])
        ->name('admin.mitra.assignRole');
    // Toggle aktif/draft role
    Route::post('/roles/{role}/toggle-active', [\App\Http\Controllers\Admin\RoleController::class, 'toggleActive'])
        ->name('admin.roles.toggleActive');

    // WFH Admin
    Route::prefix('wfh')->name('admin.wfh.')->group(function () {
        Route::get('/', [AdminWfhController::class, 'index'])->name('index');
        Route::get('/{wfhOrder}', [AdminWfhController::class, 'show'])->name('show');
        Route::post('/{wfhOrder}/resolusi', [AdminWfhController::class, 'resolusiDispute'])->name('resolusi');
    });

    // Jastip Admin (modul baru)
    Route::prefix('jastip-monitoring')->name('admin.jastip.')->group(function () {
        Route::get('/', [AdminJastipNewController::class, 'index'])->name('index');
        Route::get('/{jastipOrder}', [AdminJastipNewController::class, 'show'])->name('show');
        Route::post('/{jastipOrder}/resolusi', [AdminJastipNewController::class, 'resolusiDispute'])->name('resolusi');
    });

    // Tenaga Admin
    Route::prefix('tenaga')->name('admin.tenaga.')->group(function () {
        Route::get('/', [AdminTenagaController::class, 'index'])->name('index');
        Route::get('/{tenagaOrder}', [AdminTenagaController::class, 'show'])->name('show');
        Route::post('/{tenagaOrder}/resolusi', [AdminTenagaController::class, 'resolusiDispute'])->name('resolusi');
    });

    // Service Admin
    Route::prefix('service')->name('admin.service.')->group(function () {
        Route::get('/', [AdminServiceController::class, 'index'])->name('index');
        Route::get('/{serviceOrder}', [AdminServiceController::class, 'show'])->name('show');
        Route::post('/{serviceOrder}/resolusi', [AdminServiceController::class, 'resolusiDispute'])->name('resolusi');
    });

    // PPOB Monitoring Admin
    Route::prefix('ppob')->name('admin.ppob.')->group(function () {
        Route::get('/', [AdminPpobController::class, 'index'])->name('index');
        Route::get('/{trx}', [AdminPpobController::class, 'show'])->name('show');
        Route::post('/{trx}/update-status', [AdminPpobController::class, 'updateStatus'])->name('updateStatus');
    });

    // Game Top-Up Admin: REMOVED — fitur dihapus saat cleanup tabel kategoris/layanans
    Route::post('/monitor/{id}/force-logout', [AdminMonitorController::class, 'forceLogout'])->name('admin.monitor.force_logout');

    Route::prefix('mitra')->name('admin.mitra.')->group(function () {
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

// Webhooks (public, no auth)
Route::post('/webhook/tokopay', [TopupController::class, 'webhook'])->name('webhook.tokopay');
Route::post('/webhook/digiflazz', function (\Illuminate\Http\Request $request) {
    app(\App\Services\PpobService::class)->handleWebhook($request->all());
    return response()->json(['status' => 'ok']);
})->name('webhook.digiflazz');
