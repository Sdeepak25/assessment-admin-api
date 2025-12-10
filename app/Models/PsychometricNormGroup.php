<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsychometricNormGroup extends Model
{
    protected $table = 'psychometric_norm_groups';
    protected $guarded = [];

    protected $casts = [
        'stats' => 'array',
    ];

    public function model()
    {
        return $this->belongsTo(PsychometricModel::class, 'model_id');
    }
}
