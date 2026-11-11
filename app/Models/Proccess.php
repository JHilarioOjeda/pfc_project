<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Proccess extends Model
{
    use HasFactory;

    protected $table = 'proccess';

    protected $fillable = [
        'id_tarima_np',
        'who_made',
        'id_line',
        'pieces_alreadyproccess',
        'operator_name',
        'document_url',
        'start_date',
        'finished_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'finished_date' => 'datetime',
    ];

    // Indica si ya se capturó información que se perdería/quedaría inconsistente al revertir el inicio
    public function hasRegisteredInfo(): bool
    {
        $charges = $this->charges_count ?? $this->charges()->count();
        $reports = $this->meditions_reports_count ?? $this->meditionsReports()->count();

        return $charges > 0
            || $reports > 0
            || (float) $this->pieces_alreadyproccess > 0
            || !empty($this->id_line)
            || !empty($this->document_url);
    }

    public function tarimaNp(): BelongsTo
    {
        return $this->belongsTo(TarimaNp::class, 'id_tarima_np');
    }

    public function whomade(): BelongsTo
    {
        return $this->belongsTo(User::class, 'who_made');
    }

    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class, 'id_proccess');
    }

    public function timeouts(): HasManyThrough
    {
        return $this->hasManyThrough(
            Timeout::class,
            Charge::class,
            'id_proccess',
            'id_charge'
        );
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(WorkLine::class, 'id_line');
    }

    public function meditionsReports(): HasMany
    {
        return $this->hasMany(MeditionsReport::class, 'id_proccess');
    }
}
