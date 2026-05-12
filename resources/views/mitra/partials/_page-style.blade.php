{{-- ════════════════════════════════════════════════════════════════
     MITRA PAGE STYLE — Golden Ratio / Fibonacci Design Tokens
     Sequence spacing: 5, 8, 13, 21, 34, 55, 89, 144 px
     Ratio φ ≈ 1.618 antar elemen utama
     Pakai di view mitra dengan: @include('mitra.partials._page-style')
     ════════════════════════════════════════════════════════════════ --}}
<style>
    /* ── Page Layout ── */
    .m-page {
        padding: var(--fib-4);
        max-width: var(--container-max);
        margin: 0 auto;
    }

    /* ── Hero Section (header biru gradient dengan back button) ── */
    .m-hero {
        background: linear-gradient(135deg, var(--mitra-blue) 0%, var(--mitra-blue-mid) 100%);
        padding: var(--fib-5) var(--fib-4) var(--fib-6);
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .m-hero::before {
        content: '';
        position: absolute;
        top: calc(var(--fib-7) * -1);
        right: calc(var(--fib-7) * -1);
        width: var(--fib-8);
        height: var(--fib-8);
        background: rgba(179, 218, 253, 0.15);
        border-radius: 50%;
        pointer-events: none;
    }
    .m-hero > * { position: relative; z-index: 1; }

    .m-hero-bar {
        display: flex;
        align-items: center;
        gap: var(--fib-3);
        margin-bottom: var(--fib-3);
    }
    .m-hero-back {
        color: #fff;
        font-size: var(--t-lg);
        text-decoration: none;
        line-height: 1;
        padding: var(--fib-1);
        border-radius: var(--r-sm);
        transition: background 0.2s ease;
    }
    .m-hero-back:hover { background: rgba(255,255,255,0.12); color: #fff; }
    .m-hero-eyebrow {
        font-size: var(--t-xxs);
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        opacity: 0.75;
    }
    .m-hero-title {
        font-size: var(--t-lg);
        font-weight: 800;
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.01em;
    }
    .m-hero-meta {
        text-align: center;
        font-size: var(--t-xs);
        opacity: 0.85;
    }

    /* ── Hero Action Pill (tombol kanan di hero) ── */
    .m-hero-action {
        margin-left: auto;
        background: rgba(255,255,255,0.18);
        color: #fff;
        text-decoration: none;
        padding: var(--fib-2) var(--fib-3);
        border-radius: var(--r-pill);
        font-size: var(--t-xs);
        font-weight: 700;
        backdrop-filter: blur(8px);
        transition: background 0.2s ease;
    }
    .m-hero-action:hover { background: rgba(255,255,255,0.28); color: #fff; }

    /* ── Hero Stat Hero (angka besar di tengah) ── */
    .m-hero-stat-label { font-size: var(--t-xxs); opacity: 0.8; }
    .m-hero-stat-value {
        font-size: var(--t-2xl);
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.1;
    }
    .m-hero-stat-sub { font-size: var(--t-xs); opacity: 0.75; margin-top: var(--fib-1); }

    /* ── Card ── */
    .m-card {
        background: var(--surface);
        border: none;
        border-radius: var(--r-lg);
        box-shadow: 0 var(--fib-1) var(--fib-3) rgba(10, 92, 169, 0.04);
        margin-bottom: var(--fib-3);
    }
    .m-card-body { padding: var(--fib-3); }
    .m-card-pad-lg { padding: var(--fib-4); }

    /* ── Section Title ── */
    .m-section-title {
        font-size: var(--t-base);
        font-weight: 700;
        color: var(--ink);
        margin: var(--fib-3) 0 var(--fib-2);
        display: flex;
        align-items: center;
        gap: var(--fib-2);
    }
    .m-section-title-icon { color: var(--mitra-blue); font-size: var(--t-md); }

    /* ── Stat Card Grid (untuk dashboard analytics) ── */
    .m-stat-card {
        background: var(--surface);
        border-radius: var(--r-md);
        padding: var(--fib-3);
        box-shadow: 0 var(--fib-1) var(--fib-2) rgba(10, 92, 169, 0.04);
    }
    .m-stat-card-label {
        font-size: var(--t-xxs);
        font-weight: 700;
        color: var(--ink-soft);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: var(--fib-1);
        margin-bottom: var(--fib-1);
    }
    .m-stat-card-value {
        font-size: var(--t-md);
        font-weight: 800;
        color: var(--ink);
        line-height: 1.2;
    }
    .m-stat-card-hint { font-size: var(--t-xxs); color: var(--ink-soft); margin-top: var(--fib-1); }

    /* ── Filter Chips Bar (scrollable horizontal) ── */
    .m-chip-bar {
        display: flex;
        gap: var(--fib-2);
        overflow-x: auto;
        padding-bottom: var(--fib-2);
        margin-bottom: var(--fib-3);
        scrollbar-width: thin;
    }
    .m-chip-bar::-webkit-scrollbar { height: 3px; }
    .m-chip-bar::-webkit-scrollbar-thumb { background: var(--line); border-radius: var(--r-pill); }
    .m-chip {
        flex-shrink: 0;
        padding: var(--fib-2) var(--fib-3);
        border-radius: var(--r-pill);
        font-size: var(--t-xs);
        font-weight: 600;
        white-space: nowrap;
        text-decoration: none;
        background: var(--surface);
        color: var(--ink-soft);
        border: 1px solid var(--line);
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: var(--fib-1);
    }
    .m-chip:hover { transform: translateY(-1px); }
    .m-chip.active {
        background: var(--mitra-blue);
        color: #fff;
        border-color: var(--mitra-blue);
    }
    .m-chip-count {
        background: var(--mitra-blue-soft);
        color: var(--mitra-blue);
        padding: 1px var(--fib-1);
        border-radius: var(--r-pill);
        font-size: var(--t-xxs);
        font-weight: 700;
    }
    .m-chip.active .m-chip-count { background: rgba(255,255,255,0.25); color: #fff; }

    /* ── Empty State ── */
    .m-empty {
        background: var(--surface);
        border-radius: var(--r-lg);
        padding: var(--fib-5) var(--fib-4);
        text-align: center;
        box-shadow: 0 var(--fib-1) var(--fib-3) rgba(10,92,169,0.04);
    }
    .m-empty-icon { font-size: var(--t-2xl); color: var(--line); }
    .m-empty-title {
        font-size: var(--t-base);
        font-weight: 700;
        color: var(--ink);
        margin: var(--fib-3) 0 var(--fib-1);
    }
    .m-empty-text { font-size: var(--t-xs); color: var(--ink-soft); margin: 0; }

    /* ── Action Buttons ── */
    .m-btn-primary {
        background: linear-gradient(135deg, var(--mitra-blue), var(--mitra-blue-mid));
        color: #fff;
        border: none;
        border-radius: var(--r-pill);
        font-weight: 700;
        font-size: var(--t-sm);
        padding: var(--fib-2) var(--fib-4);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: var(--fib-1);
        text-decoration: none;
    }
    .m-btn-primary:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 var(--fib-2) var(--fib-4) rgba(0, 90, 169, 0.3);
    }
    .m-btn-primary-block { width: 100%; }

    /* ── Form ── */
    .m-form-label {
        font-size: var(--t-xs);
        font-weight: 700;
        color: var(--ink);
        margin-bottom: var(--fib-1);
        display: block;
    }
    .m-form-help { font-size: var(--t-xxs); color: var(--ink-soft); margin-top: var(--fib-1); }
    .m-form-input,
    .m-form-textarea,
    .m-form-select {
        width: 100%;
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        padding: var(--fib-2) var(--fib-3);
        font-size: var(--t-sm);
        font-family: inherit;
        background: var(--surface);
        color: var(--ink);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .m-form-input:focus,
    .m-form-textarea:focus,
    .m-form-select:focus {
        outline: none;
        border-color: var(--mitra-blue);
        box-shadow: 0 0 0 3px rgba(0,90,169,0.12);
    }

    /* ── Alert ── */
    .m-alert {
        border-radius: var(--r-md);
        padding: var(--fib-2) var(--fib-3);
        font-size: var(--t-xs);
        margin-bottom: var(--fib-3);
        display: flex;
        align-items: center;
        gap: var(--fib-2);
    }
    .m-alert-success { background: #d1fae5; color: #065f46; }
    .m-alert-error   { background: #fee2e2; color: #991b1b; }
    .m-alert-info    { background: var(--mitra-blue-soft); color: var(--mitra-blue-dark); }
    .m-alert-warn    { background: #fef3c7; color: #92400e; }

    /* ── List Item Row ── */
    .m-list-row {
        background: var(--surface);
        border-radius: var(--r-md);
        padding: var(--fib-3);
        margin-bottom: var(--fib-2);
        box-shadow: 0 var(--fib-1) var(--fib-2) rgba(10,92,169,0.04);
        display: flex;
        gap: var(--fib-3);
        align-items: flex-start;
    }
    .m-list-icon {
        width: var(--fib-5); height: var(--fib-5);
        border-radius: var(--r-md);
        background: var(--mitra-blue-soft);
        color: var(--mitra-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--t-md);
        flex-shrink: 0;
    }
    .m-list-body { flex-grow: 1; min-width: 0; }
    .m-list-title { font-size: var(--t-sm); font-weight: 700; color: var(--ink); }
    .m-list-meta { font-size: var(--t-xxs); color: var(--ink-soft); margin-top: 2px; }
</style>
