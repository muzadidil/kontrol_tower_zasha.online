@extends('layouts.pelanggan')

@section('content')
<style>
    .alamat-hero {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
        padding: 21px 21px 21px;
        position: relative;
        overflow: hidden;
    }
    .alamat-hero::before {
        content: '';
        position: absolute;
        top: -34px; right: -34px;
        width: 89px; height: 89px;
        border-radius: 50%;
        background: rgba(240,165,0,0.10);
    }
    .alamat-hero::after {
        content: '';
        position: absolute;
        bottom: -21px; left: 34px;
        width: 55px; height: 55px;
        border-radius: 50%;
        background: rgba(240,165,0,0.07);
    }

    .alamat-card {
        background: #fff;
        border-radius: 13px;
        padding: 16px;
        margin-bottom: 13px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border-left: 4px solid #e2e8f0;
        transition: box-shadow 0.2s;
    }
    .alamat-card.utama { border-left-color: var(--gold, #f0a500); }
    .alamat-card:last-child { margin-bottom: 0; }

    .alamat-badge-utama {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: linear-gradient(135deg, #f0a500, #e09000);
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 99px;
        margin-bottom: 6px;
    }
    .alamat-label  { font-size: 0.82rem; font-weight: 700; color: #1a1a2e; margin-bottom: 2px; }
    .alamat-penerima { font-size: 0.78rem; color: #64748b; }
    .alamat-text   { font-size: 0.8rem; color: #475569; margin-top: 6px; line-height: 1.5; }

    .alamat-actions { display: flex; gap: 6px; margin-top: 10px; flex-wrap: wrap; }
    .btn-alamat-action {
        font-size: 0.72rem; font-weight: 600;
        padding: 5px 12px; border-radius: 8px;
        border: 1.5px solid; cursor: pointer;
        transition: all 0.2s; background: transparent;
    }
    .btn-edit-a  { color: #005aa9; border-color: #005aa9; }
    .btn-edit-a:hover { background: #005aa9; color: #fff; }
    .btn-utama-a { color: #16a34a; border-color: #16a34a; }
    .btn-utama-a:hover { background: #16a34a; color: #fff; }
    .btn-hapus-a { color: #dc2626; border-color: #dc2626; }
    .btn-hapus-a:hover { background: #dc2626; color: #fff; }

    .btn-tambah-alamat {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 14px;
        background: linear-gradient(135deg, #f0a500, #e09000);
        color: #fff; font-weight: 700; font-size: 0.9rem;
        border: none; border-radius: 13px; cursor: pointer;
        box-shadow: 0 4px 13px rgba(240,165,0,0.3);
        transition: all 0.2s;
    }
    .btn-tambah-alamat:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(240,165,0,0.4); }

    /* Bottom Sheet */
    .bs-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.55);
        z-index: 1050;
        opacity: 0; pointer-events: none;
        transition: opacity 0.25s;
    }
    .bs-overlay.open { opacity: 1; pointer-events: all; }
    .bs-sheet {
        position: fixed; left: 50%; bottom: 0;
        width: 100%; max-width: 480px;
        transform: translate(-50%, 100%);
        z-index: 1051;
        background: #fff;
        border-radius: 21px 21px 0 0;
        max-height: 90vh; overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
        transition: transform 0.3s cubic-bezier(0.32,0.72,0,1);
        padding-bottom: 34px;
    }
    .bs-sheet.open { transform: translate(-50%, 0); }
    @media (max-width: 480px) {
        .bs-sheet { max-width: 100%; }
    }
    .bs-handle {
        width: 40px; height: 4px;
        background: #e2e8f0; border-radius: 2px;
        margin: 12px auto 0;
    }
    .bs-title { font-size: 1rem; font-weight: 700; color: #1a1a2e; padding: 16px 21px 8px; }

    /* Map */
    #map-container { display: none; }
    #map-container.show { display: block; }
    #map { width: 100%; height: 200px; border-radius: 13px; background: #f1f5f9; }
    @media (max-height: 700px) {
        #map { height: 160px; }
    }
    .map-hint { font-size: 0.72rem; color: #94a3b8; margin-top: 6px; text-align: center; }
    #map-selected-address {
        background: #f0fdf4; border: 1.5px solid #bbf7d0;
        border-radius: 10px; padding: 10px 12px;
        font-size: 0.8rem; color: #15803d;
        margin-top: 8px; display: none;
    }
    #map-selected-address.show { display: block; }

    /* Label chips */
    .label-chips { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 4px; }
    .label-chip {
        font-size: 0.72rem; font-weight: 600;
        padding: 4px 12px; border-radius: 99px;
        border: 1.5px solid #e2e8f0; color: #64748b;
        cursor: pointer; background: #fff; transition: all 0.15s;
        user-select: none;
    }
    .label-chip.active { background: #1a1a2e; color: #f0a500; border-color: #1a1a2e; }

    .form-z label { font-size: 0.78rem; font-weight: 600; color: #374151; margin-bottom: 4px; display: block; }
    .form-z .form-control {
        border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-size: 0.85rem; padding: 10px 14px;
        transition: border-color 0.2s; width: 100%;
    }
    .form-z .form-control:focus { border-color: #f0a500; box-shadow: none; outline: none; }
    .form-z textarea { resize: none; }

    .empty-state { text-align: center; padding: 55px 21px; color: #94a3b8; }
    .empty-state i { font-size: 2.5rem; margin-bottom: 13px; display: block; opacity: 0.4; }
    .empty-state p { font-size: 0.85rem; margin: 0; }
</style>

{{-- HERO --}}
<div class="alamat-hero">
    <div class="d-flex align-items-center gap-3" style="position:relative; z-index:1;">
        <a href="{{ route('pelanggan.profil') }}"
           style="width:36px; height:36px; border-radius:10px; color:#fff; text-decoration:none;
                  background:rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-arrow-left" style="font-size:0.85rem;"></i>
        </a>
        <div>
            <div style="font-size:1.05rem; font-weight:700; color:#fff;">Buku Alamat</div>
            <div style="font-size:0.75rem; color:rgba(255,255,255,0.6);">Kelola alamat pengiriman kamu</div>
        </div>
    </div>
</div>

{{-- CONTENT --}}
<div style="padding: 21px 13px 90px; max-width: 480px; margin: 0 auto;">

    @if(session('success'))
        <div style="background:#f0fdf4; color:#15803d; font-size:0.82rem; padding:12px 14px;
                    border-radius:10px; margin-bottom:13px; display:flex; align-items:center; gap:8px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Address List --}}
    @if($alamats->isEmpty())
        <div class="empty-state">
            <i class="fas fa-map-marker-alt"></i>
            <p>Belum ada alamat tersimpan.<br>Tambah alamat pertamamu sekarang.</p>
        </div>
    @else
        @foreach($alamats as $alamat)
        <div class="alamat-card {{ $alamat->is_utama ? 'utama' : '' }}">
            @if($alamat->is_utama)
            <div class="alamat-badge-utama">
                <i class="fas fa-star" style="font-size:0.6rem;"></i> Utama
            </div>
            @endif
            <div class="alamat-label">{{ $alamat->label_alamat }}</div>
            <div class="alamat-penerima">
                <i class="fas fa-user me-1" style="font-size:0.65rem; color:#94a3b8;"></i>{{ $alamat->nama_penerima }}
                &nbsp;&bull;&nbsp;
                <i class="fab fa-whatsapp me-1" style="font-size:0.65rem; color:#25d366;"></i>{{ $alamat->no_wa_penerima }}
            </div>
            <div class="alamat-text">
                <i class="fas fa-map-marker-alt me-1" style="font-size:0.65rem; color:#94a3b8;"></i>{{ $alamat->alamat_lengkap }}
            </div>
            <div class="alamat-actions">
                <button type="button" class="btn-alamat-action btn-edit-a"
                    onclick="bukaModalEdit(
                        {{ $alamat->id }},
                        '{{ addslashes($alamat->label_alamat) }}',
                        '{{ addslashes($alamat->nama_penerima) }}',
                        '{{ addslashes($alamat->no_wa_penerima) }}',
                        '{{ addslashes($alamat->alamat_lengkap) }}',
                        {{ $alamat->is_utama ? 'true' : 'false' }},
                        {{ $alamat->lat !== null ? $alamat->lat : 'null' }},
                        {{ $alamat->lng !== null ? $alamat->lng : 'null' }}
                    )">
                    <i class="fas fa-pencil-alt me-1"></i>Edit
                </button>
                @if(!$alamat->is_utama)
                <form method="POST" action="{{ route('pelanggan.alamat.utama', $alamat->id) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-alamat-action btn-utama-a">
                        <i class="fas fa-star me-1"></i>Jadikan Utama
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('pelanggan.alamat.destroy', $alamat->id) }}" style="display:inline;"
                      onsubmit="return confirm('Hapus alamat ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-alamat-action btn-hapus-a">
                        <i class="fas fa-trash me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    @endif

    <div style="margin-top: 21px;">
        <button type="button" class="btn-tambah-alamat" onclick="bukaModal()">
            <i class="fas fa-plus"></i> Tambah Alamat Baru
        </button>
    </div>
</div>

{{-- BOTTOM SHEET --}}
<div class="bs-overlay" id="bsOverlay" onclick="tutupModal()"></div>
<div class="bs-sheet" id="bsSheet">
    <div class="bs-handle"></div>
    <div class="bs-title" id="bsTitle">Tambah Alamat</div>

    <div style="padding: 0 21px;">
        <form id="formAlamat" method="POST" action="{{ route('pelanggan.alamat.store') }}" class="form-z">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="lat" id="inputLat" value="">
            <input type="hidden" name="lng" id="inputLng" value="">

            {{-- Label --}}
            <div class="mb-3">
                <label>Label Alamat</label>
                <div class="label-chips">
                    <span class="label-chip" onclick="setLabel('Rumah')"><i class="fas fa-home me-1"></i>Rumah</span>
                    <span class="label-chip" onclick="setLabel('Kantor')"><i class="fas fa-building me-1"></i>Kantor</span>
                    <span class="label-chip" onclick="setLabel('Kos')"><i class="fas fa-door-open me-1"></i>Kos</span>
                    <span class="label-chip" onclick="setLabel('Lainnya')"><i class="fas fa-map-pin me-1"></i>Lainnya</span>
                </div>
                <input type="text" name="label_alamat" id="inputLabel" class="form-control mt-2"
                       placeholder="atau ketik sendiri..." required maxlength="100">
            </div>

            {{-- Google Maps (only if key is set) --}}
            @if($gmaps_api_key)
            <div id="map-container" class="mb-3">
                <label>Pilih Lokasi di Peta</label>
                <div id="autocomplete-container" style="width:100%; margin-bottom:8px;"></div>
                <div id="map"></div>
                <div class="map-hint"><i class="fas fa-hand-pointer me-1"></i>Seret pin untuk menyesuaikan lokasi</div>
                <div id="map-selected-address"></div>
            </div>
            @endif

            {{-- Alamat Lengkap --}}
            <div class="mb-3">
                <label>Alamat Lengkap <span style="color:#dc2626;">*</span></label>
                <textarea name="alamat_lengkap" id="inputAlamat" class="form-control" rows="3"
                          placeholder="Jl. Contoh No. 123, RT/RW, Kelurahan, Kecamatan..." required></textarea>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label>Nama Penerima</label>
                    <input type="text" name="nama_penerima" id="inputNama" class="form-control"
                           placeholder="Nama lengkap" required maxlength="100">
                </div>
                <div class="col-6">
                    <label>No. WhatsApp</label>
                    <input type="text" name="no_wa_penerima" id="inputWa" class="form-control"
                           placeholder="08xxxxxxxxxx" required maxlength="20">
                </div>
            </div>

            <div class="mb-3 d-flex align-items-center gap-2">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="is_utama" id="inputUtama" value="1">
                    <label class="form-check-label" for="inputUtama" style="font-size:0.82rem; color:#374151;">
                        Jadikan alamat utama
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-tambah-alamat" style="border-radius:10px; padding:12px;">
                <i class="fas fa-save me-2"></i><span id="btnSubmitText">Simpan Alamat</span>
            </button>
        </form>
    </div>
</div>

@push('scripts')
@if($gmaps_api_key)
<script>
// Google Maps dynamic loader (loading=async, required for new Places API).
(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[t.toLowerCase()]),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.googleapis.com/maps/api/js?`+e;a.onerror=()=>n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a);d[q]=f}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})
({key: "{{ $gmaps_api_key }}", v: "weekly"});
</script>

<script>
let map, marker, placeAutocomplete, geocoder;
let AdvancedMarkerEl;
let mapsReady = false;

async function initMap() {
    const { Map }                      = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement }    = await google.maps.importLibrary("marker");
    const { PlaceAutocompleteElement } = await google.maps.importLibrary("places");
    const { Geocoder }                 = await google.maps.importLibrary("geocoding");

    AdvancedMarkerEl = AdvancedMarkerElement;
    geocoder = new Geocoder();

    const defaultPos = { lat: -2.5489, lng: 118.0149 };

    map = new Map(document.getElementById('map'), {
        center: defaultPos,
        zoom: 5,
        mapTypeControl: false,
        mapId: 'zasha_map',
    });

    marker = new AdvancedMarkerEl({
        map: null,
        position: defaultPos,
        gmpDraggable: true,
    });

    marker.addListener('dragend', function() {
        const pos = marker.position;
        const lat = (typeof pos.lat === 'function') ? pos.lat() : pos.lat;
        const lng = (typeof pos.lng === 'function') ? pos.lng() : pos.lng;
        setLatLng(lat, lng);
        reverseGeocode(lat, lng);
    });

    map.addListener('click', function(e) {
        marker.position = e.latLng;
        marker.map = map;
        const lat = e.latLng.lat();
        const lng = e.latLng.lng();
        setLatLng(lat, lng);
        reverseGeocode(lat, lng);
    });

    placeAutocomplete = new PlaceAutocompleteElement({
        componentRestrictions: { country: 'id' },
    });
    placeAutocomplete.style.width = '100%';
    document.getElementById('autocomplete-container').appendChild(placeAutocomplete);

    placeAutocomplete.addEventListener('gmp-select', async ({ placePrediction }) => {
        const place = placePrediction.toPlace();
        await place.fetchFields({
            fields: ['location', 'formattedAddress', 'addressComponents', 'displayName'],
        });

        const lat = place.location.lat();
        const lng = place.location.lng();

        map.setCenter({ lat, lng });
        map.setZoom(17);
        marker.position = { lat, lng };
        marker.map = map;

        fillForm(lat, lng, place.formattedAddress);
    });

    mapsReady = true;
}

function reverseGeocode(lat, lng) {
    if (!geocoder) return;
    geocoder.geocode({ location: { lat, lng } }, function(results, status) {
        if (status === 'OK' && results[0]) {
            fillForm(lat, lng, results[0].formatted_address);
        } else {
            setLatLng(lat, lng);
        }
    });
}

function fillForm(lat, lng, formattedAddress) {
    setLatLng(lat, lng);

    if (formattedAddress) {
        document.getElementById('inputAlamat').value = formattedAddress;
        const box = document.getElementById('map-selected-address');
        box.textContent = formattedAddress;
        box.classList.add('show');
    }
}

function setLatLng(lat, lng) {
    document.getElementById('inputLat').value = (lat !== null && lat !== undefined && lat !== '') ? Number(lat).toFixed(7) : '';
    document.getElementById('inputLng').value = (lng !== null && lng !== undefined && lng !== '') ? Number(lng).toFixed(7) : '';
}

function refreshMap(existingAddress, existingLat, existingLng) {
    const mc = document.getElementById('map-container');
    mc.classList.add('show');
    setTimeout(function() {
        if (!mapsReady || !map) {
            // Map belum siap, initMap akan jalan sendiri saat SDK ready.
            return;
        }
        google.maps.event.trigger(map, 'resize');

        const hasCoords = (existingLat !== null && existingLat !== undefined && existingLat !== '' &&
                           existingLng !== null && existingLng !== undefined && existingLng !== '');
        if (hasCoords) {
            const pos = { lat: Number(existingLat), lng: Number(existingLng) };
            map.setCenter(pos);
            map.setZoom(17);
            marker.position = pos;
            marker.map = map;
            setLatLng(pos.lat, pos.lng);
        } else if (existingAddress && geocoder) {
            geocoder.geocode({ address: existingAddress }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const loc = results[0].geometry.location;
                    map.setCenter(loc);
                    map.setZoom(16);
                    marker.position = loc;
                    marker.map = map;
                    setLatLng(loc.lat(), loc.lng());
                }
            });
        }
    }, 350);
}

// Auto-init on load (loader bertanggung jawab fetch SDK).
initMap();
</script>
@endif

<script>
function bukaModal() {
    document.getElementById('bsTitle').textContent = 'Tambah Alamat';
    document.getElementById('btnSubmitText').textContent = 'Simpan Alamat';
    document.getElementById('formAlamat').action = '{{ route("pelanggan.alamat.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('inputLabel').value = '';
    document.getElementById('inputAlamat').value = '';
    document.getElementById('inputNama').value = '';
    document.getElementById('inputWa').value = '';
    document.getElementById('inputUtama').checked = false;
    document.getElementById('inputLat').value = '';
    document.getElementById('inputLng').value = '';
    document.querySelectorAll('.label-chip').forEach(c => c.classList.remove('active'));

    @if($gmaps_api_key)
    const box = document.getElementById('map-selected-address');
    box.textContent = '';
    box.classList.remove('show');
    if (typeof placeAutocomplete !== 'undefined' && placeAutocomplete) {
        try { placeAutocomplete.value = ''; } catch (e) { /* property may be read-only on some builds */ }
    }
    refreshMap(null);
    @endif

    openSheet();
}

function bukaModalEdit(id, label, nama, wa, alamat, isUtama, lat, lng) {
    document.getElementById('bsTitle').textContent = 'Edit Alamat';
    document.getElementById('btnSubmitText').textContent = 'Perbarui Alamat';
    document.getElementById('formAlamat').action = '/alamat/' + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('inputLabel').value = label;
    document.getElementById('inputAlamat').value = alamat;
    document.getElementById('inputNama').value = nama;
    document.getElementById('inputWa').value = wa;
    document.getElementById('inputUtama').checked = isUtama;
    document.getElementById('inputLat').value = (lat !== null && lat !== undefined) ? lat : '';
    document.getElementById('inputLng').value = (lng !== null && lng !== undefined) ? lng : '';
    document.querySelectorAll('.label-chip').forEach(c => {
        c.classList.toggle('active', c.textContent.trim().replace(/^\S+\s*/, '').includes(label));
    });

    @if($gmaps_api_key)
    if (alamat) {
        const box = document.getElementById('map-selected-address');
        box.textContent = alamat;
        box.classList.add('show');
    }
    refreshMap(alamat, lat, lng);
    @endif

    openSheet();
}

function openSheet() {
    document.getElementById('bsOverlay').classList.add('open');
    document.getElementById('bsSheet').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function tutupModal() {
    document.getElementById('bsOverlay').classList.remove('open');
    document.getElementById('bsSheet').classList.remove('open');
    document.body.style.overflow = '';
}

function setLabel(val) {
    document.getElementById('inputLabel').value = val;
    document.querySelectorAll('.label-chip').forEach(c => {
        const text = c.textContent.trim().replace(/^\S+\s*/, '');
        c.classList.toggle('active', text === val || c.textContent.trim() === val);
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') tutupModal();
});
</script>
@endpush
@endsection
