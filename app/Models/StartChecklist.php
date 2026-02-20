<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StartChecklist extends Model
{
    use HasFactory;

    protected $table = 'start_checklist';

    protected $fillable = [
        'id_user',
        'questions',
        'register_date',
    ];

    protected $casts = [
        'questions' => 'array',
        'register_date' => 'datetime',
    ];
}
