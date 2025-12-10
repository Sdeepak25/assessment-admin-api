<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAttemptItem extends Model
{
    protected $table = 'test_attempt_items';
    protected $guarded = [];

    protected $casts = [
        'response_choice_ids' => 'array',
    ];
}
