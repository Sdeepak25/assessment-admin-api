<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateGroupMember extends Model
{
    protected $table = 'candidate_group_members';
    public $timestamps = false;
    protected $guarded = [];
}
