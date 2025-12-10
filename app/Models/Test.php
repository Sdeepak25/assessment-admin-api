<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    protected $table = 'tests';
    protected $guarded = [];

    protected $casts = [
        'total_duration' => 'integer',
        'total_weight' => 'integer',
    ];

    public function sections()
    {
        return $this->hasMany(TestSection::class, 'test_id');
    }
}
