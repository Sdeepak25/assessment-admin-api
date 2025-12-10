<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Candidate;

class CandidateController extends ApiController
{
    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $query = Candidate::where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $candidates = $query->orderBy('created_at', 'desc')->paginate(20);

        return $this->success($candidates);
    }

    public function store(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'external_id' => 'nullable|string|max:100',
            'status'      => 'nullable|in:invited,active,completed,archived',
        ]);

        // unique email per tenant
        $exists = Candidate::where('tenant_id', $tenantId)
            ->where('email', $data['email'])
            ->exists();

        if ($exists) {
            return $this->error('Candidate email already exists in this tenant', 422);
        }

        $candidate = Candidate::create(array_merge($data, [
            'tenant_id' => $tenantId,
        ]));

        return $this->success($candidate, 'Candidate created', 201);
    }

    public function show(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $candidate = Candidate::where('tenant_id', $tenantId)
            ->findOrFail($id);

        return $this->success($candidate);
    }

    public function update(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $candidate = Candidate::where('tenant_id', $tenantId)
            ->findOrFail($id);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'email'       => 'sometimes|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'external_id' => 'nullable|string|max:100',
            'status'      => 'nullable|in:invited,active,completed,archived',
        ]);

        if (isset($data['email']) && $data['email'] !== $candidate->email) {
            $exists = Candidate::where('tenant_id', $tenantId)
                ->where('email', $data['email'])
                ->where('id', '<>', $candidate->id)
                ->exists();

            if ($exists) {
                return $this->error('Candidate email already exists in this tenant', 422);
            }
        }

        $candidate->update($data);

        return $this->success($candidate, 'Candidate updated');
    }

    public function destroy(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $candidate = Candidate::where('tenant_id', $tenantId)
            ->findOrFail($id);

        $candidate->delete();

        return $this->success(null, 'Candidate deleted');
    }
}
