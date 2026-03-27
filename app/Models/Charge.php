<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Charge extends Model
{
    use HasFactory;

    protected $table = 'charges';

    protected $fillable = [
        'id_proccess',
        'quantity_pieces',
        'status',
        'who_made',
        'made_date',
        'who_free',
        'free_date',
        'who_confirms',
        'confirm_date',
    ];

    protected $casts = [
        'made_date'    => 'datetime',
        'free_date'    => 'datetime',
        'confirm_date' => 'datetime',
    ];

    public function proccess(): BelongsTo
    {
        return $this->belongsTo(Proccess::class, 'id_proccess');
    }

    public function whoMade(): BelongsTo
    {
        return $this->belongsTo(User::class, 'who_made');
    }

    public function whoFree(): BelongsTo
    {
        return $this->belongsTo(User::class, 'who_free');
    }

    public function whoConfirms(): BelongsTo
    {
        return $this->belongsTo(User::class, 'who_confirms');
    }

    public function timeouts(): HasMany
    {
        return $this->hasMany(Timeout::class, 'id_charge');
    }
}
