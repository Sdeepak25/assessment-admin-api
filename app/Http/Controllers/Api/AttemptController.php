<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\TestAssignment;
use App\Models\TestAttempt;
use App\Models\TestAttemptItem;
use App\Models\TestSectionItem;
use App\Models\TestSection;

class AttemptController extends ApiController
{
    public function start(Request $request, $assignmentId)
    {
        $assignment = TestAssignment::with('attempts')->findOrFail($assignmentId);

        // simple logic: create new attempt_number
        $nextAttemptNumber = ($assignment->attempts->max('attempt_number') ?? 0) + 1;

        $attempt = TestAttempt::create([
            'test_assignment_id' => $assignment->id,
            'attempt_number'     => $nextAttemptNumber,
            'status'             => 'in_progress',
            'started_at'         => now(),
        ]);

        return $this->success($attempt, 'Attempt started', 201);
    }

    public function show($id)
    {
        $attempt = TestAttempt::with('items')->findOrFail($id);

        return $this->success($attempt);
    }

    public function submit(Request $request, $id)
    {
        $attempt = TestAttempt::with('items')->findOrFail($id);

        $data = $request->validate([
            'responses'   => 'array',
            'responses.*.test_attempt_item_id' => 'integer',
            'responses.*.response_text'        => 'nullable|string',
            'responses.*.response_choice_ids'  => 'array',
            'responses.*.response_numeric'     => 'nullable|numeric',
        ]);

        foreach ($data['responses'] ?? [] as $resp) {
            $item = TestAttemptItem::where('test_attempt_id', $attempt->id)
                ->find($resp['test_attempt_item_id']);

            if (! $item) {
                continue;
            }

            $item->response_text       = $resp['response_text'] ?? $item->response_text;
            $item->response_choice_ids = $resp['response_choice_ids'] ?? $item->response_choice_ids;
            $item->response_numeric    = $resp['response_numeric'] ?? $item->response_numeric;
            $item->save();
        }

        $attempt->status       = 'completed';
        $attempt->completed_at = now();
        $attempt->save();

        return $this->success($attempt->fresh('items'), 'Attempt submitted');
    }
}
