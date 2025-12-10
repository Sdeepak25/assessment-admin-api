<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ProctoringSession;
use App\Models\ProctoringFlag;
use App\Models\ProctoringMessage;

class ProctoringController extends ApiController
{
    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $sessions = ProctoringSession::where('tenant_id', $tenantId)
            ->paginate(20);

        return $this->success($sessions);
    }

    public function show(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $session = ProctoringSession::where('tenant_id', $tenantId)
            ->with(['flags', 'messages'])
            ->findOrFail($id);

        return $this->success($session);
    }

    public function addFlag(Request $request, $id)
    {
        $session = ProctoringSession::findOrFail($id);

        $data = $request->validate([
            'flag_type'     => 'required|string',
            'severity'      => 'nullable|in:low,medium,high',
            'timestamp_sec' => 'nullable|integer',
            'notes'         => 'nullable|string',
        ]);

        $flag = ProctoringFlag::create([
            'proctoring_session_id' => $session->id,
            'flag_type'             => $data['flag_type'],
            'severity'              => $data['severity'] ?? 'low',
            'timestamp_sec'         => $data['timestamp_sec'] ?? null,
            'notes'                 => $data['notes'] ?? null,
            'created_by'            => $request->user()->id,
        ]);

        $session->increment('flags_count');

        return $this->success($flag, 'Flag added', 201);
    }

    public function sendMessage(Request $request, $id)
    {
        $session = ProctoringSession::findOrFail($id);

        $data = $request->validate([
            'sender_type' => 'required|in:proctor,candidate,system',
            'message'     => 'required|string',
        ]);

        $msg = ProctoringMessage::create([
            'proctoring_session_id' => $session->id,
            'sender_type'           => $data['sender_type'],
            'sender_id'             => $request->user()->id,
            'message'               => $data['message'],
        ]);

        return $this->success($msg, 'Message sent', 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $session = ProctoringSession::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:live,completed,terminated,paused',
        ]);

        $session->status = $data['status'];
        if ($data['status'] === 'completed' || $data['status'] === 'terminated') {
            $session->ended_at = now();
        }
        $session->save();

        return $this->success($session, 'Status updated');
    }
}
