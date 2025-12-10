<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProctoringFlag extends Model
{
    protected $table = 'proctoring_flags';
    protected $guarded = [];

    public function session()
    {
        return $this->belongsTo(ProctoringSession::class, 'proctoring_session_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
