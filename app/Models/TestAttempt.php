<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    protected $table = 'test_attempts';
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(TestAttemptItem::class);
    }
}
