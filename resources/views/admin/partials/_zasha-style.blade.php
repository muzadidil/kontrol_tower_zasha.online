{{-- Shared Zasha admin design system. Include di view yang butuh row-item minimalis. --}}
<style>
:root {
    --zasha-blue: #005aa9;
    --zasha-blue-dark: #003d75;
    --zasha-blue-light: #e6f0fa;
    --zasha-blue-soft: #eff6ff;
    --zasha-gray-50: #f9fafb;
    --zasha-gray-100: #f3f4f6;
    --zasha-gray-200: #e5e7eb;
    --zasha-gray-400: #94a3b8;
    --zasha-gray-500: #64748b;
    --zasha-gray-700: #334155;
    --zasha-gray-900: #1e293b;
    --zasha-success: #10b981;
    --zasha-warning: #f59e0b;
    --zasha-danger: #ef4444;
    --zasha-info: #0ea5e9;
    --zasha-purple: #7c3aed;
}

/* ─────────────────────────────────────────────────────
   ROW ITEM MINIMALIS (mirip Digiflazz transactions)
   ───────────────────────────────────────────────────── */
.zasha-card {
    background: #fff;
    border: 1px solid var(--zasha-gray-200);
    border-radius: 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    overflow: hidden;
}

.zasha-list-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--zasha-gray-200);
    background: var(--zasha-gray-50);
}
.zasha-list-header h6 {
    margin: 0;
    font-size: 0.85rem; font-weight: 700;
    color: var(--zasha-gray-900);
    display: flex; align-items: center; gap: 8px;
}
.zasha-list-header .badge-count {
    background: var(--zasha-blue);
    color: white;
    font-size: 0.7rem; font-weight: 700;
    padding: 2px 10px; border-radius: 999px;
}

.zasha-row {
    display: flex; align-items: center; gap: 14px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--zasha-gray-100);
    transition: background 0.15s ease;
    text-decoration: none;
    color: inherit;
}
.zasha-row:hover {
    background: var(--zasha-blue-soft);
    color: inherit;
    text-decoration: none;
}
.zasha-row:last-child { border-bottom: none; }

.zasha-row-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: var(--zasha-blue-light);
    color: var(--zasha-blue);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.05rem;
    flex-shrink: 0;
}
.zasha-row-icon.success { background: #d1fae5; color: var(--zasha-success); }
.zasha-row-icon.warning { background: #fef3c7; color: var(--zasha-warning); }
.zasha-row-icon.danger  { background: #fee2e2; color: var(--zasha-danger); }
.zasha-row-icon.purple  { background: #ede9fe; color: var(--zasha-purple); }
.zasha-row-icon.gray    { background: var(--zasha-gray-100); color: var(--zasha-gray-500); }

.zasha-row-body {
    flex: 1;
    min-width: 0; /* allow text truncate */
}
.zasha-row-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--zasha-gray-900);
    line-height: 1.3;
    margin-bottom: 2px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.zasha-row-meta {
    font-size: 0.72rem;
    color: var(--zasha-gray-500);
    display: flex; align-items: center; gap: 10px;
    flex-wrap: wrap;
}
.zasha-row-meta i { font-size: 0.7rem; opacity: 0.8; }
.zasha-row-meta .sep { color: var(--zasha-gray-200); }

.zasha-row-amount {
    text-align: right;
    flex-shrink: 0;
}
.zasha-row-amount-main {
    font-size: 0.9rem;
    font-weight: 800;
    color: var(--zasha-blue);
    white-space: nowrap;
}
.zasha-row-amount-main.success { color: var(--zasha-success); }
.zasha-row-amount-main.danger  { color: var(--zasha-danger); }
.zasha-row-amount-sub {
    font-size: 0.7rem;
    color: var(--zasha-gray-400);
    margin-top: 2px;
}

.zasha-status-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.4px;
    white-space: nowrap;
}
.zasha-status-badge.pending  { background: #fef3c7; color: #92400e; }
.zasha-status-badge.process  { background: #dbeafe; color: #1e40af; }
.zasha-status-badge.success  { background: #d1fae5; color: #065f46; }
.zasha-status-badge.danger   { background: #fee2e2; color: #991b1b; }
.zasha-status-badge.gray     { background: var(--zasha-gray-100); color: var(--zasha-gray-700); }
.zasha-status-badge.purple   { background: #ede9fe; color: #5b21b6; }

/* Empty state */
.zasha-empty {
    text-align: center;
    padding: 60px 20px;
    color: var(--zasha-gray-400);
}
.zasha-empty i { font-size: 3rem; opacity: 0.5; display: block; margin-bottom: 12px; }
.zasha-empty-title { font-size: 0.9rem; font-weight: 700; color: var(--zasha-gray-500); margin-bottom: 4px; }
.zasha-empty-sub   { font-size: 0.78rem; color: var(--zasha-gray-400); }

/* Filter / search bar minimal */
.zasha-filter-bar {
    display: flex; gap: 10px; align-items: center; flex-wrap: wrap;
    padding: 14px 20px;
    border-bottom: 1px solid var(--zasha-gray-200);
    background: white;
}
.zasha-filter-bar .form-control, .zasha-filter-bar .form-select {
    border-radius: 999px;
    border: 1.5px solid var(--zasha-gray-200);
    font-size: 0.8rem; padding: 7px 14px;
    height: auto;
}
.zasha-filter-bar .form-control:focus, .zasha-filter-bar .form-select:focus {
    border-color: var(--zasha-blue);
    box-shadow: 0 0 0 3px rgba(0,90,169,0.1);
}
.zasha-filter-bar .btn {
    border-radius: 999px;
    padding: 7px 18px;
    font-size: 0.78rem; font-weight: 700;
}

/* Page header (judul + tombol aksi) */
.zasha-page-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    margin-bottom: 20px; gap: 12px; flex-wrap: wrap;
}
.zasha-page-title h4 {
    margin: 0;
    font-size: 1.15rem; font-weight: 800;
    color: var(--zasha-gray-900);
    display: flex; align-items: center; gap: 10px;
}
.zasha-page-title h4 i { color: var(--zasha-blue); }
.zasha-page-subtitle {
    font-size: 0.78rem; color: var(--zasha-gray-500);
    margin-top: 4px;
}
</style>
