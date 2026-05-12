<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Setting extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'key';
    protected $keyType    = 'string';
    public $incrementing  = false;
    protected $fillable   = ['key', 'value'];

    public static function get(string $key, $default = null): ?string
    {
        $row = DB::table('settings')->where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public static function set(string $key, $value): void
    {
        DB::table('settings')->upsert(
            ['key' => $key, 'value' => $value, 'updated_at' => now(), 'created_at' => now()],
            ['key'],
            ['value', 'updated_at']
        );
    }

    /** Komisi Zasha dalam persen (default 10). */
    public static function komisiPersen(): float
    {
        return (float) (static::get('komisi_zasha_persen', '10') ?: 10);
    }
}
