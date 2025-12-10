<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsychometricScale extends Model
{
    protected $table = 'psychometric_scales';
    protected $guarded = [];

    public function model()
    {
        return $this->belongsTo(PsychometricModel::class, 'model_id');
    }

    public function parent()
    {
        return $this->belongsTo(PsychometricScale::class, 'parent_scale_id');
    }

    public function children()
    {
        return $this->hasMany(PsychometricScale::class, 'parent_scale_id');
    }

    public function items()
    {
        return $this->hasMany(PsychometricItem::class, 'scale_id');
    }
}
