@extends('layouts.mitra')

@section('content')
@php $totalFeatured = $portfolios->where('is_featured', true)->count(); @endphp

{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('mitra.dashboard') }}" class="text-white me-3" style="font-size:1.4rem;text-decoration:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div style="font-size:.75rem;opacity:.8;">PORTFOLIO</div>
            <h5 class="fw-bold m-0">Karya Saya</h5>
        </div>
        <a href="{{ route('mitra.portfolio.create') }}"
           class="ms-auto text-white text-decoration-none small fw-bold"
           style="background:rgba(255,255,255,.18);padding:6px 14px;border-radius:999px;">
            <i class="bi bi-plus-lg me-1"></i> Tambah
        </a>
    </div>
    <div class="text-center" style="font-size:.75rem;opacity:.85;">
        {{ $portfolios->count() }} karya · {{ $totalFeatured }} featured
    </div>
</div>

<div style="padding: var(--fib-4, 16px); max-width: 720px; margin: 0 auto;">

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm small">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm small">
            <i class="bi bi-x-circle-fill me-1"></i>{{ session('error') }}
        </div>
    @endif

    @if($portfolios->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <i class="bi bi-images" style="font-size:3rem;color:#cbd5e1;"></i>
            <h6 class="mt-3 fw-bold">Belum Ada Portfolio</h6>
            <p class="text-muted small">Tambahkan contoh karya agar pelanggan yakin sebelum order.</p>
            <a href="{{ route('mitra.portfolio.create') }}"
               class="btn btn-primary rounded-pill px-4 mt-2">
                <i class="bi bi-plus-circle me-1"></i>Tambah Karya Pertama
            </a>
        </div>
    @else
        <div class="row g-2">
            @foreach($portfolios as $p)
                <div class="col-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden">
                        {{-- Featured badge --}}
                        @if($p->is_featured)
                            <div style="position:absolute;top:8px;right:8px;z-index:2;
                                        background:#fbbf24;color:#fff;padding:3px 10px;
                                        border-radius:999px;font-size:.6rem;font-weight:700;">
                                <i class="bi bi-star-fill"></i> Featured
                            </div>
                        @endif

                        {{-- Preview --}}
                        @if($p->isImage())
                            <div style="height:120px;background:url('{{ asset('storage/' . $p->file_path) }}') center/cover;"></div>
                        @elseif($p->file_path)
                            <div style="height:120px;background:linear-gradient(135deg,#eff6ff,#dbeafe);
                                        display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-file-earmark-pdf" style="font-size:2.5rem;color:#005aa9;"></i>
                            </div>
                        @elseif($p->link_url)
                            <div style="height:120px;background:linear-gradient(135deg,#f5f3ff,#ede9fe);
                                        display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-link-45deg" style="font-size:2.5rem;color:#7c3aed;"></i>
                            </div>
                        @endif

                        <div class="card-body p-2">
                            <div class="fw-bold small text-truncate" style="color:#1e293b;">
                                {{ $p->judul }}
                            </div>
                            @if($p->kategori)
                                <span class="badge bg-light text-dark" style="font-size:.6rem;">
                                    {{ $p->kategori }}
                                </span>
                            @endif

                            <div class="d-flex gap-1 mt-2">
                                <a href="{{ route('mitra.portfolio.edit', $p->id) }}"
                                   class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1 py-1"
                                   style="font-size:.65rem;">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('mitra.portfolio.toggleFeatured', $p->id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm rounded-pill w-100 py-1
                                                {{ $p->is_featured ? 'btn-warning' : 'btn-outline-warning' }}"
                                            style="font-size:.65rem;">
                                        <i class="bi {{ $p->is_featured ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('mitra.portfolio.destroy', $p->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus &quot;{{ $p->judul }}&quot;?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger rounded-pill py-1"
                                            style="font-size:.65rem;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="alert alert-info rounded-3 small mt-3 mb-0"
             style="background:#eff6ff;border-color:#bfdbfe;color:#1e40af;">
            <i class="bi bi-info-circle-fill me-1"></i>
            Karya bertanda <i class="bi bi-star-fill text-warning"></i> <strong>Featured</strong>
            akan ditampilkan terlebih dulu di halaman detail mitra untuk pelanggan.
        </div>
    @endif
</div>
@endsection
