<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberPart extends Model
{
    use HasFactory;

    protected $table = 'number_parts';

    protected $fillable = [
        'partnumber',
        'process',
        'details',
        'microns',
        'inches',
        'decimeters',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
