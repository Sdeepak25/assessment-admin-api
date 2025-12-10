<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsTestSummary extends Model
{
    protected $table = 'analytics_test_summary';
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
