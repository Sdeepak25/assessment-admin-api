<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemMedia extends Model
{
    protected $table = 'item_media';
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
