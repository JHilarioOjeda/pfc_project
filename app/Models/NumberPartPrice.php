<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberPartPrice extends Model
{
    use HasFactory;

    protected $table = 'np_prices';

    protected $fillable = [
        'id_np',
        'price',
        'price_date',
    ];

    protected $casts = [
        'price'      => 'decimal:6',
        'price_date' => 'datetime',
    ];

    public function numberPart()
    {
        return $this->belongsTo(NumberPart::class, 'id_np');
    }
}
