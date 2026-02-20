<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proccess extends Model
{
    use HasFactory;

    protected $table = 'proccess';

    protected $fillable = [
        'id_tarima_np',
        'who_made',
        'id_line',
        'pieces_alreadyproccess',
        'operator_name',
        'document_url',
        'start_date',
        'finished_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'finished_date' => 'datetime',
    ];
}
