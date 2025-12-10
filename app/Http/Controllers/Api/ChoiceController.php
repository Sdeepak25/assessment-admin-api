<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemChoice;

class ChoiceController extends ApiController
{
    public function indexByItem($itemId)
    {
        $choices = ItemChoice::where('item_id', $itemId)->orderBy('sort_order')->get();
        return $this->success($choices);
    }

    public function store(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);

        $data = $request->validate([
            'label'      => 'nullable|string|max:10',
            'text'       => 'required|string',
            'is_correct' => 'boolean',
            'score'      => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
        ]);

        $choice = $item->choices()->create($data);

        return $this->success($choice, 'Choice created', 201);
    }

    public function update(Request $request, $id)
    {
        $choice = ItemChoice::findOrFail($id);

        $choice->update($request->only(['label','text','is_correct','score','sort_order']));

        return $this->success($choice, 'Choice updated');
    }

    public function destroy($id)
    {
        $choice = ItemChoice::findOrFail($id);
        $choice->delete();

        return $this->success(null, 'Choice deleted');
    }
}
