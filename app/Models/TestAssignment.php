<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAssignment extends Model
{
    protected $table = 'test_assignments';
    protected $guarded = [];

    public function attempts()
    {
        return $this->hasMany(TestAttempt::class);
    }
}
