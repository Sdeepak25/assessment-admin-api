<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Integration;

class IntegrationController extends ApiController
{
    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $integrations = Integration::where('tenant_id', $tenantId)->get();

        return $this->success($integrations);
    }

    public function store(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $data = $request->validate([
            'type'     => 'required|in:sso,hris,ats',
            'provider' => 'required|string',
            'config'   => 'array',
            'status'   => 'nullable|in:connected,disconnected,error',
        ]);

        $integration = Integration::create(array_merge($data, [
            'tenant_id' => $tenantId,
        ]));

        return $this->success($integration, 'Integration created', 201);
    }

    public function show(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $integration = Integration::where('tenant_id', $tenantId)->findOrFail($id);

        return $this->success($integration);
    }

    public function update(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $integration = Integration::where('tenant_id', $tenantId)->findOrFail($id);

        $integration->update($request->only(['config','status']));

        return $this->success($integration, 'Integration updated');
    }

    public function destroy(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $integration = Integration::where('tenant_id', $tenantId)->findOrFail($id);
        $integration->delete();

        return $this->success(null, 'Integration deleted');
    }
}
