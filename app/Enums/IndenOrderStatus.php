<?php

namespace App\Enums;

enum IndenOrderStatus: string
{
    case MenungguMitra      = 'menunggu_mitra';
    case Ditolak            = 'ditolak';
    case MenungguDp         = 'menunggu_dp';
    case DpDibayar          = 'dp_dibayar';
    case Dikerjakan         = 'dikerjakan';
    case MenungguPelunasan  = 'menunggu_pelunasan';
    case Selesai            = 'selesai';
    case Dispute            = 'dispute';

    public function allowedTransitions(): array
    {
        return match($this) {
            self::MenungguMitra      => [self::MenungguDp, self::Ditolak],
            self::MenungguDp         => [self::DpDibayar],
            self::DpDibayar          => [self::Dikerjakan],
            self::Dikerjakan         => [self::MenungguPelunasan],
            self::MenungguPelunasan  => [self::Selesai, self::Dispute],
            self::Dispute            => [self::Selesai],
            self::Ditolak            => [],
            self::Selesai            => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions());
    }

    public function label(): string
    {
        return match($this) {
            self::MenungguMitra      => 'Menunggu Approval Mitra',
            self::Ditolak            => 'Ditolak Mitra',
            self::MenungguDp         => 'Menunggu Pembayaran DP',
            self::DpDibayar          => 'DP Sudah Dibayar',
            self::Dikerjakan         => 'Sedang Dikerjakan',
            self::MenungguPelunasan  => 'Menunggu Pelunasan',
            self::Selesai            => 'Selesai',
            self::Dispute            => 'Dispute',
        };
    }
}
