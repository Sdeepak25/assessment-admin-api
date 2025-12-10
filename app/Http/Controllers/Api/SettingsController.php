<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\TenantSetting;

class SettingsController extends ApiController
{
    public function show(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $settings = TenantSetting::firstOrCreate(
            ['tenant_id' => $tenantId],
            []
        );

        return $this->success($settings);
    }

    public function update(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $settings = TenantSetting::firstOrCreate(
            ['tenant_id' => $tenantId],
            []
        );

        $settings->update($request->only([
            'password_policy',
            'smtp_config',
            'storage_config',
            'data_retention',
            'maintenance_mode',
        ]));

        return $this->success($settings, 'Settings updated');
    }
}
