<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsychometricModel extends Model
{
    protected $table = 'psychometric_models';
    protected $guarded = [];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scales()
    {
        return $this->hasMany(PsychometricScale::class, 'model_id');
    }

    public function normGroups()
    {
        return $this->hasMany(PsychometricNormGroup::class, 'model_id');
    }
}
