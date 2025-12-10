<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTag extends Model
{
    protected $table = 'item_tags';
    protected $guarded = [];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_tag_pivot', 'tag_id', 'item_id');
    }
}
