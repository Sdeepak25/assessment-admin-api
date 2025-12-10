<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class TestSection extends Model
// {
//     protected $table = 'test_sections';
//     protected $guarded = [];

//     public function test()
//     {
//         return $this->belongsTo(Test::class);
//     }

//     public function items()
//     {
//         return $this->hasMany(TestSectionItem::class, 'test_section_id');
//     }
// }

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestSection extends Model
{
    protected $table = 'test_sections';
    protected $guarded = [];

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id');
    }
    public function items()
    {
        // if you want items via pivot
        return $this->belongsToMany(Item::class, 'test_section_items', 'test_section_id', 'item_id')
                    ->withPivot(['id','weight','sort_order','requires_manual_grading','partial_scoring_rule','negative_marking']);
    }

}

