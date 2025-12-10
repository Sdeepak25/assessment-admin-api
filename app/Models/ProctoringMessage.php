<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProctoringMessage extends Model
{
    protected $table = 'proctoring_messages';
    protected $guarded = [];

    public function session()
    {
        return $this->belongsTo(ProctoringSession::class, 'proctoring_session_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
