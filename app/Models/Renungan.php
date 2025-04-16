<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    protected static function booted()
    {
        static::creating(function ($renungan) {
            if (empty($renungan->slug)) {
                $renungan->slug = Str::slug($renungan->judul, '-');
            }
        });

        static::updating(function ($renungan) {
            if ($renungan->isDirty('judul')) {
                $renungan->slug = Str::slug($renungan->judul, '-');
            }
        });
    }
}
