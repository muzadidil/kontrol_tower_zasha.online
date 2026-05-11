# Rangkuman Sistem Dompet Pelanggan & Dompet Mitra - Zasha Tower

## 📋 Ringkasan Umum

Zasha Tower memiliki dua sistem dompet digital yang terpisah untuk **Pelanggan** dan **Mitra**. Kedua sistem ini mengelola saldo, transaksi top-up, dan penarikan dana dengan mekanisme yang berbeda sesuai kebutuhan masing-masing pengguna.

---

## 1️⃣ DOMPET PELANGGAN

### 1.1 Struktur Data

**Tabel Utama:** `pelanggans`
- `id_pelanggan` (Primary Key)
- `saldo` (decimal:2) - Saldo dompet pelanggan
- `nama_pelanggan`
- `email`
- `no_wa`
- `tgl_lahir` / `birth_date`
- `status_verifikasi`
- `kode_zasha` (unique) - Identitas unik pelanggan

**Tabel Transaksi:** `dompet_pelanggan`
- `id` (Primary Key)
- `id_pelanggan` (Foreign Key)
- `nominal` - Jumlah nominal top-up yang diminta
- `kode_unik` (integer, 3 digit: 100-999) - Kode verifikasi transfer
- `total_transfer` (decimal:2) - Total yang harus ditransfer = nominal + kode_unik
- `bank_tujuan` (string) - Bank tujuan transfer (DANA atau BCA)
- `status` (enum) - Status transaksi (pending, sukses, batal)
- `waktu_request` (timestamp) - Waktu request dibuat

### 1.2 Fitur Top-Up Pelanggan

#### Alur Top-Up:
1. **Pelanggan membuka halaman Dompet** (`/pelanggan/dompet`)
2. **Mengisi form top-up:**
   - Memilih media transfer (DANA atau BCA)
   - Memasukkan nominal (minimal Rp 10.000, maksimal Rp 10.000.000)
   - Memasukkan nama pengirim (sesuai nama rekening)
3. **Sistem generate kode unik** (3 digit random: 100-999)
4. **Menampilkan detail transfer:**
   - Nominal top-up
   - Kode unik (3 digit terakhir)
   - Total transfer = nominal + kode_unik
   - Nomor rekening tujuan
5. **Pelanggan melakukan transfer** sesuai total_transfer
6. **Sistem verifikasi otomatis** berdasarkan kode unik di akhir nominal
7. **Saldo otomatis bertambah** setelah verifikasi

#### Contoh Transaksi:
```
Nominal yang diminta: Rp 100.000
Kode unik yang di-generate: 456
Total transfer: Rp 100.456
Pelanggan transfer: Rp 100.456 ke rekening ZASHA
Sistem deteksi: 456 = kode unik valid
Saldo pelanggan: +Rp 100.000
```

#### Bank Tujuan:
- **DANA:** 082232458226 (a/n Muzadidil Fuad)
- **BCA:** 1470807381 (a/n Muzadidil Fuad)

### 1.3 Status Transaksi Dompet Pelanggan

| Status | Keterangan |
|--------|-----------|
| `pending` | Menunggu transfer dari pelanggan |
| `sukses` | Transfer terverifikasi, saldo sudah ditambahkan |
| `batal` | Transfer dibatalkan atau gagal |

### 1.4 Validasi Top-Up

```php
// Dari PelangganController@topup()
- nominal: required, numeric, min:10000, max:10000000
- bank_tujuan: required, in:DANA,BCA
```

### 1.5 Notifikasi Pelanggan

Setiap kali pelanggan melakukan request top-up, sistem mengirim notifikasi:
- **Judul:** "Request Top-Up Diterima"
- **Pesan:** "Request top-up sebesar Rp [nominal] sedang diproses. Silakan transfer sesuai nominal yang ditunjukkan."
- **Tipe:** topup
- **Link:** `/pelanggan/dompet`

### 1.6 UI/UX Dompet Pelanggan

**Halaman:** `resources/views/pelanggan/dompet.blade.php`

**Komponen:**
1. **Hero Section** - Menampilkan saldo saat ini
   - Gradient biru (001d4d → 0047b3)
   - Saldo dalam format Rp dengan pemisah ribuan
   - Label "Saldo Saya" dan "Tersedia untuk digunakan"

2. **Card Top-Up**
   - Tombol "ISI SALDO (TOP UP)" dengan ikon plus
   - Form tersembunyi yang bisa di-toggle
   - Pilihan nominal cepat: Rp 50.000, Rp 100.000, Rp 250.000, Rp 500.000

3. **Notifikasi Sukses**
   - Menampilkan detail transfer yang harus dilakukan
   - Breakdown: Nominal + Kode Unik = Total Transfer
   - Nomor rekening tujuan
   - Warning: "Transfer sesuai nominal termasuk 3 digit kode unik di akhir agar otomatis terdeteksi"

4. **Riwayat Isi Saldo**
   - List transaksi dengan status badge (pending/sukses/batal)
   - Menampilkan: nominal, kode unik, bank, tanggal/jam
   - Empty state jika belum ada transaksi

---

## 2️⃣ DOMPET MITRA

### 2.1 Struktur Data

**Tabel Utama:** `mitra`
- `id_mitra` (Primary Key)
- `saldo_mitra` (decimal:2) - Saldo dompet mitra
- `nama_asli`
- `nama_panggilan`
- `no_wa`
- `status_mitra`
- `status_verifikasi`
- `rating` (decimal:2)
- `rejection_rate` (decimal:2)
- `timeout_streak`
- `offline_until` (datetime)
- `fcm_token`

**Tabel Penarikan:** `withdrawal_requests`
- `id` (Primary Key)
- `mitra_id` (Foreign Key)
- `bank_name` (string) - Nama bank/e-wallet
- `account_number` (string) - Nomor rekening tujuan
- `account_name` (string) - Nama pemilik rekening
- `nominal` (decimal:2) - Jumlah penarikan (minimal Rp 50.000)
- `status` (enum) - Status penarikan (pending, approved, rejected)
- `created_at` (timestamp)

### 2.2 Fitur Penarikan Dana Mitra

#### Alur Penarikan:
1. **Mitra membuka halaman Saldo & Penarikan** (`/mitra/saldo`)
2. **Melihat saldo tersedia** dalam card hijau
3. **Mengisi form tarik dana:**
   - Nama bank / e-wallet (BCA, Mandiri, GoPay, dll)
   - Nomor rekening tujuan
   - Nama pemilik rekening (sesuai buku tabungan)
   - Nominal (minimal Rp 50.000)
4. **Submit request penarikan**
5. **Admin memproses** (approve/reject)
6. **Saldo mitra berkurang** setelah disetujui

#### Validasi Penarikan:
```php
- bank_name: required, string
- account_number: required, string
- account_name: required, string
- nominal: required, numeric, min:50000
```

### 2.3 Status Penarikan Mitra

| Status | Keterangan | Badge |
|--------|-----------|-------|
| `pending` | Menunggu persetujuan admin | Warning (kuning) |
| `approved` | Penarikan disetujui, dana ditransfer | Success (hijau) |
| `rejected` | Penarikan ditolak | Danger (merah) |

### 2.4 Sumber Saldo Mitra

Saldo mitra bertambah dari:
1. **Komisi pesanan** - Dari order WFH, Jastip, Tenaga, Service
2. **Wallet transfer** - Transfer dari mitra lain
3. **Refund/Dispute** - Pengembalian dana dari dispute

Saldo mitra berkurang dari:
1. **Penarikan dana** - Withdraw ke rekening bank
2. **Wallet transfer** - Transfer ke mitra lain
3. **Penalty/Denda** - Dari sistem (jika ada)

### 2.5 UI/UX Dompet Mitra

**Halaman:** `resources/views/mitra/saldo.blade.php`

**Komponen:**
1. **Saldo Card**
   - Gradient hijau (0a5c36 → 1a7a4a)
   - Menampilkan saldo tersedia
   - Format Rp dengan pemisah ribuan
   - Selectable text (allow-select)

2. **Form Tarik Dana**
   - Input nama bank/e-wallet
   - Input nomor rekening
   - Input nama pemilik rekening
   - Input nominal dengan prefix "Rp"
   - Tombol "Ajukan Penarikan" dengan ikon arrow-down

3. **Riwayat Penarikan**
   - List request penarikan dengan status badge
   - Menampilkan: bank, nomor rekening, nama pemilik, nominal, tanggal
   - Nominal ditampilkan dengan tanda minus (merah)
   - Empty state jika belum ada riwayat

4. **Alert Messages**
   - Success alert (hijau) - Jika penarikan berhasil diajukan
   - Error alert (merah) - Jika ada error

---

## 3️⃣ PERBANDINGAN DOMPET PELANGGAN vs MITRA

| Aspek | Pelanggan | Mitra |
|-------|-----------|-------|
| **Tujuan Utama** | Menyimpan dana untuk pembayaran layanan | Menyimpan komisi dari layanan yang diberikan |
| **Cara Menambah Saldo** | Top-up via transfer bank (DANA/BCA) | Otomatis dari komisi pesanan |
| **Cara Mengurangi Saldo** | Pembayaran pesanan | Penarikan ke rekening bank |
| **Nominal Minimum** | Rp 10.000 | Rp 50.000 (untuk penarikan) |
| **Nominal Maksimum** | Rp 10.000.000 | Unlimited |
| **Verifikasi** | Otomatis via kode unik (3 digit) | Manual oleh admin |
| **Waktu Proses** | Instant (jika kode unik cocok) | 1-3 hari kerja (tergantung admin) |
| **Bank Tujuan** | Fixed (DANA & BCA) | Fleksibel (input manual) |
| **Notifikasi** | Otomatis saat request top-up | Otomatis saat request penarikan |

---

## 4️⃣ FITUR KEAMANAN & VALIDASI

### Dompet Pelanggan:
- ✅ Kode unik 3 digit untuk verifikasi transfer
- ✅ Validasi nominal (min-max)
- ✅ Validasi bank tujuan (whitelist: DANA, BCA)
- ✅ Timestamp setiap transaksi
- ✅ Status tracking (pending/sukses/batal)

### Dompet Mitra:
- ✅ Validasi nominal minimum (Rp 50.000)
- ✅ Approval workflow (pending → approved/rejected)
- ✅ Admin review sebelum transfer
- ✅ Timestamp setiap request
- ✅ Riwayat lengkap dengan status

---

## 5️⃣ INTEGRASI DENGAN SISTEM LAIN

### Dompet Pelanggan:
- **Order System** - Saldo digunakan untuk pembayaran pesanan
- **Notification System** - Notifikasi top-up request
- **Finance Module** - Tracking transaksi keuangan

### Dompet Mitra:
- **Order System** - Komisi otomatis masuk ke saldo
- **Wallet Transfer** - Transfer antar mitra
- **Withdrawal System** - Penarikan dana ke bank
- **Dispute System** - Refund dari dispute
- **Notification System** - Notifikasi penarikan request

---

## 6️⃣ MIGRATION & DATABASE

### Migration Terbaru:
**File:** `2026_05_10_132543_add_kode_unik_to_dompet_pelanggan_table.php`

Menambahkan kolom ke tabel `dompet_pelanggan`:
```sql
ALTER TABLE dompet_pelanggan ADD COLUMN kode_unik INT NULLABLE AFTER nominal;
ALTER TABLE dompet_pelanggan ADD COLUMN total_transfer DECIMAL(15,2) NULLABLE AFTER kode_unik;
ALTER TABLE dompet_pelanggan ADD COLUMN bank_tujuan VARCHAR(255) NULLABLE AFTER total_transfer;
```

### Tabel Terkait:
- `pelanggans` - Data pelanggan + saldo
- `dompet_pelanggan` - Riwayat top-up pelanggan
- `mitra` - Data mitra + saldo
- `withdrawal_requests` - Riwayat penarikan mitra
- `wallet_transfers` - Transfer antar mitra
- `disputes` - Dispute/sengketa pesanan

---

## 7️⃣ ROUTE & ENDPOINT

### Pelanggan:
- `GET /pelanggan/dompet` - Halaman dompet
- `POST /pelanggan/dompet/topup` - Submit top-up request

### Mitra:
- `GET /mitra/saldo` - Halaman saldo & penarikan
- `POST /mitra/withdrawal/request` - Submit penarikan request

---

## 8️⃣ CATATAN PENTING

1. **Kode Unik Pelanggan:**
   - Generate random 100-999
   - Ditambahkan ke nominal untuk verifikasi otomatis
   - Contoh: nominal 100.000 + kode 456 = transfer 100.456

2. **Saldo Mitra:**
   - Tidak ada top-up manual
   - Hanya bertambah dari komisi pesanan
   - Bisa transfer ke mitra lain (wallet transfer)

3. **Approval Workflow:**
   - Pelanggan: Otomatis (instant)
   - Mitra: Manual (admin review)

4. **Notifikasi:**
   - Pelanggan: Saat request top-up
   - Mitra: Saat request penarikan

5. **Audit Trail:**
   - Semua transaksi tercatat dengan timestamp
   - Status tracking untuk transparansi
   - Riwayat lengkap untuk laporan keuangan

---

## 📝 Kesimpulan

Sistem dompet Zasha Tower dirancang untuk:
- **Pelanggan:** Kemudahan top-up dengan verifikasi otomatis via kode unik
- **Mitra:** Manajemen komisi dan penarikan dana dengan approval workflow

Kedua sistem terintegrasi dengan baik dalam ekosistem Zasha Tower dan mendukung berbagai jenis transaksi keuangan.
