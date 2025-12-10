<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends ApiController
{
    public function index(Request $request)
    {
        $query = AuditLog::query();

        if ($request->has('actor')) {
            $query->where('user_id', $request->actor);
        }

        if ($request->has('action')) {
            $query->where('action', 'like', '%'.$request->action.'%');
        }

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        $logs = $query->orderByDesc('created_at')->paginate(50);

        return $this->success($logs);
    }
}
