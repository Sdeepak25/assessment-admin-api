<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobRole extends Model
{
    protected $table = 'job_roles';
    protected $guarded = [];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
