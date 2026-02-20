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
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
