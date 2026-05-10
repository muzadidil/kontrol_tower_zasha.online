@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;

    // Notifikasi count — pakai tabel yang benar (mitra single, pelanggans plural)
    $pendingVerifMitra = DB::table('mitra')->where('status_verifikasi', 'pending_review')->count();
    $pendingVerifPelanggan = DB::table('pelanggans')->where('status_verifikasi', 0)->count();
    $pendingVerif = $pendingVerifMitra + $pendingVerifPelanggan;

    $pendingWithdrawal = Schema::hasTable('withdrawal_requests')
        ? DB::table('withdrawal_requests')->where('status', 'pending')->count() : 0;

    $pendingTopup = Schema::hasTable('topup_mitra')
        ? DB::table('topup_mitra')->where('status_topup', 'Pending')->count() : 0;

    $openDisputes = Schema::hasTable('disputes')
        ? DB::table('disputes')->where('status', 'open')->count() : 0;

    $totalNotif = $pendingVerif + $pendingWithdrawal + $pendingTopup + $openDisputes;

    $adminName = auth()->user()->name ?? 'Administrator';
    $adminEmail = auth()->user()->email ?? '';
@endphp

<div class="container-fluid d-flex align-items-center justify-content-between p-0">

    {{-- ── BRAND + SEARCH ──────────────────────────────────── --}}
    <div class="d-flex align-items-center">
        <a class="navbar-brand fw-bold me-4" href="{{ route('admin.dashboard') }}" style="color: #005aa9; font-size: 1.4rem;">
            ZASHA <span class="text-warning">TOWER</span>
        </a>
        <form class="d-none d-md-flex" action="{{ route('admin.orders.index') }}" method="GET">
            <div class="input-group bg-light rounded-pill px-3 border">
                <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Cari order, mitra, pelanggan..." value="{{ request('search') }}" style="width: 280px;">
                <button class="btn btn-link text-muted" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>

    {{-- ── RIGHT: NOTIF + USER ─────────────────────────────── --}}
    <ul class="navbar-nav align-items-center flex-row mb-0">

        {{-- Notification Bell --}}
        <li class="nav-item me-2 dropdown">
            <a class="nav-link text-muted position-relative p-2" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-regular fa-bell fs-5"></i>
                @if($totalNotif > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px;">{{ $totalNotif }}</span>
                @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 320px;">
                <li class="px-3 py-2 border-bottom">
                    <strong class="small">Notifikasi Tugas Admin</strong>
                    @if($totalNotif > 0)
                        <span class="badge bg-danger ms-2">{{ $totalNotif }}</span>
                    @endif
                </li>

                @if($totalNotif === 0)
                    <li class="px-3 py-4 text-center text-muted small">
                        <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                        Tidak ada tugas pending. 🎉
                    </li>
                @else
                    @if($pendingVerif > 0)
                    <li><a class="dropdown-item small py-2" href="{{ route('admin.verification.index') }}">
                        <i class="fas fa-user-check text-warning me-2"></i>
                        <strong>{{ $pendingVerif }}</strong> Verifikasi Menunggu
                        <small class="text-muted d-block ps-4">{{ $pendingVerifMitra }} mitra, {{ $pendingVerifPelanggan }} pelanggan</small>
                    </a></li>
                    @endif

                    @if($pendingWithdrawal > 0)
                    <li><a class="dropdown-item small py-2" href="{{ route('admin.finance.withdrawal') }}">
                        <i class="fas fa-money-bill-wave text-info me-2"></i>
                        <strong>{{ $pendingWithdrawal }}</strong> Penarikan Dana Menunggu
                    </a></li>
                    @endif

                    @if($pendingTopup > 0)
                    <li><a class="dropdown-item small py-2" href="{{ route('admin.finance.topup.index') }}">
                        <i class="fas fa-cash-register text-success me-2"></i>
                        <strong>{{ $pendingTopup }}</strong> Topup Belum Dikonfirmasi
                    </a></li>
                    @endif

                    @if($openDisputes > 0)
                    <li><a class="dropdown-item small py-2" href="{{ route('admin.wfh.index', ['status' => 'dispute']) }}">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        <strong>{{ $openDisputes }}</strong> Dispute Terbuka
                    </a></li>
                    @endif
                @endif
            </ul>
        </li>

        {{-- Quick Actions --}}
        <li class="nav-item me-2 dropdown">
            <a class="nav-link text-muted p-2" href="#" data-bs-toggle="dropdown" title="Aksi Cepat">
                <i class="fas fa-bolt fs-5"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                <li class="px-3 py-2 border-bottom"><strong class="small">Aksi Cepat</strong></li>
                <li><a class="dropdown-item small" href="{{ route('admin.finance.deposit') }}"><i class="fas fa-plus-circle text-success me-2"></i>Deposit Manual</a></li>
                <li><a class="dropdown-item small" href="{{ route('admin.kategori.index') }}"><i class="fas fa-tags text-warning me-2"></i>Kelola Master Layanan</a></li>
                <li><a class="dropdown-item small" href="{{ route('admin.game-topup.index') }}"><i class="fas fa-gamepad text-info me-2"></i>Game Top-Up</a></li>
                <li><a class="dropdown-item small" href="{{ route('admin.monitor') }}"><i class="fas fa-desktop text-secondary me-2"></i>Session Monitor</a></li>
            </ul>
        </li>

        <div class="vr mx-2 text-gray-300" style="height: 25px;"></div>

        {{-- User Profile Dropdown --}}
        <li class="nav-item dropdown">
            <a class="nav-link p-1" href="#" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration:none;">
                <div class="d-flex align-items-center">
                    <div class="text-end me-3 d-none d-sm-block">
                        <p class="mb-0 small fw-bold text-dark" style="line-height: 1;">{{ $adminName }}</p>
                        <small class="text-success" style="font-size: 10px;">
                            <i class="fas fa-circle" style="font-size: 6px;"></i> Online
                        </small>
                    </div>
                    <img class="rounded-circle border border-2 border-primary" src="https://ui-avatars.com/api/?name={{ urlencode($adminName) }}&background=005aa9&color=fff" width="40" height="40">
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 220px;">
                <li class="px-3 py-2 border-bottom">
                    <strong class="small">{{ $adminName }}</strong>
                    @if($adminEmail) <div class="text-muted" style="font-size: 11px;">{{ $adminEmail }}</div> @endif
                </li>
                <li><a class="dropdown-item small" href="{{ route('admin.settings', ['tab' => 'about']) }}">
                    <i class="fas fa-cog text-secondary me-2"></i>Pengaturan
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item small text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</div>
