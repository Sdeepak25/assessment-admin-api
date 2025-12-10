<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $table = 'notification_logs';
    protected $guarded = [];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function template()
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }
}
