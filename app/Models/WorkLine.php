<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkLine extends Model
{
    use HasFactory;

    protected $table = 'work_lines';

    protected $fillable = [
        'name',
    ];
}
