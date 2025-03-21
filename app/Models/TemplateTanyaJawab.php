<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateTanyaJawab extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable = [
        'pertanyaan',
        'jawaban'
    ];
}
