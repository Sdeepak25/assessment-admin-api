<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSectionItem extends Model
{
    protected $table = 'test_section_items';
    protected $guarded = [];

    public function section()
    {
        return $this->belongsTo(TestSection::class, 'test_section_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
