<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\TestAttemptItem;

class GradingController extends ApiController
{
    public function queue()
    {
        $items = TestAttemptItem::where('requires_manual_grading', 1)
            ->whereNull('graded_at')
            ->paginate(20);

        return $this->success($items);
    }

    public function show($id)
    {
        $item = TestAttemptItem::findOrFail($id);
        return $this->success($item);
    }

    public function grade(Request $request, $id)
    {
        $item = TestAttemptItem::findOrFail($id);

        $data = $request->validate([
            'final_score'    => 'required|numeric',
            'grader_comment' => 'nullable|string',
        ]);

        $item->final_score    = $data['final_score'];
        $item->grader_comment = $data['grader_comment'] ?? null;
        $item->graded_at      = now();
        $item->graded_by      = $request->user()->id;
        $item->save();

        return $this->success($item, 'Response graded');
    }
}
