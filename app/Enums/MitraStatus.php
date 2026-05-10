<?php

namespace App\Enums;

enum MitraStatus: string
{
    case PendingDocument = 'pending_document';
    case PendingReview   = 'pending_review';
    case Active          = 'active';
    case Rejected        = 'rejected';
    case Suspended       = 'suspended';

    public function label(): string
    {
        return match($this) {
            self::PendingDocument => 'Menunggu Dokumen',
            self::PendingReview   => 'Menunggu Verifikasi Admin',
            self::Active          => 'Aktif',
            self::Rejected        => 'Ditolak',
            self::Suspended       => 'Dinonaktifkan',
        };
    }

    public function isActive(): bool { return $this === self::Active; }
}
