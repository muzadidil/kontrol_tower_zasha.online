<?php

namespace App\Enums;

enum TenagaOrderStatus: string
{
    case MenungguMitra      = 'menunggu_mitra';
    case Ditolak            = 'ditolak';
    case MenujuLokasi       = 'menuju_lokasi';
    case Dikerjakan         = 'dikerjakan';
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case Selesai            = 'selesai';
    case Dispute            = 'dispute';

    public function allowedTransitions(): array
    {
        return match($this) {
            self::MenungguMitra      => [self::MenujuLokasi, self::Ditolak],
            self::Ditolak            => [],
            self::MenujuLokasi       => [self::Dikerjakan],
            self::Dikerjakan         => [self::MenungguKonfirmasi],
            self::MenungguKonfirmasi => [self::Selesai, self::Dispute],
            self::Dispute            => [self::Selesai],
            self::Selesai            => [],
        };
    }

    public function canTransitionTo(self $next): bool { return in_array($next, $this->allowedTransitions()); }
}
