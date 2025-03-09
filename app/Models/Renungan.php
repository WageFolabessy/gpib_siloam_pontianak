<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renungan extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable = [
        'judul',
        'alkitab',
        'bacaan_alkitab',
        'thumbnail',
        'isi_bacaan',
        'slug',
    ];
}
