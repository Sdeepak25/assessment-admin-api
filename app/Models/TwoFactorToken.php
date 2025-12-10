<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwoFactorToken extends Model
{
    protected $table = 'two_factor_tokens';
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
