<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Invoice;

class BillingController extends ApiController
{
    public function summary(Request $request)
    {
        $tenant = $request->user()->tenant;
        $subscription = Subscription::where('tenant_id', $tenant->id)
            ->with('plan')
            ->latest()
            ->first();

        return $this->success([
            'tenant'       => $tenant,
            'subscription' => $subscription,
        ]);
    }

    public function invoices(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $subsIds = Subscription::where('tenant_id', $tenantId)->pluck('id');

        $invoices = Invoice::whereIn('subscription_id', $subsIds)->orderByDesc('created_at')->get();

        return $this->success($invoices);
    }
}
