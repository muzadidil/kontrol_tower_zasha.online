# Testing Fitur Kode Unik Top-Up

## Cara Test Fitur:

1. **Login sebagai Pelanggan**
   - Buka browser dan akses: `http://localhost/zasha-tower/public`
   - Login dengan akun pelanggan

2. **Akses Halaman Dompet**
   - Klik menu "Dompet" atau akses: `http://localhost/zasha-tower/public/pelanggan/dompet`

3. **Lakukan Top-Up**
   - Klik tombol "ISI SALDO (TOP UP)"
   - Pilih Media Transfer (DANA atau BCA)
   - Masukkan nominal, contoh: **100000** (100 ribu)
   - Isi Nama Pengirim
   - Klik "KONFIRMASI SEKARANG"

4. **Verifikasi Hasil**
   Setelah submit, Anda akan melihat:
   - **Total Transfer**: Rp 100.XXX (contoh: Rp 100.123)
   - **Rincian Transfer**:
     - Nominal Topup: Rp 100.000
     - Kode Unik: + 123 (angka random 100-999)
   - **Rekening Tujuan** sesuai pilihan bank

## Contoh Output:

```
Transfer tepat sejumlah:
Rp 100.123

Rincian Transfer:
Nominal Topup:    Rp 100.000
Kode Unik:        + 123

Ke Rekening (DANA):
082232458226
a/n Muzadidil Fuad
```

## Penjelasan Teknis:

### 1. Controller (`PelangganController.php`)
- Generate kode unik 3 digit: `rand(100, 999)`
- Hitung total: `$total_transfer = $nominal + $kode_unik`
- Simpan ke database dengan kolom: `kode_unik`, `total_transfer`, `bank_tujuan`

### 2. Database
Tabel `dompet_pelanggan` sekarang memiliki kolom:
- `nominal` - Nominal yang diminta pelanggan
- `kode_unik` - Kode unik 3 digit
- `total_transfer` - Total yang harus ditransfer
- `bank_tujuan` - Bank/metode pembayaran

### 3. View (`dompet.blade.php`)
- Menampilkan total transfer dengan format: Rp XXX.XXX
- Breakdown nominal + kode unik
- Riwayat transaksi menampilkan detail kode unik

## Troubleshooting:

Jika tidak muncul perubahan:
1. Clear cache: `php artisan cache:clear`
2. Clear config: `php artisan config:clear`
3. Clear view: `php artisan view:clear`
4. Refresh browser dengan Ctrl+F5 (hard refresh)
5. Pastikan sudah login sebagai pelanggan
6. Cek database apakah kolom baru sudah ada: `DESCRIBE dompet_pelanggan;`
