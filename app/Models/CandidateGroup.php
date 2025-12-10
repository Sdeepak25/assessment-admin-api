<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateGroup extends Model
{
    protected $table = 'candidate_groups';
    protected $guarded = [];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function candidates()
    {
        return $this->belongsToMany(Candidate::class, 'candidate_group_members', 'group_id', 'candidate_id');
    }
}
