<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeout extends Model
{
    use HasFactory;

    protected $table = 'timeouts';

    protected $fillable = [
        'id_proccess',
        'type',
        'hours',
    ];
}
