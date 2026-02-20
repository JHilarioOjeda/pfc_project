<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarima extends Model
{
    use HasFactory;

    protected $table = 'tarima';

    protected $fillable = [
        'id_customer',
        'who_register',
        'serial_number',
        'register_date',
        'document_url',
        'active',
    ];

    protected $casts = [
        'register_date' => 'datetime',
        'active' => 'boolean',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'id_customer');
    }

    public function who_register(){
        return $this->belongsTo(User::class, 'who_register');
    }

    public function tarimaNps(){
        return $this->hasMany(TarimaNp::class, 'id_tarima');
    }
}
