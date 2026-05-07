<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPekerjaan extends Model
{
    use HasFactory;

    protected $table = 'kategori_pekerjaan';
    protected $primaryKey = 'id_kategori';
    public $timestamps = true;

    protected $fillable = [
        'nama_kategori',
        'svg_kategori',
    ];
}