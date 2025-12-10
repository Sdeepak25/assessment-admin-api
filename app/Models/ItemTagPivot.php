<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTagPivot extends Model
{
    protected $table = 'item_tag_pivot';
    public $timestamps = false;
    protected $guarded = [];
}
