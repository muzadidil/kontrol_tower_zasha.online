<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pelanggan extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table      = 'pelanggans';
    protected $primaryKey = 'id_pelanggan';

    protected $fillable = [
        // existing columns
        'nama_pelanggan', 'email', 'password', 'no_wa',
        'tgl_lahir', 'foto', 'kode_zasha', 'saldo', 'status_verifikasi',
        // new columns (migration 003)
        'nama_panggilan', 'birth_date', 'saldo_pelanggan',
        'rating_pelanggan', 'is_profile_complete', 'fcm_token',
    ];

    protected $casts = [
        'birth_date'          => 'date',
        'saldo_pelanggan'     => 'decimal:2',
        'rating_pelanggan'    => 'decimal:2',
        'is_profile_complete' => 'boolean',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function alamats()
    {
        return $this->hasMany(Alamat::class, 'id_pelanggan');
    }

    public function getUmurAttribute(): int
    {
        $date = $this->birth_date ?? ($this->tgl_lahir ? \Carbon\Carbon::parse($this->tgl_lahir) : null);
        return $date ? $date->age : 0;
    }

    public function canOrderJasa(): bool
    {
        return $this->is_profile_complete && $this->umur >= 10;
    }

    public function canOrderAllServices(): bool
    {
        return $this->is_profile_complete && $this->umur >= 18;
    }

    public function canOrderJastipOnly(): bool
    {
        return $this->is_profile_complete && $this->umur >= 10 && $this->umur < 18;
    }

    public function getNameAttribute(): string
    {
        return $this->nama_pelanggan ?? '';
    }

    public function getIsVerifAttribute(): int
    {
        return $this->status_verifikasi ?? 0;
    }
}
