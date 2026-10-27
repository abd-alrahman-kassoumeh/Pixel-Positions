<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvAnalysis extends Model
{
    protected $fillable = [
        'status',
        'recommended_job_ids'
    ];

    protected $casts = [
        'recommended_job_ids' => 'array'
    ];
}
