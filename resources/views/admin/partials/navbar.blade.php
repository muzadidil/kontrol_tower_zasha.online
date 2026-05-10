<div class="container-fluid d-flex align-items-center justify-content-between p-0">
    <div class="d-flex align-items-center">
        <a class="navbar-brand fw-bold me-4" href="#" style="color: #005aa9; font-size: 1.4rem;">
            ZASHA <span class="text-warning">TOWER</span>
        </a>
        <form class="d-none d-md-flex">
            <div class="input-group bg-light rounded-pill px-3 border">
                <input type="text" class="form-control bg-light border-0 small" placeholder="Cari data..." style="width: 250px;">
                <button class="btn btn-link text-muted" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>

    <ul class="navbar-nav align-items-center flex-row">
        <li class="nav-item me-3 dropdown">
            @php
                $pendingVerif = \Illuminate\Support\Facades\DB::table('mitras')->where('status_verifikasi', 'pending_review')->count()
                    + \Illuminate\Support\Facades\DB::table('pelanggans')->where('status_verifikasi', 0)->count();
                $pendingWithdrawal = \Illuminate\Support\Facades\DB::table('withdrawal_requests')->where('status', 'pending')->count();
                $pendingTopup = \Illuminate\Support\Facades\DB::table('topup_requests')->where('status', 'pending')->count();
                $openDisputes = \Illuminate\Support\Facades\DB::table('disputes')->where('status', 'open')->count();
                $totalNotif = $pendingVerif + $pendingWithdrawal + $pendingTopup + $openDisputes;
            @endphp
            <a class="nav-link text-muted position-relative p-2" href="#" data-bs-toggle="dropdown">
                <i class="fa-regular fa-bell fs-5"></i>
                @if($totalNotif > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 8px;">{{ $totalNotif }}</span>
                @endif
            </a>
            @if($totalNotif > 0)
            <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 300px;">
                @if($pendingVerif > 0)
                <li><a class="dropdown-item small" href="{{ route('admin.verification.index') }}">
                    <i class="fas fa-check-circle text-warning me-2"></i>{{ $pendingVerif }} Verifikasi Menunggu
                </a></li>
                @endif
                @if($pendingWithdrawal > 0)
                <li><a class="dropdown-item small" href="{{ route('admin.finance.withdrawal') }}">
                    <i class="fas fa-money-bill-wave text-info me-2"></i>{{ $pendingWithdrawal }} Penarikan Dana Menunggu
                </a></li>
                @endif
                @if($pendingTopup > 0)
                <li><a class="dropdown-item small" href="{{ route('admin.finance.topup.index') }}">
                    <i class="fas fa-arrow-up-to-line text-success me-2"></i>{{ $pendingTopup }} Topup Dikonfirmasi
                </a></li>
                @endif
                @if($openDisputes > 0)
                <li><a class="dropdown-item small" href="{{ route('admin.wfh.index', ['status' => 'dispute']) }}">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>{{ $openDisputes }} Dispute Terbuka
                </a></li>
                @endif
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item small text-muted text-center" href="#">Tandai semua dibaca</a></li>
            </ul>
            @endif
        </li>
        <div class="vr mx-3 text-gray-300" style="height: 25px;"></div>
        <li class="nav-item">
            <div class="d-flex align-items-center">
                <div class="text-end me-3 d-none d-sm-block">
                    <p class="mb-0 small fw-bold" style="line-height: 1;">Administrator</p>
                    <small class="text-success" style="font-size: 10px;">Status: Online</small>
                </div>
                <img class="rounded-circle border border-2 border-primary" src="https://ui-avatars.com/api/?name=Admin&background=005aa9&color=fff" width="40">
            </div>
        </li>
    </ul>
</div>