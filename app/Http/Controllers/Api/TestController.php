<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Test;
use App\Models\TestSection;
use App\Models\TestSectionItem;
use App\Models\Item;
use App\Models\ItemChoice;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Exception;

class TestController extends Controller
{
    // List tests (minimal)
    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id;
        $tests = Test::where('tenant_id', $tenantId)->get();
        return response()->json(['status' => 'ok', 'data' => $tests]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['nullable', Rule::in(['job', 'psychometric'])],
            'status' => ['nullable', Rule::in(['draft','published','archived'])],
            'sections' => 'nullable|array',
            'sections.*.title' => 'required_with:sections|string',
            'sections.*.duration' => 'nullable|integer',
            'sections.*.weightage' => 'nullable|numeric',
            'sections.*.shuffle' => 'nullable|boolean',
            'sections.*.questions' => 'nullable|array',
            'sections.*.questions.*.id' => 'nullable|integer',
            'sections.*.questions.*.type' => 'required_with:sections.*.questions|string',
            'sections.*.questions.*.stem' => 'required_with:sections.*.questions|string',
            'sections.*.questions.*.choices' => 'nullable|array',
            'sections.*.questions.*.choices.*.id' => 'nullable|integer',
            'sections.*.questions.*.choices.*.text' => 'required_with:sections.*.questions.*.choices|string',
            'sections.*.questions.*.choices.*.isCorrect' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $test = Test::create([
                'tenant_id' => $user->tenant_id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'] ?? 'job',
                'status' => $data['status'] ?? 'draft',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'last_modified' => now(),
            ]);

            if (!empty($data['sections'])) {
                foreach ($data['sections'] as $sIndex => $s) {
                    $section = $test->sections()->create([
                        'title' => $s['title'],
                        'duration' => $s['duration'] ?? null,
                        'weightage' => $s['weightage'] ?? null,
                        'shuffle_items' => !empty($s['shuffle']) ? 1 : 0,
                        'sort_order' => $sIndex + 1,
                    ]);

                    // questions
                    if (!empty($s['questions'])) {
                        foreach ($s['questions'] as $qIndex => $q) {
                            // create or update item
                            if (!empty($q['id'])) {
                                $item = Item::where('id', $q['id'])->where('tenant_id', $user->tenant_id)->first();
                                if ($item) {
                                    $item->update([
                                        'stem' => $q['stem'],
                                        'type' => $this->mapQuestionType($q['type'] ?? $item->type),
                                        'difficulty' => $this->mapDifficulty($q['difficulty'] ?? $item->difficulty),
                                        'default_weight' => $q['weight'] ?? 1.0,
                                        'metadata' => $q['metadata'] ?? $item->metadata,
                                        'updated_by' => $user->id,
                                    ]);
                                } else {
                                    $item = Item::create([
                                        'tenant_id' => $user->tenant_id,
                                        'stem' => $q['stem'],
                                        'type' => $this->mapQuestionType($q['type'] ?? 'single'),
                                        'difficulty' => $this->mapDifficulty($q['difficulty'] ?? null) ?? 'medium',
                                        'default_weight' => $q['weight'] ?? 1.0,
                                        'created_by' => $user->id,
                                        'updated_by' => $user->id,
                                    ]);
                                }
                            } else {
                                $item = Item::create([
                                    'tenant_id' => $user->tenant_id,
                                    'stem' => $q['stem'],
                                    'type' => $this->mapQuestionType($q['type'] ?? 'single'),
                                    'difficulty' => $this->mapDifficulty($q['difficulty'] ?? null) ?? 'medium',
                                    'default_weight' => $q['weight'] ?? 1.0,
                                    'created_by' => $user->id,
                                    'updated_by' => $user->id,
                                ]);
                            }

                            // choices (delete & recreate for simplicity)
                            if (isset($q['choices']) && is_array($q['choices'])) {
                                ItemChoice::where('item_id', $item->id)->delete();
                                foreach ($q['choices'] as $choiceIndex => $c) {
                                    ItemChoice::create([
                                        'item_id' => $item->id,
                                        'label' => $c['label'] ?? null,
                                        'text' => $c['text'],
                                        'is_correct' => !empty($c['isCorrect']) ? 1 : 0,
                                        'score' => $c['score'] ?? null,
                                        'sort_order' => $choiceIndex + 1,
                                    ]);
                                }
                            }

                            // create test_section_items mapping
                            TestSectionItem::create([
                                'test_section_id' => $section->id,
                                'item_id' => $item->id,
                                'weight' => $q['weight'] ?? null,
                                'negative_marking' => $q['negative_marking'] ?? null,
                                'partial_scoring_rule' => $q['partial_scoring_rule'] ?? null,
                                'sort_order' => $qIndex + 1,
                                'requires_manual_grading' => !empty($q['requires_manual_grading']) ? 1 : 0,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            // return nested resource
            return response()->json(['status' => 'ok', 'data' => $this->loadTestResource($test)], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Create failed: '.$e->getMessage()], 500);
        }
    }


    // Show test with nested sections -> questions -> choices
    public function show(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $test = Test::where('tenant_id', $tenantId)->find($id);
        if (!$test) {
            return response()->json(['status' => 'error', 'message' => 'Test not found'], 404);
        }

        return response()->json(['status' => 'ok', 'data' => $this->loadTestResource($test)]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['nullable', Rule::in(['job', 'psychometric'])],
            'status' => ['nullable', Rule::in(['draft','published','archived'])],
            'sections' => 'nullable|array',
            'sections.*.id' => 'nullable|integer',
            'sections.*.title' => 'required_with:sections|string',
            'sections.*.duration' => 'nullable|integer',
            'sections.*.weightage' => 'nullable|numeric',
            'sections.*.shuffle' => 'nullable|boolean',
            'sections.*.questions' => 'nullable|array',
            'sections.*.questions.*.id' => 'nullable|integer',
            'sections.*.questions.*.type' => 'required_with:sections.*.questions|string',
            'sections.*.questions.*.stem' => 'required_with:sections.*.questions|string',
            'sections.*.questions.*.choices' => 'nullable|array',
            'sections.*.questions.*.choices.*.id' => 'nullable|integer',
            'sections.*.questions.*.choices.*.text' => 'required_with:sections.*.questions.*.choices|string',
        ]);

        $test = Test::where('id', $id)->where('tenant_id', $user->tenant_id)->firstOrFail();

        DB::beginTransaction();
        try {
            $test->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? $test->description,
                'type' => $data['type'] ?? $test->type,
                'status' => $data['status'] ?? $test->status,
                'updated_by' => $user->id,
                'last_modified' => now(),
            ]);

            // If sections provided, upsert sections and their questions
            if (!empty($data['sections'])) {
                $incomingSectionIds = [];

                foreach ($data['sections'] as $sIndex => $s) {
                    // Upsert section
                    if (!empty($s['id'])) {
                        $section = TestSection::where('id', $s['id'])->where('test_id', $test->id)->first();
                        if ($section) {
                            $section->update([
                                'title' => $s['title'],
                                'duration' => $s['duration'] ?? null,
                                'weightage' => $s['weightage'] ?? null,
                                'shuffle_items' => !empty($s['shuffle']) ? 1 : 0,
                                'sort_order' => $sIndex + 1,
                            ]);
                        } else {
                            $section = $test->sections()->create([
                                'title' => $s['title'],
                                'duration' => $s['duration'] ?? null,
                                'weightage' => $s['weightage'] ?? null,
                                'shuffle_items' => !empty($s['shuffle']) ? 1 : 0,
                                'sort_order' => $sIndex + 1,
                            ]);
                        }
                    } else {
                        $section = $test->sections()->create([
                            'title' => $s['title'],
                            'duration' => $s['duration'] ?? null,
                            'weightage' => $s['weightage'] ?? null,
                            'shuffle_items' => !empty($s['shuffle']) ? 1 : 0,
                            'sort_order' => $sIndex + 1,
                        ]);
                    }

                    $incomingSectionIds[] = $section->id;

                    // Handle questions
                    $incomingTsiIds = [];
                    if (!empty($s['questions'])) {
                        foreach ($s['questions'] as $qIndex => $q) {
                            // Upsert item
                            if (!empty($q['id'])) {
                                $item = Item::where('id', $q['id'])->where('tenant_id', $user->tenant_id)->first();
                                if ($item) {
                                    $item->update([
                                        'stem' => $q['stem'],
                                        'type' => $this->mapQuestionType($q['type'] ?? $item->type),
                                        'difficulty' => $this->mapDifficulty($q['difficulty'] ?? $item->difficulty),
                                        'default_weight' => $q['weight'] ?? 1.0,
                                        'metadata' => $q['metadata'] ?? $item->metadata,
                                        'updated_by' => $user->id,
                                    ]);
                                } else {
                                    $item = Item::create([
                                        'tenant_id' => $user->tenant_id,
                                        'stem' => $q['stem'],
                                        'type' => $this->mapQuestionType($q['type'] ?? 'single'),
                                        'difficulty' => $this->mapDifficulty($q['difficulty'] ?? null) ?? 'medium',
                                        'default_weight' => $q['weight'] ?? 1.0,
                                        'created_by' => $user->id,
                                        'updated_by' => $user->id,
                                    ]);
                                }
                            } else {
                                $item = Item::create([
                                    'tenant_id' => $user->tenant_id,
                                    'stem' => $q['stem'],
                                    'type' => $this->mapQuestionType($q['type'] ?? 'single'),
                                    'difficulty' => $this->mapDifficulty($q['difficulty'] ?? null) ?? 'medium',
                                    'default_weight' => $q['weight'] ?? 1.0,
                                    'created_by' => $user->id,
                                    'updated_by' => $user->id,
                                ]);
                            }

                            // choices: delete existing and recreate (simpler)
                            if (isset($q['choices']) && is_array($q['choices'])) {
                                ItemChoice::where('item_id', $item->id)->delete();
                                foreach ($q['choices'] as $choiceIndex => $c) {
                                    ItemChoice::create([
                                        'item_id' => $item->id,
                                        'label' => $c['label'] ?? null,
                                        'text' => $c['text'],
                                        'is_correct' => !empty($c['isCorrect']) ? 1 : 0,
                                        'score' => $c['score'] ?? null,
                                        'sort_order' => $choiceIndex + 1,
                                    ]);
                                }
                            }

                            // Upsert test_section_item mapping
                            $tsi = TestSectionItem::where('test_section_id', $section->id)
                                ->where('item_id', $item->id)
                                ->first();

                            if ($tsi) {
                                $tsi->update([
                                    'weight' => $q['weight'] ?? $tsi->weight,
                                    'negative_marking' => $q['negative_marking'] ?? $tsi->negative_marking,
                                    'partial_scoring_rule' => $q['partial_scoring_rule'] ?? $tsi->partial_scoring_rule,
                                    'sort_order' => $qIndex + 1,
                                    'requires_manual_grading' => !empty($q['requires_manual_grading']) ? 1 : 0,
                                ]);
                            } else {
                                $tsi = TestSectionItem::create([
                                    'test_section_id' => $section->id,
                                    'item_id' => $item->id,
                                    'weight' => $q['weight'] ?? null,
                                    'negative_marking' => $q['negative_marking'] ?? null,
                                    'partial_scoring_rule' => $q['partial_scoring_rule'] ?? null,
                                    'sort_order' => $qIndex + 1,
                                    'requires_manual_grading' => !empty($q['requires_manual_grading']) ? 1 : 0,
                                ]);
                            }

                            $incomingTsiIds[] = $tsi->id;
                        } // end questions
                    }

                    // Remove TSIs that are not in incomingTsiIds for this section (optional)
                    if (!empty($incomingTsiIds)) {
                        TestSectionItem::where('test_section_id', $section->id)
                            ->whereNotIn('id', $incomingTsiIds)
                            ->delete();
                    }
                } // end sections loop

                // Optionally delete sections removed on frontend
                TestSection::where('test_id', $test->id)
                    ->whereNotIn('id', $incomingSectionIds)
                    ->delete();
            }

            DB::commit();

            return response()->json(['status' => 'ok', 'data' => $this->loadTestResource($test), 'message' => 'Test updated']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Update failed: '.$e->getMessage()], 500);
        }
    }

    // Publish endpoint
    public function publish(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;
        $test = Test::where('tenant_id', $tenantId)->find($id);
        if (!$test) {
            return response()->json(['status' => 'error', 'message' => 'Test not found'], 404);
        }
        $test->status = 'published';
        $test->updated_by = $request->user()->id ?? $test->updated_by;
        $test->last_modified = Carbon::now();
        $test->save();

        return response()->json(['status' => 'ok', 'message' => 'Published', 'data' => $this->loadTestResource($test)]);
    }

    protected function syncQuestionsForSection(TestSection $section, array $questions, $tenantId, $userId = null)
    {
        // collect kept test_section_item ids to cleanup removed ones
        $keptTsiIds = [];

        foreach ($questions as $qIndex => $qPayload) {
            // If question already exists (we expect incoming 'id' to be the Item id or TSI id).
            $incomingItemId = isset($qPayload['id']) && is_numeric($qPayload['id']) ? intval($qPayload['id']) : null;

            if ($incomingItemId) {
                // find test_section_item for this section & item
                $tsi = TestSectionItem::where('test_section_id', $section->id)
                    ->where('item_id', $incomingItemId)
                    ->first();
                if ($tsi) {
                    // update Item
                    $item = Item::find($incomingItemId);
                    if ($item) {
                        $item->update([
                            'stem' => $qPayload['stem'] ?? $item->stem,
                            'type' => $this->mapQuestionType($qPayload['type'] ?? $item->type),
                            'difficulty' => $this->mapDifficulty($qPayload['difficulty'] ?? $item->difficulty),
                            'updated_by' => $userId ?? $item->updated_by,
                        ]);
                    }

                    // update tsi meta
                    $tsi->update([
                        'weight' => $qPayload['weight'] ?? $tsi->weight,
                        'negative_marking' => $qPayload['negative_marking'] ?? $tsi->negative_marking,
                        'partial_scoring_rule' => $qPayload['partial_scoring_rule'] ?? $tsi->partial_scoring_rule,
                        'sort_order' => $qIndex + 1,
                    ]);

                    $keptTsiIds[] = $tsi->id;

                    // sync choices for this item
                    if (!empty($qPayload['choices']) && is_array($qPayload['choices'])) {
                        $this->syncChoicesForItem($item, $qPayload['choices']);
                    }
                    continue;
                }
            }

            // If we reached here, either there's no incoming numeric id or no matching tsi — create new item & tsi
            $item = Item::create([
                'tenant_id' => $tenantId,
                'stem' => $qPayload['stem'] ?? '',
                'type' => $this->mapQuestionType($qPayload['type'] ?? 'single'),
                'difficulty' => $this->mapDifficulty($qPayload['difficulty'] ?? null),
                'created_by' => $userId ?? null,
                'updated_by' => $userId ?? null,
            ]);

            $tsi = TestSectionItem::create([
                'test_section_id' => $section->id,
                'item_id' => $item->id,
                'weight' => $qPayload['weight'] ?? null,
                'negative_marking' => $qPayload['negative_marking'] ?? null,
                'partial_scoring_rule' => $qPayload['partial_scoring_rule'] ?? null,
                'sort_order' => $qIndex + 1,
                'requires_manual_grading' => $qPayload['requires_manual_grading'] ?? 0,
            ]);

            $keptTsiIds[] = $tsi->id;

            if (!empty($qPayload['choices']) && is_array($qPayload['choices'])) {
                foreach ($qPayload['choices'] as $ci => $cPayload) {
                    ItemChoice::create([
                        'item_id' => $item->id,
                        'label' => $this->choiceLabelFromIndex($ci),
                        'text' => $cPayload['text'] ?? '',
                        'is_correct' => !empty($cPayload['isCorrect']) ? 1 : 0,
                        'score' => isset($cPayload['score']) ? $cPayload['score'] : null,
                        'sort_order' => $ci + 1,
                    ]);
                }
            }
        }

        // Delete TSI entries not present in incoming payload (and cascade will remove item_choices / item? depending on your logic)
        $existingTsi = TestSectionItem::where('test_section_id', $section->id)->get();
        foreach ($existingTsi as $e) {
            if (!in_array($e->id, $keptTsiIds)) {
                // Optionally delete the item as well if not referenced elsewhere.
                $item = Item::find($e->item_id);
                $e->delete();
                if ($item) {
                    // If item is orphan now, you may delete it. Here we delete choices and item to keep DB clean.
                    ItemChoice::where('item_id', $item->id)->delete();
                    $item->delete();
                }
            }
        }
    }

    protected function syncChoicesForItem(Item $item, array $choices)
    {
        $keptChoiceIds = [];
        foreach ($choices as $idx => $c) {
            $incomingChoiceId = isset($c['id']) && is_numeric($c['id']) ? intval($c['id']) : null;
            if ($incomingChoiceId) {
                $choice = ItemChoice::where('item_id', $item->id)->where('id', $incomingChoiceId)->first();
                if ($choice) {
                    $choice->update([
                        'text' => $c['text'] ?? $choice->text,
                        'is_correct' => !empty($c['isCorrect']) ? 1 : 0,
                        'score' => isset($c['score']) ? $c['score'] : $choice->score,
                        'sort_order' => $idx + 1,
                    ]);
                    $keptChoiceIds[] = $choice->id;
                    continue;
                }
            }
            // create new
            $new = ItemChoice::create([
                'item_id' => $item->id,
                'label' => $this->choiceLabelFromIndex($idx),
                'text' => $c['text'] ?? '',
                'is_correct' => !empty($c['isCorrect']) ? 1 : 0,
                'score' => isset($c['score']) ? $c['score'] : null,
                'sort_order' => $idx + 1,
            ]);
            $keptChoiceIds[] = $new->id;
        }

        // delete removed choices
        ItemChoice::where('item_id', $item->id)->whereNotIn('id', $keptChoiceIds)->delete();
    }

    // Helper to map ui types to DB enums
    protected function mapQuestionType($type)
    {
        if (!$type) return 'single_choice';
        $t = strtolower($type);
        if (in_array($t, ['single','single_choice'])) return 'single_choice';
        if (in_array($t, ['multiple','multiple_choice','multi'])) return 'multiple_choice';
        if ($t === 'text') return 'text';
        if ($t === 'numeric') return 'numeric';
        if ($t === 'likert') return 'likert';
        if ($t === 'matrix') return 'matrix';
        return $t;
    }

    // Map difficulty numeric or string to DB enum
    protected function mapDifficulty($val)
    {
        if ($val === null) return null;
        if (is_numeric($val)) {
            $n = intval($val);
            if ($n <= 1) return 'easy';
            if ($n <= 3) return 'medium';
            return 'hard';
        }
        $s = strtolower($val);
        if (in_array($s, ['easy','medium','hard'])) return $s;
        return null;
    }

    protected function choiceLabelFromIndex($idx)
    {
        return chr(65 + $idx); // 0 -> 'A', etc.
    }

    // Load resource with nested structure
    protected function loadTestResource(Test $test)
    {
        $test->load(['sections' => function($q){
            $q->orderBy('sort_order');
        }]);

        $result = [
            'id' => $test->id,
            'tenant_id' => $test->tenant_id,
            'name' => $test->name,
            'description' => $test->description,
            'type' => $test->type,
            'status' => $test->status,
            'last_modified' => $test->last_modified,
            'created_at' => $test->created_at,
            'updated_at' => $test->updated_at,
            'sections' => [],
        ];

        foreach ($test->sections as $section) {
            // load tsi -> item -> choices
            $tsiRows = TestSectionItem::where('test_section_id', $section->id)
                ->orderBy('sort_order')
                ->get();

            $questions = [];
            foreach ($tsiRows as $tsi) {
                $item = Item::find($tsi->item_id);
                if (!$item) continue;
                $choices = ItemChoice::where('item_id', $item->id)->orderBy('sort_order')->get();
                $questions[] = [
                    'id' => $item->id,
                    'type' => $item->type,
                    'stem' => $item->stem,
                    'difficulty' => $item->difficulty,
                    'weight' => $tsi->weight,
                    'negative_marking' => $tsi->negative_marking,
                    'partial_scoring_rule' => $tsi->partial_scoring_rule,
                    'requires_manual_grading' => (bool)$tsi->requires_manual_grading,
                    'choices' => $choices->map(function($c){
                        return [
                            'id' => $c->id,
                            'label' => $c->label,
                            'text' => $c->text,
                            'isCorrect' => (bool)$c->is_correct,
                            'score' => $c->score,
                        ];
                    })->toArray(),
                ];
            }

            $result['sections'][] = [
                'id' => $section->id,
                'title' => $section->title,
                'description' => $section->description,
                'duration' => $section->duration,
                'weightage' => $section->weightage,
                'shuffle_items' => (bool)$section->shuffle_items,
                'sort_order' => $section->sort_order,
                'questions' => $questions,
            ];
        }

        return $result;
    }
}
