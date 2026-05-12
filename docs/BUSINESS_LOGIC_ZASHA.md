# BUSINESS LOGIC & SOP ZASHA
> Dokumen induk ketentuan bisnis, alur sistem, dan aturan operasional platform Zasha.
> Update dokumen ini setiap ada aturan atau fitur baru.

---

## BAB 1 — ORDER & PEMESANAN

### 1.1 Order Langsung
- Sifatnya **hari ini** — order sekarang, selesai sekarang
- Pembayaran penuh di awal sebelum mitra berangkat
- Tidak ada sistem DP

### 1.2 Order Inden
- Sifatnya **terjadwal** — untuk kebutuhan di tanggal tertentu di masa depan
- Contoh: butuh mitra untuk acara nikahan tanggal 20
- Pelanggan memilih mitra yang tersedia di tanggal tersebut
- **Wajib bayar DP 50%** sebagai ikatan sebelum mitra dikonfirmasi

---

## BAB 2 — ALUR ORDER INDEN

### 2.1 Tahapan Lengkap

| No | Tahap | Pelaku | Keterangan |
|----|-------|--------|------------|
| 1 | Pilih mitra & tanggal | Pelanggan | Lihat mitra yang aktif/tersedia |
| 2 | Approve order | Mitra | Mitra konfirmasi sanggup |
| 3 | Bayar DP 50% | Pelanggan | Masuk Escrow, bukan langsung ke mitra |
| 4 | Hari H — klik "Saya Sudah Tiba" | Mitra | Trigger notifikasi ke pelanggan |
| 5 | Bayar pelunasan 50% | Pelanggan | Setelah mitra tiba, masuk Escrow |
| 6 | Pekerjaan selesai | Mitra | — |
| 7 | Konfirmasi selesai | Pelanggan | Maksimal 24 jam setelah jadwal selesai |
| 8 | Dana cair 100% | Sistem → Mitra | Setelah pelanggan konfirmasi |

### 2.2 Auto-Konfirmasi
- Jika pelanggan **tidak konfirmasi dalam 24 jam** setelah jadwal selesai, sistem otomatis cairkan dana ke mitra
- Tujuan: mitra tidak menunggu terlalu lama

---

## BAB 3 — PEMBAYARAN & ESCROW

### 3.1 Struktur Pembayaran Order Inden

| Tahap | Waktu | Nominal |
|-------|-------|---------|
| DP | Saat booking dikonfirmasi mitra | 50% |
| Pelunasan | Hari H saat mitra tiba | 50% |
| Dana cair ke mitra | Setelah pelanggan konfirmasi selesai | 100% |

### 3.2 Sistem Escrow
- Semua pembayaran **ditahan sistem** terlebih dahulu
- Dana baru dilepas ke mitra setelah pekerjaan dikonfirmasi selesai
- Tujuan: melindungi pelanggan dari mitra yang tidak bertanggung jawab

---

## BAB 4 — KEBIJAKAN CANCEL

### 4.1 Cancel oleh Pelanggan

| Waktu Cancel | Konsekuensi |
|---|---|
| Sebelum H-1 | Tidak kena penalti, DP kembali penuh |
| H-1 atau kurang | Kena penalti (DP tidak kembali / sebagian hangus) |

### 4.2 Cancel oleh Mitra

| Waktu Cancel | Konsekuensi |
|---|---|
| Sebelum H-2 | Tidak kena penalti, sistem cari pengganti |
| H-2 atau kurang | Kena penalti: suspend 2 hari + rating turun |

### 4.3 Alur Jika Mitra Cancel
1. Sistem otomatis cari mitra pengganti
2. Jika pengganti ditemukan → order lanjut, DP tidak kembali
3. Jika pengganti **tidak ditemukan** → DP kembali 100% ke pelanggan
4. Mitra yang cancel tetap kena penalti (suspend + rating turun)

---

## BAB 5 — PENALTI & SANKSI

### 5.1 Penalti Mitra
| Pelanggaran | Sanksi |
|---|---|
| Cancel inden H-2 atau kurang | Suspend 2 hari + rating turun |
| Tidak hadir tanpa konfirmasi | Suspend + rating turun signifikan |

### 5.2 Penalti Pelanggan
| Pelanggaran | Sanksi |
|---|---|
| Cancel H-1 atau kurang | DP tidak kembali / sebagian hangus |

---

## BAB 6 — DISPUTE & KOMPLAIN

### 6.1 Alur Dispute
- Pelanggan punya waktu **24 jam** setelah jadwal selesai untuk mengajukan komplain
- Zasha bertindak sebagai **penengah**
- Selama dispute berlangsung, dana tetap ditahan di Escrow

### 6.2 Resolusi
- Jika terbukti mitra bersalah → dana kembali ke pelanggan
- Jika tidak terbukti → dana cair ke mitra

---

> **Catatan:** Dokumen ini bersifat living document. Tambahkan bab baru sesuai perkembangan fitur dan kebijakan Zasha.
