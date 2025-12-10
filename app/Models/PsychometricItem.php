<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsychometricItem extends Model
{
    protected $table = 'psychometric_items';
    protected $guarded = [];

    public function scale()
    {
        return $this->belongsTo(PsychometricScale::class, 'scale_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
