<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\PsychometricModel;
use App\Models\PsychometricScale;
use App\Models\PsychometricNormGroup;
use App\Models\PsychometricItem;

class PsychometricModelController extends ApiController
{
    // ----------------- MODELS -----------------

    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $models = PsychometricModel::where('tenant_id', $tenantId)
            ->withCount('scales')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return $this->success($models);
    }

    public function store(Request $request)
    {
        $tenantId = $request->user()->tenant_id;
        $userId   = $request->user()->id;

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:draft,active,archived',
        ]);

        $model = PsychometricModel::create([
            'tenant_id'   => $tenantId,
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'] ?? 'draft',
            'created_by'  => $userId,
            'updated_by'  => $userId,
        ]);

        return $this->success($model, 'Psychometric model created', 201);
    }

    public function show(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $model = PsychometricModel::where('tenant_id', $tenantId)
            ->with(['scales.children', 'normGroups'])
            ->findOrFail($id);

        return $this->success($model);
    }

    public function update(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;
        $userId   = $request->user()->id;

        $model = PsychometricModel::where('tenant_id', $tenantId)->findOrFail($id);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:draft,active,archived',
        ]);

        $model->update(array_merge(
            $data,
            ['updated_by' => $userId]
        ));

        return $this->success($model, 'Psychometric model updated');
    }

    public function destroy(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id;

        $model = PsychometricModel::where('tenant_id', $tenantId)->findOrFail($id);

        $model->delete();

        return $this->success(null, 'Psychometric model deleted');
    }

    // ----------------- SCALES -----------------

    public function addScale(Request $request, $modelId)
    {
        $tenantId = $request->user()->tenant_id;

        $model = PsychometricModel::where('tenant_id', $tenantId)->findOrFail($modelId);

        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'nullable|string|max:50',
            'description'     => 'nullable|string',
            'parent_scale_id' => 'nullable|integer|exists:psychometric_scales,id',
            'sort_order'      => 'nullable|integer',
        ]);

        if (!empty($data['parent_scale_id'])) {
            // Ensure parent belongs to same model
            $parent = PsychometricScale::where('model_id', $model->id)
                ->where('id', $data['parent_scale_id'])
                ->firstOrFail();
        }

        $scale = PsychometricScale::create([
            'model_id'        => $model->id,
            'name'            => $data['name'],
            'code'            => $data['code'] ?? null,
            'description'     => $data['description'] ?? null,
            'parent_scale_id' => $data['parent_scale_id'] ?? null,
            'sort_order'      => $data['sort_order'] ?? 1,
        ]);

        return $this->success($scale, 'Scale created', 201);
    }

    public function updateScale(Request $request, $scaleId)
    {
        $scale = PsychometricScale::findOrFail($scaleId);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'code'        => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
        ]);

        $scale->update($data);

        return $this->success($scale, 'Scale updated');
    }

    public function deleteScale($scaleId)
    {
        $scale = PsychometricScale::findOrFail($scaleId);
        $scale->delete();

        return $this->success(null, 'Scale deleted');
    }

    // ----------------- NORM GROUPS -----------------

    public function addNormGroup(Request $request, $modelId)
    {
        $tenantId = $request->user()->tenant_id;

        $model = PsychometricModel::where('tenant_id', $tenantId)->findOrFail($modelId);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'population'  => 'nullable|string|max:255',
            'sample_size' => 'nullable|integer',
            'stats'       => 'array',
        ]);

        $norm = PsychometricNormGroup::create([
            'model_id'    => $model->id,
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'population'  => $data['population'] ?? null,
            'sample_size' => $data['sample_size'] ?? null,
            'stats'       => $data['stats'] ?? null,
        ]);

        return $this->success($norm, 'Norm group created', 201);
    }

    public function updateNormGroup(Request $request, $normId)
    {
        $norm = PsychometricNormGroup::findOrFail($normId);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'population'  => 'nullable|string|max:255',
            'sample_size' => 'nullable|integer',
            'stats'       => 'array',
        ]);

        $norm->update($data);

        return $this->success($norm, 'Norm group updated');
    }

    public function deleteNormGroup($normId)
    {
        $norm = PsychometricNormGroup::findOrFail($normId);
        $norm->delete();

        return $this->success(null, 'Norm group deleted');
    }

    // ----------------- ITEMS ASSIGNED TO SCALES -----------------

    public function assignItemToScale(Request $request, $scaleId)
    {
        $scale = PsychometricScale::findOrFail($scaleId);

        $data = $request->validate([
            'item_id'        => 'required|integer|exists:items,id',
            'reverse_scored' => 'boolean',
            'weight'         => 'nullable|numeric',
        ]);

        $pi = PsychometricItem::firstOrCreate(
            [
                'scale_id' => $scale->id,
                'item_id'  => $data['item_id'],
            ],
            [
                'reverse_scored' => $data['reverse_scored'] ?? 0,
                'weight'         => $data['weight'] ?? 1.0,
            ]
        );

        if (!$pi->wasRecentlyCreated) {
            $pi->reverse_scored = $data['reverse_scored'] ?? $pi->reverse_scored;
            $pi->weight         = $data['weight'] ?? $pi->weight;
            $pi->save();
        }

        return $this->success($pi, 'Item assigned to scale');
    }

    public function removeItemFromScale($scaleId, $itemId)
    {
        $pi = PsychometricItem::where('scale_id', $scaleId)
            ->where('item_id', $itemId)
            ->firstOrFail();

        $pi->delete();

        return $this->success(null, 'Item removed from scale');
    }
}
