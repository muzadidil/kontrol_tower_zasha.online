# Logika Top-Up Pelanggan - Penjelasan Detail

## 🔍 Ringkasan Singkat

Sistem top-up pelanggan di Zasha Tower menggunakan mekanisme **kode unik 3 digit** untuk verifikasi otomatis transfer bank. Pelanggan diminta mentransfer nominal + kode unik, dan sistem akan otomatis mendeteksi dan mengkonfirmasi transfer berdasarkan kode unik tersebut.

---

## 📊 Alur Lengkap Top-Up Pelanggan

### **STEP 1: Pelanggan Membuka Halaman Dompet**

**Route:** `GET /pelanggan/dompet`

**Controller:** [`PelangganController@dompet()`](app/Http/Controllers/PelangganController.php:22-28)

```php
public function dompet()
{
    $id_p      = auth('pelanggan')->id();
    $pelanggan = DB::table('pelanggans')->where('id_pelanggan', $id_p)->first();
    $riwayat   = DB::table('dompet_pelanggan')->where('id_pelanggan', $id_p)->orderBy('waktu_request', 'desc')->get();
    return view('pelanggan.dompet', compact('pelanggan', 'riwayat'));
}
```

**Yang terjadi:**
1. Ambil ID pelanggan yang login
2. Query data pelanggan dari tabel `pelanggans` (termasuk saldo saat ini)
3. Query riwayat top-up dari tabel `dompet_pelanggan` (diurutkan terbaru)
4. Render view `pelanggan.dompet` dengan data pelanggan dan riwayat

**Data yang ditampilkan di View:**
- Saldo saat ini: `$pelanggan->saldo`
- Riwayat transaksi: `$riwayat` (array of objects)

---

### **STEP 2: Pelanggan Mengisi Form Top-Up**

**View:** [`resources/views/pelanggan/dompet.blade.php`](resources/views/pelanggan/dompet.blade.php:83-128)

**Form Input:**
```html
<form action="{{ route('pelanggan.dompet.topup') }}" method="POST">
    @csrf
    
    <!-- Input 1: Pilih Bank -->
    <select name="bank_tujuan" required>
        <option value="DANA">DANA — Muzadidil Fuad</option>
        <option value="BCA">BCA — Muzadidil Fuad</option>
    </select>
    
    <!-- Input 2: Nominal -->
    <input type="number" name="nominal" placeholder="Minimal Rp 10.000" required>
    
    <!-- Input 3: Nama Pengirim -->
    <input type="text" name="nomor_rekening" placeholder="Sesuai nama rekening" required>
    
    <button type="submit">KONFIRMASI SEKARANG</button>
</form>
```

**Validasi di Frontend:**
- Nominal: required, number
- Bank tujuan: required, select
- Nama pengirim: required, text

---

### **STEP 3: Submit Form ke Backend**

**Route:** `POST /pelanggan/dompet/topup`

**Controller:** [`PelangganController@topup()`](app/Http/Controllers/PelangganController.php:30-67)

```php
public function topup(Request $request)
{
    // STEP 3A: VALIDASI INPUT
    $request->validate([
        'nominal' => 'required|numeric|min:10000|max:10000000',
        'bank_tujuan' => 'required|in:DANA,BCA',
    ]);

    // STEP 3B: AMBIL ID PELANGGAN
    $id_p = auth('pelanggan')->id();
    
    // STEP 3C: GENERATE KODE UNIK (3 DIGIT: 100-999)
    $kode_unik = rand(100, 999);
    
    // STEP 3D: HITUNG TOTAL TRANSFER
    $total_transfer = $request->nominal + $kode_unik;

    // STEP 3E: SIMPAN KE DATABASE
    DB::table('dompet_pelanggan')->insert([
        'id_pelanggan'   => $id_p,
        'nominal'        => $request->nominal,
        'kode_unik'      => $kode_unik,
        'total_transfer' => $total_transfer,
        'bank_tujuan'    => $request->bank_tujuan,
        'status'         => 'pending',
        'waktu_request'  => now(),
    ]);

    // STEP 3F: KIRIM NOTIFIKASI
    NotifHelper::kirim(
        $id_p,
        'Request Top-Up Diterima',
        'Request top-up sebesar Rp ' . number_format($request->nominal, 0, ',', '.') . ' sedang diproses. Silakan transfer sesuai nominal yang ditunjukkan.',
        'topup',
        route('pelanggan.dompet')
    );

    // STEP 3G: RETURN RESPONSE
    return back()
        ->with('notif_topup', 'sukses')
        ->with('data_transfer', $total_transfer)
        ->with('data_bank', $request->bank_tujuan);
}
```

---

## 🔢 Contoh Perhitungan Konkret

### **Skenario:**
Pelanggan ingin top-up Rp 100.000 via DANA

### **Proses:**

| Step | Aksi | Nilai |
|------|------|-------|
| 1 | Pelanggan input nominal | Rp 100.000 |
| 2 | Sistem generate kode unik | 456 (random 100-999) |
| 3 | Hitung total transfer | 100.000 + 456 = **Rp 100.456** |
| 4 | Simpan ke database | `dompet_pelanggan` record baru |
| 5 | Tampilkan ke pelanggan | "Transfer Rp 100.456 ke DANA" |

### **Data yang Tersimpan di Database:**

```sql
INSERT INTO dompet_pelanggan (
    id_pelanggan, 
    nominal, 
    kode_unik, 
    total_transfer, 
    bank_tujuan, 
    status, 
    waktu_request
) VALUES (
    5,                    -- ID pelanggan
    100000,               -- Nominal yang diminta
    456,                  -- Kode unik 3 digit
    100456,               -- Total transfer
    'DANA',               -- Bank tujuan
    'pending',            -- Status awal
    '2026-05-10 14:08:57' -- Waktu request
);
```

---

## 📱 Tampilan di Frontend (Setelah Submit)

**View:** [`resources/views/pelanggan/dompet.blade.php`](resources/views/pelanggan/dompet.blade.php:43-81)

Sistem menampilkan notifikasi sukses dengan detail:

```
┌─────────────────────────────────────────┐
│ Transfer tepat sejumlah:                │
│ Rp 100.456                              │
│                                         │
│ Rincian Transfer:                       │
│ Nominal Topup:    Rp 100.000            │
│ Kode Unik:        + 456                 │
│                                         │
│ Ke Rekening (DANA):                     │
│ 082232458226                            │
│ a/n Muzadidil Fuad                      │
│                                         │
│ ⚠️ Transfer sesuai nominal termasuk     │
│ 3 digit kode unik di akhir agar         │
│ otomatis terdeteksi.                    │
└─────────────────────────────────────────┘
```

---

## 🏦 Nomor Rekening Tujuan

### **DANA:**
- Nomor: `082232458226`
- Atas Nama: Muzadidil Fuad

### **BCA:**
- Nomor: `1470807381`
- Atas Nama: Muzadidil Fuad

---

## ✅ Validasi Input

### **Nominal:**
```php
'nominal' => 'required|numeric|min:10000|max:10000000'
```
- **Required:** Harus diisi
- **Numeric:** Harus angka
- **Min:** Minimal Rp 10.000
- **Max:** Maksimal Rp 10.000.000

### **Bank Tujuan:**
```php
'bank_tujuan' => 'required|in:DANA,BCA'
```
- **Required:** Harus diisi
- **In:** Hanya boleh DANA atau BCA

---

## 📊 Struktur Tabel `dompet_pelanggan`

```sql
CREATE TABLE dompet_pelanggan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_pelanggan INT NOT NULL,
    nominal DECIMAL(15,2),           -- Nominal yang diminta
    kode_unik INT NULLABLE,          -- Kode unik 3 digit (100-999)
    total_transfer DECIMAL(15,2),    -- Total = nominal + kode_unik
    bank_tujuan VARCHAR(255),        -- DANA atau BCA
    status VARCHAR(50),              -- pending, sukses, batal
    waktu_request TIMESTAMP,         -- Waktu request dibuat
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggans(id_pelanggan)
);
```

---

## 🔄 Status Transaksi

| Status | Keterangan | Kapan Terjadi |
|--------|-----------|---------------|
| `pending` | Menunggu transfer dari pelanggan | Saat form disubmit |
| `sukses` | Transfer terverifikasi, saldo sudah ditambah | Saat sistem deteksi kode unik cocok |
| `batal` | Transfer dibatalkan atau gagal | Manual oleh admin atau timeout |

---

## 🔐 Mekanisme Verifikasi (Teori)

### **Bagaimana Sistem Tahu Transfer Sudah Masuk?**

Saat ini, sistem **belum memiliki mekanisme otomatis** untuk:
1. Mendeteksi transfer masuk ke rekening bank
2. Memverifikasi kode unik
3. Mengubah status dari `pending` → `sukses`
4. Menambah saldo pelanggan

### **Kemungkinan Implementasi:**

**Opsi 1: Manual oleh Admin**
- Admin melihat transfer masuk di rekening bank
- Admin cek kode unik di akhir nominal
- Admin update status di database menjadi `sukses`
- Admin trigger increment saldo pelanggan

**Opsi 2: Integrasi Bank API**
- Sistem terhubung dengan API bank (DANA/BCA)
- Setiap transfer masuk, webhook dari bank diterima
- Sistem otomatis verifikasi kode unik
- Sistem otomatis update status dan saldo

**Opsi 3: Cron Job**
- Setiap jam, sistem query rekening bank
- Cek transfer masuk yang belum diverifikasi
- Verifikasi kode unik
- Update status dan saldo

---

## 📝 Notifikasi yang Dikirim

**Saat pelanggan submit form top-up:**

```php
NotifHelper::kirim(
    $id_p,                                    // ID pelanggan
    'Request Top-Up Diterima',                // Judul
    'Request top-up sebesar Rp 100.000 sedang diproses. Silakan transfer sesuai nominal yang ditunjukkan.',  // Pesan
    'topup',                                  // Tipe notifikasi
    route('pelanggan.dompet')                 // Link
);
```

**Notifikasi akan:**
- Disimpan di tabel `notifikasi`
- Dikirim ke FCM token pelanggan (push notification)
- Ditampilkan di halaman notifikasi pelanggan

---

## 🔍 Riwayat Transaksi

**Ditampilkan di halaman dompet:**

```blade
@forelse($riwayat as $r)
    <div class="transaction-card">
        <div class="amount">Rp {{ number_format($r->total_transfer, 0, ',', '.') }}</div>
        <div class="details">
            Nominal: Rp {{ number_format($r->nominal, 0, ',', '.') }} 
            + Kode: {{ $r->kode_unik }} 
            • Via {{ $r->bank_tujuan }} 
            • {{ date('d M, H:i', strtotime($r->waktu_request)) }}
        </div>
        <span class="status-badge">{{ ucfirst($r->status) }}</span>
    </div>
@empty
    <div class="empty-state">Belum ada riwayat transaksi</div>
@endforelse
```

---

## 🛠️ Script Utility

### **1. Check Data Top-Up** (`check_topup_data.php`)

```bash
php check_topup_data.php
```

Menampilkan 5 transaksi top-up terbaru:
```
ID: 15
Pelanggan ID: 5
Nominal: 100000
Kode Unik: 456
Total Transfer: 100456
Bank Tujuan: DANA
Status: pending
Waktu: 2026-05-10 14:08:57
```

### **2. Update Data Lama** (`update_existing_topup_data.php`)

```bash
php update_existing_topup_data.php
```

Mengupdate transaksi lama yang belum punya kode_unik:
```
Ditemukan 3 record yang perlu diupdate.

✓ ID 1: Nominal Rp 50.000 → Total Transfer Rp 50.234 (Kode: 234)
✓ ID 2: Nominal Rp 100.000 → Total Transfer Rp 100.567 (Kode: 567)
✓ ID 3: Nominal Rp 250.000 → Total Transfer Rp 250.789 (Kode: 789)

✅ Selesai! Semua data lama sudah diupdate.
```

---

## 🚀 Alur Lengkap Visualisasi

```
┌─────────────────────────────────────────────────────────────┐
│ PELANGGAN MEMBUKA HALAMAN DOMPET                            │
│ GET /pelanggan/dompet                                       │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ CONTROLLER: PelangganController@dompet()                    │
│ - Query saldo pelanggan dari tabel pelanggans               │
│ - Query riwayat dari tabel dompet_pelanggan                 │
│ - Return view dengan data                                   │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ VIEW: pelanggan/dompet.blade.php                            │
│ - Tampilkan saldo saat ini                                  │
│ - Tampilkan form top-up                                     │
│ - Tampilkan riwayat transaksi                               │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ PELANGGAN MENGISI FORM                                      │
│ - Pilih bank (DANA/BCA)                                     │
│ - Input nominal (Rp 10.000 - Rp 10.000.000)                 │
│ - Input nama pengirim                                       │
│ - Klik "KONFIRMASI SEKARANG"                                │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ FORM SUBMIT: POST /pelanggan/dompet/topup                   │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ CONTROLLER: PelangganController@topup()                     │
│                                                             │
│ 1. VALIDASI INPUT                                           │
│    - nominal: required, numeric, min:10000, max:10000000    │
│    - bank_tujuan: required, in:DANA,BCA                     │
│                                                             │
│ 2. GENERATE KODE UNIK                                       │
│    - $kode_unik = rand(100, 999)                            │
│                                                             │
│ 3. HITUNG TOTAL TRANSFER                                    │
│    - $total_transfer = $nominal + $kode_unik                │
│                                                             │
│ 4. SIMPAN KE DATABASE                                       │
│    - INSERT INTO dompet_pelanggan (...)                     │
│    - Status: 'pending'                                      │
│                                                             │
│ 5. KIRIM NOTIFIKASI                                         │
│    - NotifHelper::kirim(...)                                │
│                                                             │
│ 6. RETURN RESPONSE                                          │
│    - Redirect back dengan session data                      │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ VIEW: pelanggan/dompet.blade.php (dengan session data)      │
│                                                             │
│ Tampilkan notifikasi sukses:                                │
│ - Transfer tepat sejumlah: Rp 100.456                       │
│ - Rincian: Nominal Rp 100.000 + Kode 456                    │
│ - Rekening tujuan: 082232458226 (DANA)                      │
│ - Warning: Transfer sesuai nominal + kode unik              │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ PELANGGAN MELAKUKAN TRANSFER                                │
│ - Buka aplikasi bank/e-wallet                               │
│ - Transfer ke nomor rekening ZASHA                          │
│ - Nominal: Rp 100.456 (sesuai yang ditunjukkan)             │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ SISTEM VERIFIKASI (BELUM OTOMATIS)                          │
│                                                             │
│ Kemungkinan:                                                │
│ 1. Admin manual cek dan update status                       │
│ 2. Bank API webhook (jika terintegrasi)                     │
│ 3. Cron job periodic check                                  │
│                                                             │
│ Saat terverifikasi:                                         │
│ - UPDATE dompet_pelanggan SET status='sukses'               │
│ - UPDATE pelanggans SET saldo = saldo + 100000              │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ PELANGGAN MELIHAT RIWAYAT TRANSAKSI                         │
│ - Status berubah dari 'pending' → 'sukses'                  │
│ - Saldo bertambah Rp 100.000                                │
└─────────────────────────────────────────────────────────────┘
```

---

## 📌 Poin Penting

1. **Kode Unik adalah Kunci Verifikasi**
   - Generate random 100-999
   - Ditambahkan ke nominal untuk total transfer
   - Digunakan untuk verifikasi otomatis

2. **Nominal vs Total Transfer**
   - Nominal: Jumlah yang ingin ditambahkan ke saldo (Rp 100.000)
   - Total Transfer: Nominal + Kode Unik (Rp 100.456)
   - Pelanggan harus transfer sesuai total_transfer

3. **Status Awal Selalu 'pending'**
   - Saat form disubmit, status langsung 'pending'
   - Menunggu verifikasi dari sistem/admin
   - Belum ada penambahan saldo otomatis

4. **Notifikasi Dikirim Saat Submit**
   - Bukan saat transfer masuk
   - Bukan saat verifikasi
   - Langsung setelah form disubmit

5. **Riwayat Transaksi Lengkap**
   - Menampilkan nominal, kode unik, bank, status, waktu
   - Diurutkan dari terbaru ke terlama
   - Bisa dilihat kapan saja di halaman dompet

---

## 🎯 Kesimpulan

Logika top-up pelanggan Zasha Tower adalah:

1. **Pelanggan input nominal** → Sistem generate kode unik → Hitung total transfer
2. **Simpan ke database** dengan status `pending` → Kirim notifikasi
3. **Tampilkan detail transfer** ke pelanggan (nominal + kode unik)
4. **Pelanggan transfer** sesuai total_transfer ke rekening ZASHA
5. **Sistem verifikasi** (manual/otomatis) berdasarkan kode unik
6. **Update status** menjadi `sukses` dan **tambah saldo** pelanggan

Mekanisme kode unik memastikan sistem bisa otomatis mendeteksi transfer mana yang sesuai dengan request top-up mana, tanpa perlu input manual dari pelanggan.
