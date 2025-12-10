<?php

// namespace App\Http\Controllers\Api;

// use Illuminate\Http\Request;
// use App\Models\Test;
// use App\Models\TestSection;

// class TestSectionController extends ApiController
// {
//     public function store(Request $request, $testId)
//     {
//         $tenantId = $request->user()->tenant_id;

//         $test = Test::where('tenant_id', $tenantId)->findOrFail($testId);

//         $data = $request->validate([
//             'title'       => 'required|string',
//             'description' => 'nullable|string',
//             'duration'    => 'nullable|integer',
//             'weightage'   => 'nullable|integer',
//             'shuffle_items'=> 'boolean',
//         ]);

//         $section = $test->sections()->create($data);

//         return $this->success($section, 'Section created', 201);
//     }

//     public function update(Request $request, $id)
//     {
//         $section = TestSection::findOrFail($id);

//         $section->update($request->only([
//             'title','description','duration','weightage','shuffle_items','sort_order'
//         ]));

//         return $this->success($section, 'Section updated');
//     }

//     public function destroy($id)
//     {
//         $section = TestSection::findOrFail($id);
//         $section->delete();

//         return $this->success(null, 'Section deleted');
//     }
// }

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\TestSection;

class TestSectionController extends Controller
{
    // POST /api/tests/{testId}/sections
    public function store(Request $request, $testId)
    {
        $tenantId = $request->user()->tenant_id;
        $test = Test::where('tenant_id', $tenantId)->findOrFail($testId);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
            'weightage' => 'nullable|integer',
            'shuffle_items' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $section = $test->sections()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'duration' => $data['duration'] ?? null,
            'weightage' => $data['weightage'] ?? null,
            'shuffle_items' => $data['shuffle_items'] ? 1 : 0,
            'sort_order' => $data['sort_order'] ?? ($test->sections()->count()+1),
        ]);

        return response()->json(['status' => true, 'data' => $section], 201);
    }

    // PUT /api/sections/{id}
    public function update(Request $request, $id)
    {
        $section = TestSection::findOrFail($id);
        // ensure ownership by tenant
        $tenantId = $request->user()->tenant_id;
        if ($section->test->tenant_id !== $tenantId) abort(403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
            'weightage' => 'nullable|integer',
            'shuffle_items' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $section->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'duration' => $data['duration'] ?? null,
            'weightage' => $data['weightage'] ?? null,
            'shuffle_items' => $data['shuffle_items'] ? 1 : 0,
            'sort_order' => $data['sort_order'] ?? $section->sort_order,
        ]);

        return response()->json(['status' => true, 'data' => $section]);
    }

    // DELETE /api/sections/{id}
    public function destroy(Request $request, $id)
    {
        $section = TestSection::findOrFail($id);
        $tenantId = $request->user()->tenant_id;
        if ($section->test->tenant_id !== $tenantId) abort(403);

        $section->delete();
        return response()->json(['status' => true, 'message' => 'Section removed']);
    }
}
