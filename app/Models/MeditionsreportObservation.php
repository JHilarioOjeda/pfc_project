<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeditionsreportObservation extends Model
{
    use HasFactory;

    protected $table = 'meditionsreport_observations';

    protected $fillable = [
        'id_medition_report',
        'thickness_in_microns',
        'visual_appearance',
    ];
}
