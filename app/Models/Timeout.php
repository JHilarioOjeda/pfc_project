<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeout extends Model
{
    use HasFactory;

    protected $table = 'timeouts';

    protected $fillable = [
        'id_charge',
        'type',
        'hours',
    ];

    protected $casts = [
        'hours' => 'decimal:6',
    ];

    public function charge()
    {
        return $this->belongsTo(Charge::class, 'id_charge');
    }
}
