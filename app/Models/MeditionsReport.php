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

    // Folios (documentos impresos) de los que este reporte ha formado parte.
    // Un mismo reporte puede pertenecer a más de uno a lo largo del tiempo
    // (se imprime solo primero, se reimprime después combinado con otros
    // reportes bajo un folio distinto), por eso es muchos-a-muchos.
    public function folios()
    {
        return $this->belongsToMany(
            MeditionsReportFolio::class,
            'meditions_report_folio_report',
            'id_meditions_report',
            'id_folio'
        )->withTimestamps();
    }

    // El folio más reciente asignado a este reporte, o null si nunca ha sido
    // impreso como parte de un lote. Si la relación ya fue cargada (p. ej.
    // con ->with('folios')) reutiliza la colección en memoria para evitar
    // una consulta extra dentro de listados.
    public function latestFolio(): ?MeditionsReportFolio
    {
        if ($this->relationLoaded('folios')) {
            return $this->folios->sortByDesc('id')->first();
        }

        return $this->folios()->orderByDesc('meditions_report_folios.id')->first();
    }
}
