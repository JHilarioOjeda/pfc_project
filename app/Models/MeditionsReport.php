<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeditionsReport extends Model
{
    use HasFactory;

    protected $table = 'meditions_report';

    protected $fillable = [
        'id_proccess',
        'requirement',
        'method',
        'register_date',
        'document_url',
    ];

    protected $casts = [
        'register_date' => 'datetime',
    ];
}
