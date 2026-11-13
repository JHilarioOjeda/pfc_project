<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'company_name',
        'address',
        'zip_code',
        'telephone',
        'email',
        'rfc',
        'line',
        'measurement_format',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'measurement_format' => 'integer',
    ];

    // Formatos disponibles para el reporte de medición (ver resources/views/formats/measurement).
    public const MEASUREMENT_FORMATS = [
        1 => 'Formato 1',
        2 => 'Formato 2',
        3 => 'Formato 3',
    ];

    public const DEFAULT_MEASUREMENT_FORMAT = 2;
}
