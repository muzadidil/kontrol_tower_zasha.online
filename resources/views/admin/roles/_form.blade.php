@php
    $isEdit = isset($role);
    $roleName = $isEdit ? old('name', $role->name) : old('name');
    $roleDesc = $isEdit ? old('description', $role->description) : old('description');
    $checked  = $isEdit ? ($activeFeatures ?? []) : (old('features', []));
    $errors   = $errors ?? new \Illuminate\Support\ViewErrorBag();
@endphp

<style>
    .feature-checkbox-card { display: flex; align-items: center; gap: 12px; padding: 14px; border: 1.5px solid #e5e7eb; border-radius: 10px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s; }
    .feature-checkbox-card:hover { border-color: #3b82f6; background: #f0f9ff; }
    .feature-checkbox-card input { width: 18px; height: 18px; cursor: pointer; }
    .feature-checkbox-card input:checked + .feature-info { color: #1e40af; font-weight: 700; }
    .feature-info { flex: 1; }
    .feature-key { font-size: 11px; color: #9ca3af; font-family: monospace; }
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
