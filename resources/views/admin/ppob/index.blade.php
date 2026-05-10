@extends('layouts.admin')
@section('title', 'Monitoring PPOB')

@section('content')
<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4"><i class="fas fa-mobile-alt text-warning me-2"></i>Monitoring PPOB (Pulsa, Paket, Token)</h4>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="text-muted small">Total Transaksi</div>
                    <div class="fw-bold fs-4">{{ number_format($stats['total_trx'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="text-muted small">Total Omzet</div>
                    <div class="fw-bold fs-5 text-info">Rp {{ number_format($stats['total_omzet'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="text-muted small">Profit Zasha (Margin)</div>
                    <div class="fw-bold fs-5 text-success">Rp {{ number_format($stats['total_margin'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between small">
                        <span class="text-success">Sukses: {{ $stats['sukses'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-warning">Pending: {{ $stats['pending'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-danger">Gagal: {{ $stats['gagal'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Ref ID / Nomor / Produk..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
                        <option value="sukses" {{ request('status')==='sukses'?'selected':'' }}>Sukses</option>
                        <option value="gagal" {{ request('status')==='gagal'?'selected':'' }}>Gagal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="jenis" class="form-select">
                        <option value="">Semua Jenis</option>
                        <option value="pulsa" {{ request('jenis')==='pulsa'?'selected':'' }}>Pulsa</option>
                        <option value="paket_data" {{ request('jenis')==='paket_data'?'selected':'' }}>Paket Data</option>
                        <option value="token_listrik" {{ request('jenis')==='token_listrik'?'selected':'' }}>Token Listrik</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="user_type" class="form-select">
                        <option value="">Semua Pembeli</option>
                        <option value="pelanggan" {{ request('user_type')==='pelanggan'?'selected':'' }}>Pelanggan</option>
                        <option value="mitra" {{ request('user_type')==='mitra'?'selected':'' }}>Mitra</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-warning">Filter</button>
                    <a href="{{ route('admin.ppob.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ref ID</th>
                        <th>Pembeli</th>
                        <th>Produk</th>
                        <th>Tujuan</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-end">Margin</th>
                        <th class="text-center">Status</th>
                        <th>Waktu</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trxs as $trx)
                    @php
                        $bm = ['pending'=>'warning','sukses'=>'success','gagal'=>'danger'];
                        $jenisIcon = ['pulsa'=>'phone','paket_data'=>'wifi','token_listrik'=>'bolt'];
                    @endphp
                    <tr>
                        <td><code class="small">{{ $trx->digiflazz_ref }}</code></td>
                        <td class="small">
                            <span class="badge bg-{{ $trx->user_type === 'mitra' ? 'info' : 'secondary' }}">{{ ucfirst($trx->user_type) }}</span>
                            #{{ $trx->user_id }}
                        </td>
                        <td class="small">
                            <i class="fas fa-{{ $jenisIcon[$trx->jenis_produk] ?? 'box' }} text-warning me-1"></i>
                            {{ $trx->nama_produk }}
                        </td>
                        <td class="small font-monospace">{{ $trx->nomor_tujuan }}</td>
                        <td class="text-end small">Rp {{ number_format($trx->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-end small fw-semibold text-success">Rp {{ number_format($trx->margin_zasha, 0, ',', '.') }}</td>
                        <td class="text-center"><span class="badge bg-{{ $bm[$trx->status] ?? 'secondary' }}">{{ ucfirst($trx->status) }}</span></td>
                        <td class="small text-muted">{{ $trx->created_at->format('d M H:i') }}</td>
                        <td><a href="{{ route('admin.ppob.show', $trx->id) }}" class="btn btn-outline-warning btn-sm">Detail</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada transaksi PPOB.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $trxs->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
