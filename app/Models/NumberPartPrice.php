<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberPartPrice extends Model
{
    use HasFactory;

    protected $table = 'number_part_prices';

    protected $fillable = [
        'number_part_id',
        'price',
        'price_date',
    ];

    protected $casts = [
        'price' => 'decimal:6',
        'price_date' => 'date',
    ];

    public function numberPart()
    {
        return $this->belongsTo(NumberPart::class);
    }
}
