<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\AnalyticsTestSummary;
use App\Models\TestAttempt;

class ReportController extends ApiController
{
    public function summary(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $query = AnalyticsTestSummary::where('tenant_id', $tenantId);

        if ($request->has('test_id')) {
            $query->where('test_id', $request->test_id);
        }

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('date', [$request->from, $request->to]);
        }

        return $this->success($query->get());
    }

    public function testDetail(Request $request, $testId)
    {
        $attempts = TestAttempt::whereHas('assignment', function ($q) use ($testId) {
                $q->where('test_id', $testId);
            })
            ->with('items')
            ->paginate(50);

        return $this->success($attempts);
    }

    public function export(Request $request)
    {
        // Stub for exporting
        return $this->success(null, 'Export not implemented yet');
    }
}
