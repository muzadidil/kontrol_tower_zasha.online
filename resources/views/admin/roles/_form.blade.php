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
    .feature-checkbox-card { display: flex; align-items: center; gap: 12px; padding: 14px; border: 1.5px solid #e5e7eb; border-radius: 10px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s; }
    .feature-checkbox-card:hover { border-color: #3b82f6; background: #f0f9ff; }
    .feature-checkbox-card input { width: 18px; height: 18px; cursor: pointer; }
    .feature-checkbox-card input:checked + .feature-info { color: #1e40af; font-weight: 700; }
    .feature-info { flex: 1; }
    .feature-key { font-size: 11px; color: #9ca3af; font-family: monospace; }

    .icon-picker { display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; }
    .icon-option {
        cursor: pointer; padding: 14px 8px; border: 1.5px solid #e5e7eb;
        border-radius: 10px; text-align: center; transition: all 0.15s;
        background: white;
    }
    .icon-option:hover { border-color: #3b82f6; }
    .icon-option.selected { border-color: #1e40af; background: #eff6ff; box-shadow: 0 2px 6px rgba(30,64,175,0.15); }
    .icon-option i { font-size: 1.6rem; display: block; margin-bottom: 4px; }
    .icon-option-label { font-size: 10px; color: #64748b; }

    .icon-preview {
        width: 64px; height: 64px; border-radius: 16px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.8rem;
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

<div class="mb-3">
    <label class="form-label fw-bold">Pilih Fitur yang Bisa Diakses Role Ini</label>
    <div class="text-muted small mb-3">Centang fitur yang diizinkan. Mitra dengan role ini hanya akan melihat menu sesuai centang.</div>
</div>

@php
    $grouped = [
        'Order Modules'  => ['order-tenaga', 'order-jastip', 'order-wfh', 'order-service', 'order-inden'],
        'Tools'          => ['maps', 'sparepart', 'portfolio'],
        'Common'         => ['saldo', 'profil', 'pesanan-list'],
    ];
@endphp

@foreach($grouped as $group => $keys)
    <div class="mb-3">
        <div class="text-muted small fw-bold mb-2">{{ strtoupper($group) }}</div>
        @foreach($keys as $key)
            @if(isset($allFeatures[$key]))
                <label class="feature-checkbox-card">
                    <input type="checkbox" name="features[]" value="{{ $key }}"
                           {{ in_array($key, $checked) ? 'checked' : '' }}>
                    <div class="feature-info">
                        <div>{{ $allFeatures[$key] }}</div>
                        <div class="feature-key">{{ $key }}</div>
                    </div>
                </label>
            @endif
        @endforeach
    </div>
@endforeach

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
        <i class="bi bi-check-lg me-1"></i> Simpan
    </button>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
</div>
