<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $table = 'webhooks';
    protected $guarded = [];

    protected $casts = [
        'events' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function logs()
    {
        return $this->hasMany(WebhookLog::class);
    }
}
