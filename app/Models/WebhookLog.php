<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $table = 'webhook_logs';
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];

    public function webhook()
    {
        return $this->belongsTo(Webhook::class);
    }
}
