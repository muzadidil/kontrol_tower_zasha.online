@extends('layouts.mitra')

@section('content')
{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="text-center">
        <div style="font-size: .75rem; opacity: .8; margin-bottom: 6px;">KEUANGAN</div>
        <h5 class="fw-bold m-0">Daftar Tarif Layanan</h5>
        <div class="mt-2" style="font-size: .8rem; opacity: .9;">
            Komisi Zasha: {{ number_format($komisiPersen, 1) }}% otomatis ditambahkan
        </div>
    </div>
</div>

<div style="padding: var(--fib-4, 16px); max-width: 720px; margin: 0 auto;">

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm">{{ session('error') }}</div>
    @endif

    {{-- Tombol Tambah --}}
    <div class="d-grid mb-3">
        <a href="{{ route('mitra.tarif.create') }}" class="btn btn-primary rounded-pill py-2 fw-bold">
            <i class="bi bi-plus-circle me-2"></i>Tambah Tarif Baru
        </a>
    </div>

    @if($tarifs->isEmpty())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-cash-stack" style="font-size: 3rem; color: #cbd5e1;"></i>
                <h6 class="mt-3 fw-bold">Belum Ada Tarif</h6>
                <p class="text-muted small mb-0">
                    Tambahkan tarif layanan Anda agar pelanggan tahu harga sebelum order.
                </p>
            </div>
        </div>
    @else
        @foreach($tarifs as $t)
            <div class="card border-0 shadow-sm rounded-4 mb-3 {{ !$t->is_aktif ? 'opacity-75' : '' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div class="flex-grow-1">
                            <div class="fw-bold" style="color: #1e293b;">{{ $t->keterangan }}</div>
                            <small class="text-muted">per {{ $t->satuan }}</small>
                        </div>
                        @if($t->is_aktif)
                            <span class="badge bg-success rounded-pill" style="font-size: .65rem;">Aktif</span>
                        @else
                            <span class="badge bg-secondary rounded-pill" style="font-size: .65rem;">Non-aktif</span>
                        @endif
                    </div>

                    {{-- Breakdown harga --}}
                    <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded-3"
                         style="background: #f8fafc; font-size: .8rem;">
                        <div>
                            <div class="text-muted">Anda terima</div>
                            <div class="fw-bold" style="color: #16a34a;">
                                Rp {{ number_format($t->nominal, 0, ',', '.') }}
                            </div>
                        </div>
                        <i class="bi bi-arrow-right text-muted"></i>
                        <div class="text-end">
                            <div class="text-muted">Pelanggan bayar</div>
                            <div class="fw-bold" style="color: #005aa9;">
                                Rp {{ number_format($t->hargaPelanggan(), 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('mitra.tarif.edit', $t->id) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('mitra.tarif.toggle', $t->id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-sm rounded-pill w-100
                                {{ $t->is_aktif ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                <i class="bi {{ $t->is_aktif ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                {{ $t->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <form action="{{ route('mitra.tarif.destroy', $t->id) }}" method="POST"
                              onsubmit="return confirm('Hapus tarif &quot;{{ $t->keterangan }}&quot;?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Info komisi --}}
        <div class="alert alert-info rounded-4 mt-3" style="border: none; background: #eff6ff; color: #005aa9; font-size: .8rem;">
            <i class="bi bi-info-circle-fill me-2"></i>
            <strong>Cara kerja komisi:</strong> Pelanggan bayar tarif Anda + {{ number_format($komisiPersen, 1) }}% komisi Zasha.
            Anda menerima 100% tarif yang Anda set.
        </div>
    @endif
</div>
@endsection
