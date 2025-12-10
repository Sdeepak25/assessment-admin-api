<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscriptions';
    protected $guarded = [];

    protected $casts = [
        'started_at'  => 'datetime',
        'ends_at'     => 'datetime',
        'cancelled_at'=> 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
