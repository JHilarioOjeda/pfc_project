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
}
