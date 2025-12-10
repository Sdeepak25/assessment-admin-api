<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $guarded = [];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end'   => 'datetime',
        'paid_at'      => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
