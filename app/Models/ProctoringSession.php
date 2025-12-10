<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProctoringSession extends Model
{
    protected $table = 'proctoring_sessions';
    protected $guarded = [];

    protected $casts = [
        'device_info' => 'array',
    ];
}
