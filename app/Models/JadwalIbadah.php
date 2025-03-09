<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalIbadah extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'keterangan',
        'hari',
        'jam',
        'kategori',
    ];
}
