<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\TestAssignment;
use App\Models\Test;
use App\Models\Candidate;

class AssignmentController extends ApiController
{
    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $assignments = TestAssignment::where('tenant_id', $tenantId)
            ->with(['attempts', 'candidate'])
            ->paginate(20);

        return $this->success($assignments);
    }

    public function store(Request $request)
    {
        $tenantId = $request->user()->tenant_id;
        $userId   = $request->user()->id;

        $data = $request->validate([
            'test_id'          => 'required|integer|exists:tests,id',
            'candidate_id'     => 'required|integer|exists:candidates,id',
            'candidate_group_id'=> 'nullable|integer',
            'due_at'           => 'nullable|date',
        ]);

        // Ensure test and candidate belong to tenant
        Test::where('tenant_id', $tenantId)->findOrFail($data['test_id']);
        Candidate::where('tenant_id', $tenantId)->findOrFail($data['candidate_id']);

        $assignment = TestAssignment::create([
            'tenant_id'          => $tenantId,
            'test_id'            => $data['test_id'],
            'candidate_id'       => $data['candidate_id'],
            'candidate_group_id' => $data['candidate_group_id'] ?? null,
            'assigned_by'        => $userId,
            'due_at'             => $data['due_at'] ?? null,
            'status'             => 'assigned',
        ]);

        return $this->success($assignment, 'Assignment created', 201);
    }

    public function show(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $assignment = TestAssignment::where('tenant_id', $tenantId)
            ->with(['attempts', 'candidate'])
            ->findOrFail($id);

        return $this->success($assignment);
    }
}
