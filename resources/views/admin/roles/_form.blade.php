@php
    $isEdit = isset($role);
    $roleName  = $isEdit ? old('name', $role->name) : old('name');
    $roleDesc  = $isEdit ? old('description', $role->description) : old('description');
    $roleIcon  = $isEdit ? old('icon', $role->icon ?? \App\Models\Role::DEFAULT_ICON) : old('icon', 'bi-shield-fill');
    $roleColor = $isEdit ? old('icon_color', $role->icon_color ?? \App\Models\Role::DEFAULT_ICON_COLOR) : old('icon_color', '#005aa9');
    $checked   = $isEdit ? ($activeFeatures ?? []) : (old('features', []));
    $errors    = $errors ?? new \Illuminate\Support\ViewErrorBag();
@endphp

<style>
    /* COMPACT chip-style untuk fitur picker (bukan card boros) */
    .feature-chip-toggle {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 14px;
        border: 1.5px solid #e5e7eb;
        border-radius: 999px;
        background: white;
        cursor: pointer;
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.15s;
        user-select: none;
        margin: 0;
    }
    .feature-chip-toggle:hover {
        border-color: #3b82f6;
        background: #eff6ff;
        color: #1e40af;
    }
    .feature-chip-toggle input { display: none; }
    .feature-chip-toggle.checked {
        background: #eff6ff;
        border-color: #1e40af;
        color: #1e40af;
        font-weight: 700;
    }
    .feature-chip-toggle.checked::before {
        content: '\F26B'; /* bi-check-lg */
        font-family: 'bootstrap-icons';
        font-size: 0.85rem;
    }
    .feature-group-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        margin-bottom: 8px;
        margin-top: 16px;
    }
    .feature-group-label:first-child { margin-top: 0; }
    .feature-chips-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    /* Icon picker tetap ada tapi lebih kompak */
    .icon-picker {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 6px;
    }
    .icon-option {
        cursor: pointer;
        padding: 10px 4px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        text-align: center;
        transition: all 0.15s;
        background: white;
    }
    .icon-option:hover { border-color: #3b82f6; }
    .icon-option.selected {
        border-color: #1e40af;
        background: #eff6ff;
    }
    .icon-option i { font-size: 1.25rem; display: block; margin-bottom: 2px; }
    .icon-option-label { font-size: 9px; color: #64748b; }

    .icon-preview {
        width: 52px; height: 52px;
        border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
    }
</style>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label fw-bold">Nama Role</label>
        <input type="text" name="name" class="form-control rounded-3" value="{{ $roleName }}" required
               placeholder="Contoh: Mitra TNG, Mitra Premium, dll">
        @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Deskripsi (opsional)</label>
        <input type="text" name="description" class="form-control rounded-3" value="{{ $roleDesc }}"
               placeholder="Penjelasan singkat role ini">
    </div>
</div>

{{-- Icon Picker + Color --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label fw-bold">Preview Icon</label>
        <div class="text-center p-3 bg-light rounded-3">
            <div id="iconPreview" class="icon-preview" style="background:{{ $roleColor }}20; color:{{ $roleColor }};">
                <i class="bi {{ $roleIcon }}"></i>
            </div>
            <div class="mt-2 small text-muted" id="iconLabel">{{ $roleIcon }}</div>
        </div>

        <label class="form-label fw-bold mt-3">Warna Icon</label>
        <input type="color" name="icon_color" id="iconColorInput" class="form-control form-control-color w-100"
               value="{{ $roleColor }}" style="height:42px;">
    </div>
    <div class="col-md-9">
        <label class="form-label fw-bold">Pilih Icon</label>
        <input type="hidden" name="icon" id="iconInput" value="{{ $roleIcon }}">
        <div class="icon-picker">
            @foreach(\App\Models\Role::SUGGESTED_ICONS as $iconKey => $iconLabel)
                <div class="icon-option {{ $roleIcon === $iconKey ? 'selected' : '' }}"
                     data-icon="{{ $iconKey }}"
                     onclick="selectIcon(this)">
                    <i class="bi {{ $iconKey }}"></i>
                    <div class="icon-option-label">{{ $iconLabel }}</div>
                </div>
            @endforeach
        </div>
        <div class="form-text mt-2">Pilih salah satu icon di atas. Warna icon bisa diatur di kiri.</div>
    </div>
</div>

<script>
    function selectIcon(el) {
        document.querySelectorAll('.icon-option').forEach(o => o.classList.remove('selected'));
        el.classList.add('selected');
        const iconKey = el.dataset.icon;
        document.getElementById('iconInput').value = iconKey;
        document.getElementById('iconPreview').innerHTML = '<i class="bi ' + iconKey + '"></i>';
        document.getElementById('iconLabel').textContent = iconKey;
    }
    // Live update preview color
    document.getElementById('iconColorInput')?.addEventListener('input', function(e) {
        const color = e.target.value;
        const preview = document.getElementById('iconPreview');
        preview.style.background = color + '20';
        preview.style.color = color;
    });
</script>

<div class="mb-2">
    <label class="form-label fw-bold mb-1">Pilih Fitur yang Bisa Diakses Role Ini</label>
    <div class="text-muted small">Klik chip untuk toggle. Mitra hanya bisa akses fitur yang dicentang.</div>
</div>

@php
    $grouped = [
        'Order Modules'  => ['order-tenaga', 'order-jastip', 'order-wfh', 'order-service', 'order-inden'],
        'Tools'          => ['maps', 'sparepart', 'portfolio'],
        'Common'         => ['saldo', 'profil', 'pesanan-list'],
    ];
@endphp

@foreach($grouped as $group => $keys)
    <div class="feature-group-label">{{ $group }}</div>
    <div class="feature-chips-wrap">
        @foreach($keys as $key)
            @if(isset($allFeatures[$key]))
                <label class="feature-chip-toggle {{ in_array($key, $checked) ? 'checked' : '' }}"
                       onclick="this.classList.toggle('checked'); event.preventDefault(); document.getElementById('feat-{{ $key }}').checked = !document.getElementById('feat-{{ $key }}').checked;">
                    <input type="checkbox" id="feat-{{ $key }}" name="features[]" value="{{ $key }}"
                           {{ in_array($key, $checked) ? 'checked' : '' }}>
                    {{ $allFeatures[$key] }}
                </label>
            @endif
        @endforeach
    </div>
@endforeach

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background:var(--zasha-blue);border-color:var(--zasha-blue);">
        <i class="bi bi-check-lg me-1"></i> Simpan
    </button>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
</div>
