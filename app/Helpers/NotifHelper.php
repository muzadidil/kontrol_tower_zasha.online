<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class NotifHelper
{
    public static function kirim(int $id_pelanggan, string $judul, string $pesan, string $tipe = 'info', ?string $url = null): void
    {
        DB::table('notifikasi')->insert([
            'id_pelanggan' => $id_pelanggan,
            'judul'        => $judul,
            'pesan'        => $pesan,
            'tipe'         => $tipe,
            'url'          => $url,
            'is_read'      => false,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public static function unreadCount(int $id_pelanggan): int
    {
        return DB::table('notifikasi')
            ->where('id_pelanggan', $id_pelanggan)
            ->where('is_read', false)
            ->count();
    }

    public static function tandaiSemuaDibaca(int $id_pelanggan): void
    {
        DB::table('notifikasi')
            ->where('id_pelanggan', $id_pelanggan)
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);
    }
}
