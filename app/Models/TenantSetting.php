<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    protected $table = 'tenant_settings';
    protected $guarded = [];

    protected $casts = [
        'password_policy' => 'array',
        'smtp_config'     => 'array',
        'storage_config'  => 'array',
        'data_retention'  => 'array',
    ];
}
