@extends('layouts.admin')
@section('title', 'Game Top-Up')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-gamepad text-warning me-2"></i>Game Top-Up</h4>
            <p class="text-muted small mb-0">Kelola produk top-up game, voucher, dan pulsa via Digiflazz.</p>
        </div>
        <a href="{{ route('admin.game-topup.import') }}" class="btn btn-warning fw-semibold">
            <i class="fas fa-cloud-download-alt me-1"></i> Import dari Digiflazz
        </a>
    </div>

    @foreach(['success','error','warning'] as $t)
        @if(session($t)) <div class="alert alert-{{$t}} alert-dismissible fade show">{{ session($t) }}<button class="btn-close" data-bs-dismiss="alert"></button></div> @endif
    @endforeach

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Cari Produk</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama / SKU code..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Filter Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $k)
                            <option value="{{ $k->kode }}" {{ request('kategori') === $k->kode ? 'selected' : '' }}>{{ $k->nama }} ({{ $k->tipe }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-warning">Filter</button>
                    <a href="{{ route('admin.game-topup.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Quick Sync per Kategori --}}
    @if($kategoris->isNotEmpty())
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <h6 class="fw-bold small mb-2">Sync Harga (refresh dari Digiflazz tanpa tambah produk baru)</h6>
            <div class="d-flex gap-2 flex-wrap">
                @foreach($kategoris as $k)
                <form action="{{ route('admin.game-topup.sync') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="kategori_id" value="{{ $k->id }}">
                    <button class="btn btn-sm btn-outline-info" onclick="return confirm('Sync harga untuk {{ $k->nama }}?')">
                        <i class="fas fa-sync-alt me-1"></i> {{ $k->nama }}
                    </button>
                </form>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kategori</th>
                        <th>Produk</th>
                        <th>SKU</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-center">Profit %</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($layanans as $l)
                    <tr>
                        <td class="small">
                            <span class="fw-semibold">{{ $l->kategori->nama }}</span>
                            <span class="badge bg-secondary ms-1">{{ $l->kategori->tipe }}</span>
                        </td>
                        <td class="small">{{ $l->layanan }}</td>
                        <td><code class="small">{{ $l->provider_id }}</code></td>
                        <td class="text-end small fw-semibold">Rp {{ number_format($l->harga, 0, ',', '.') }}</td>
                        <td class="text-center small">{{ $l->profit }}%</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $l->status === 'available' ? 'success' : 'secondary' }}">{{ $l->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada produk. <a href="{{ route('admin.game-topup.import') }}">Import dari Digiflazz dulu</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
