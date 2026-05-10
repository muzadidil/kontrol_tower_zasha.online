<?php

namespace App\Enums;

enum MitraKategori: string
{
    case Tenaga  = 'TNG';
    case Wfh     = 'WFH';
    case Jastip  = 'JST';
    case Service = 'SVC';

    public function label(): string
    {
        return match($this) {
            self::Tenaga  => 'Jasa Tenaga',
            self::Wfh     => 'WFH / Digital',
            self::Jastip  => 'Jastip / Kurir',
            self::Service => 'Service / Teknisi',
        };
    }

    public static function fromId(string $mitraId): self
    {
        return self::from(substr($mitraId, 0, 3));
    }

    public function canAddLayanan(string $layananKategori): bool
    {
        return $this->value === $layananKategori;
    }

    public function dashboardRoute(): string
    {
        return match($this) {
            self::Tenaga  => 'mitra.tenaga.dashboard',
            self::Wfh     => 'mitra.wfh.dashboard',
            self::Jastip  => 'mitra.jastip.dashboard',
            self::Service => 'mitra.service.dashboard',
        };
    }

    public function needsVehicle(): bool { return in_array($this, [self::Tenaga, self::Jastip, self::Service]); }
    public function needsLocation(): bool { return in_array($this, [self::Tenaga, self::Wfh, self::Service]); }
    public function needsSim(): bool { return $this === self::Jastip; }
}
