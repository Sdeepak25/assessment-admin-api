<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends ApiController
{
    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $items = Item::where('tenant_id', $tenantId)
            ->with('choices')
            ->paginate(20);

        return $this->success($items);
    }

    public function store(Request $request)
    {
        $tenantId = $request->user()->tenant_id;
        $userId   = $request->user()->id;

        $data = $request->validate([
            'stem'       => 'required|string',
            'type'       => 'required|in:single_choice,multiple_choice,text,numeric,likert,matrix',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'metadata'   => 'array',
        ]);

        $item = Item::create([
            'tenant_id'      => $tenantId,
            'stem'           => $data['stem'],
            'type'           => $data['type'],
            'difficulty'     => $data['difficulty'] ?? 'medium',
            'metadata'       => $data['metadata'] ?? [],
            'created_by'     => $userId,
            'updated_by'     => $userId,
        ]);

        return $this->success($item, 'Item created', 201);
    }

    public function show(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $item = Item::where('tenant_id', $tenantId)
            ->with('choices')
            ->findOrFail($id);

        return $this->success($item);
    }

    public function update(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;
        $userId   = $request->user()->id;

        $item = Item::where('tenant_id', $tenantId)->findOrFail($id);

        $item->update(array_merge(
            $request->only(['stem','type','difficulty']),
            ['metadata' => $request->input('metadata', $item->metadata)],
            ['updated_by' => $userId]
        ));

        return $this->success($item->fresh('choices'), 'Item updated');
    }

    public function destroy(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $item = Item::where('tenant_id', $tenantId)->findOrFail($id);
        $item->delete();

        return $this->success(null, 'Item deleted');
    }

    public function bulkImport(Request $request)
    {
        // For now, just a stub. You can implement CSV parsing here.
        return $this->success(null, 'Bulk import not implemented yet');
    }
}
