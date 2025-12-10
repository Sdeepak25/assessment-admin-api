<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\NotificationTemplate;

class NotificationTemplateController extends ApiController
{
    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $templates = NotificationTemplate::where('tenant_id', $tenantId)->get();

        return $this->success($templates);
    }

    public function store(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $data = $request->validate([
            'name'    => 'required|string',
            'channel' => 'required|in:email,sms',
            'subject' => 'nullable|string',
            'body'    => 'required|string',
            'enabled' => 'boolean',
        ]);

        $tpl = NotificationTemplate::create(array_merge($data, [
            'tenant_id' => $tenantId,
        ]));

        return $this->success($tpl, 'Template created', 201);
    }

    public function show(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $tpl = NotificationTemplate::where('tenant_id', $tenantId)->findOrFail($id);

        return $this->success($tpl);
    }

    public function update(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $tpl = NotificationTemplate::where('tenant_id', $tenantId)->findOrFail($id);

        $tpl->update($request->only(['name','channel','subject','body','enabled']));

        return $this->success($tpl, 'Template updated');
    }

    public function destroy(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $tpl = NotificationTemplate::where('tenant_id', $tenantId)->findOrFail($id);
        $tpl->delete();

        return $this->success(null, 'Template deleted');
    }
}
