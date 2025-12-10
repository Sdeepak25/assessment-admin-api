<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function choices()
    {
        return $this->hasMany(ItemChoice::class);
    }
}
