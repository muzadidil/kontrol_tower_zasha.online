<?php

namespace App\Enums;

enum JastipOrderStatus: string
{
    case MenungguMitra      = 'menunggu_mitra';
    case Ditolak            = 'ditolak';
    case MenujuPickup       = 'menuju_pickup';
    case Belanja            = 'belanja';
    case MenujuPengantaran  = 'menuju_pengantaran';
    case Diantar            = 'diantar';
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case Selesai            = 'selesai';
    case Dispute            = 'dispute';

    public function allowedTransitions(): array
    {
        return match($this) {
            self::MenungguMitra      => [self::MenujuPickup, self::Ditolak],
            self::Ditolak            => [],
            self::MenujuPickup       => [self::Belanja],
            self::Belanja            => [self::MenujuPengantaran],
            self::MenujuPengantaran  => [self::Diantar],
            self::Diantar            => [self::MenungguKonfirmasi],
            self::MenungguKonfirmasi => [self::Selesai, self::Dispute],
            self::Dispute            => [self::Selesai],
            self::Selesai            => [],
        };
    }

    public function canTransitionTo(self $next): bool { return in_array($next, $this->allowedTransitions()); }
}
