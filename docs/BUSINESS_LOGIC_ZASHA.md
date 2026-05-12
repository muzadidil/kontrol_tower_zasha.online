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

## BAB 6 — KONFIRMASI PEKERJAAN & SIKLUS PERBAIKAN

### 6.1 Alur Normal — Pelanggan Konfirmasi Selesai
1. Mitra menyelesaikan pekerjaan → klik **"Pekerjaan Selesai"** di dashboard
2. Status order tracking: `dikerjakan` → `selesai_mitra`
3. Pelanggan menerima notifikasi + tombol **"Konfirmasi Pekerjaan"** muncul di halaman tracking
4. Pelanggan cek hasil kerja → klik **"Pekerjaan Selesai"**
5. Sistem:
   - Status tracking: `selesai_mitra` → `selesai`
   - Escrow status: `held` → `released`
   - Saldo mitra bertambah sebesar `harga_modal` (harga jual − komisi Zasha)
   - Status mitra: `sibuk` → `online` (otomatis bebas terima order baru)
6. Pelanggan diarahkan ke halaman riwayat untuk memberi rating

### 6.2 Alur Perbaikan — Pelanggan Tandai "Belum Selesai"
Jika pelanggan merasa pekerjaan belum sesuai/belum tuntas:

1. Pelanggan klik **"Belum Selesai"** di halaman tracking
2. Sistem:
   - Status tracking: `selesai_mitra` → `belum_selesai`
   - Status mitra: **TETAP `sibuk`** (fokus perbaiki order ini, tidak boleh terima order baru)
   - Escrow tetap `held` — dana belum dilepas
   - Notifikasi dikirim ke mitra
3. Halaman tracking pelanggan menampilkan banner orange **"Mitra Sedang Memperbaiki"**
4. Dashboard mitra menampilkan banner orange **"Pelanggan Tandai Belum Selesai"** + tombol **"Saya Sudah Memperbaiki"**
5. Mitra perbaiki kerjaan → klik **"Saya Sudah Memperbaiki"**
6. Sistem:
   - Status tracking: `belum_selesai` → `selesai_mitra` (siklus ulang)
   - Pelanggan kembali melihat tombol **"Konfirmasi Pekerjaan"**
7. Siklus dapat berulang sampai pelanggan klik **"Pekerjaan Selesai"** atau membuka **dispute** (lihat BAB 7)

### 6.3 Aturan Penting Siklus Perbaikan
- **Mitra TETAP `sibuk`** sepanjang siklus perbaikan — tidak boleh terima order baru
- **Order `belum_selesai` tetap aktif** di dashboard mitra sampai diselesaikan atau dijadikan dispute
- **Dana tetap ditahan di Escrow** sampai status `selesai` atau resolusi dispute
- Mitra hanya kembali ke `online` setelah order benar-benar `selesai` (pelanggan konfirmasi) atau dialihkan ke `dispute`
- Tidak ada batasan jumlah siklus perbaikan, tapi sebaiknya pelanggan buka dispute jika sudah 2× perbaikan masih bermasalah

### 6.4 Auto-Konfirmasi (Order Inden)
- Berlaku khusus untuk Order Inden
- Jika pelanggan **tidak konfirmasi dalam 24 jam** setelah jadwal selesai dan tidak menandai "Belum Selesai", sistem otomatis cairkan dana ke mitra
- Tujuan: mitra tidak menunggu terlalu lama

---

## BAB 7 — DISPUTE & KOMPLAIN

### 7.1 Kapan Dispute Dibuka
- Pelanggan punya waktu **24 jam** setelah jadwal selesai untuk mengajukan komplain
- Biasanya dibuka setelah siklus perbaikan (BAB 6.2) gagal menghasilkan kepuasan
- Selama dispute berlangsung, dana tetap ditahan di Escrow

### 7.2 Peran Zasha
- Zasha bertindak sebagai **penengah** netral
- Mengumpulkan bukti dari kedua belah pihak (foto, chat, deskripsi keluhan)
- Memutuskan resolusi berdasarkan bukti

### 7.3 Resolusi
| Hasil | Konsekuensi |
|---|---|
| Mitra terbukti bersalah | Dana kembali ke pelanggan (escrow refunded) |
| Mitra tidak bersalah | Dana cair ke mitra (escrow released) |

---

## BAB 8 — STATUS ORDER TRACKING (REFERENSI TEKNIS)

### 8.1 Daftar Status & Transisi Valid
| Status | Arti | Transisi Berikutnya |
|--------|------|---------------------|
| `pending` | Menunggu mitra menerima | `accepted`, `ditolak_mitra` |
| `accepted` | Mitra menerima order | `menuju_lokasi` |
| `menuju_lokasi` | Mitra dalam perjalanan | `di_lokasi` |
| `di_lokasi` | Mitra tiba di lokasi | `dikerjakan` |
| `dikerjakan` | Pekerjaan sedang berlangsung | `selesai_mitra` |
| `selesai_mitra` | Mitra menandai selesai, menunggu konfirmasi pelanggan | `selesai`, `belum_selesai` |
| `belum_selesai` | Pelanggan menandai belum selesai, menunggu mitra perbaiki | `dikerjakan`, `selesai_mitra` |
| `selesai` | Order selesai, dana sudah cair (TERMINAL) | — |
| `ditolak_mitra` | Mitra menolak order (TERMINAL) | — |
| `dibatalkan` | Order dibatalkan (TERMINAL) | — |
| `dispute` | Sedang dalam dispute | `selesai`, `dibatalkan` |

### 8.2 Status Online Mitra
| Status | Arti | Bisa Terima Order Baru? |
|--------|------|------------------------|
| `online` | Siap terima order | ✅ Ya |
| `sibuk` | Sedang mengerjakan order aktif (accepted s/d belum_selesai) | ❌ Tidak |
| `offline` | Mitra tidak aktif | ❌ Tidak |
| `suspended` | Akun dibekukan karena pelanggaran | ❌ Tidak |

**Catatan penting:** Status mitra otomatis kembali ke `online` HANYA saat:
- Order benar-benar `selesai` — pelanggan konfirmasi & dana cair
- Order ditolak (`ditolak_mitra`) atau dibatalkan (`dibatalkan`)
- Resolusi dispute selesai

Status `sibuk` mencakup seluruh lifecycle order aktif, termasuk siklus perbaikan
(`belum_selesai`). Mitra tidak bisa terima order baru sampai order saat ini
benar-benar selesai — tujuannya agar mitra fokus menyelesaikan komitmen.

---

> **Catatan:** Dokumen ini bersifat living document. Tambahkan bab baru sesuai perkembangan fitur dan kebijakan Zasha.
