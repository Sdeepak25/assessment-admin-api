<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $table = 'tenants';
    protected $guarded = [];

    protected $casts = [
        'feature_flags' => 'array',
        'sso_config'    => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
