<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarimaNp extends Model
{
    use HasFactory;

    protected $table = 'tarima_nps';

    protected $fillable = [
        'id_np',
        'id_tarima',
        'quantity',
        'oc',
        'of',
        'status_cont',
    ];

    protected $casts = [
        'status_cont' => 'boolean',
    ];
}
