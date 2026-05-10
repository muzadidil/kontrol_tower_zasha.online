<?php

namespace App\Enums;

enum ServiceOrderStatus: string
{
    case MenungguMitra           = 'menunggu_mitra';
    case Ditolak                 = 'ditolak';
    case MenujuLokasi            = 'menuju_lokasi';
    case Diagnosa                = 'diagnosa';
    case MenungguKonfirmasiHarga = 'menunggu_konfirmasi_harga';
    case Dikerjakan              = 'dikerjakan';
    case MenungguKonfirmasi      = 'menunggu_konfirmasi';
    case Selesai                 = 'selesai';
    case Dispute                 = 'dispute';

    public function allowedTransitions(): array
    {
        return match($this) {
            self::MenungguMitra           => [self::MenujuLokasi, self::Ditolak],
            self::Ditolak                 => [],
            self::MenujuLokasi            => [self::Diagnosa],
            self::Diagnosa                => [self::MenungguKonfirmasiHarga],
            self::MenungguKonfirmasiHarga => [self::Dikerjakan],
            self::Dikerjakan              => [self::MenungguKonfirmasi],
            self::MenungguKonfirmasi      => [self::Selesai, self::Dispute],
            self::Dispute                 => [self::Selesai],
            self::Selesai                 => [],
        };
    }

    public function canTransitionTo(self $next): bool { return in_array($next, $this->allowedTransitions()); }
}
