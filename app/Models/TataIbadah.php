<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TataIbadah extends Model
{
    protected $fillable = [
        'judul',
        'file_pdf_path',
        'tanggal_terbit',
        'slug',
        'is_published',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tataIbadah) {
            $slugBase = Str::slug($tataIbadah->judul . '-' . $tataIbadah->tanggal_terbit->format('d-m-Y'));
            $tataIbadah->slug = static::generateUniqueSlug($slugBase);
        });

        static::updating(function ($tataIbadah) {
            if ($tataIbadah->isDirty('judul') || $tataIbadah->isDirty('tanggal_terbit')) {
                $slugBase = Str::slug($tataIbadah->judul . '-' . $tataIbadah->tanggal_terbit->format('d-m-Y'));
                $tataIbadah->slug = static::generateUniqueSlug($slugBase, $tataIbadah->id);
            }
        });
    }

    protected static function generateUniqueSlug($slugBase, $ignoreId = null)
    {
        $slug = $slugBase;
        $count = 1;
        $query = static::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        while ($query->exists()) {
            $slug = $slugBase . '-' . $count++;
            $query = static::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }
        return $slug;
    }
}
