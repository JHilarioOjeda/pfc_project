<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeditionsReportFolio extends Model
{
    protected $table = 'meditions_report_folios';

    protected $fillable = [
        'folio',
    ];

    public function reports()
    {
        return $this->belongsToMany(
            MeditionsReport::class,
            'meditions_report_folio_report',
            'id_folio',
            'id_meditions_report'
        )->withTimestamps();
    }

    // Folio existente cuya combinación de reportes enlazados es EXACTAMENTE
    // el conjunto de ids dado (mismo tamaño y mismos miembros). Si existe,
    // esa combinación ya se imprimió antes y se trata de una reimpresión.
    public static function findExactForReports($reportIds): ?self
    {
        $ids = collect($reportIds)->map(fn ($id) => (int) $id)->unique()->values();

        if ($ids->isEmpty()) {
            return null;
        }

        $count = $ids->count();

        return static::query()
            ->whereHas('reports', function ($query) use ($ids) {
                $query->whereIn('meditions_report.id', $ids->all());
            }, '=', $count)
            ->has('reports', '=', $count)
            ->orderByDesc('id')
            ->first();
    }
}
