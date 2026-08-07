<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MeditionsreportObservation;
use App\Models\Proccess;

class MeditionsReport extends Model
{
    use HasFactory;

    protected $table = 'meditions_report';

    protected $fillable = [
        'id_proccess',
        'folio',
        'requirement',
        'method',
        'register_date',
        'document_url',
        'status',
        'notes',
    ];

    protected $casts = [
        'register_date' => 'datetime',
    ];

    public function observations()
    {
        return $this->hasMany(MeditionsreportObservation::class, 'id_medition_report');
    }

    public function proccess()
    {
        return $this->belongsTo(Proccess::class, 'id_proccess');
    }
}
