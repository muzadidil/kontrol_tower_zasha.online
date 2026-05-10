<?php

namespace App\Enums;

enum WfhOrderStatus: string
{
    case PendingPembayaran  = 'pending_pembayaran';
    case MenungguMitra      = 'menunggu_mitra';
    case Ditolak            = 'ditolak';
    case Dikerjakan         = 'dikerjakan';
    case FileTerkirim       = 'file_terkirim';
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case Dispute            = 'dispute';
    case Selesai            = 'selesai';
    case Refund             = 'refund';

    public function allowedTransitions(): array
    {
        return match($this) {
            self::PendingPembayaran  => [self::MenungguMitra],
            self::MenungguMitra      => [self::Dikerjakan, self::Ditolak],
            self::Ditolak            => [],
            self::Dikerjakan         => [self::FileTerkirim],
            self::FileTerkirim       => [self::MenungguKonfirmasi],
            self::MenungguKonfirmasi => [self::Selesai, self::Dispute],
            self::Dispute            => [self::Selesai, self::Refund],
            self::Selesai            => [],
            self::Refund             => [],
        };
    }

    public function canTransitionTo(self $next): bool { return in_array($next, $this->allowedTransitions()); }
}
