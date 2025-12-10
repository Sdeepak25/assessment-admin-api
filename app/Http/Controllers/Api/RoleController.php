<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Validation\Rule;

class RoleController extends ApiController
{
    // GET /api/roles
    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id ?? null;

        $roles = Role::where(function ($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })
            ->with('permissions') // eager load permissions
            ->get();

        // Using ApiController->success wrapper (keeps your existing response shape)
        return $this->success($roles);
    }

    // POST /api/roles
    public function store(Request $request)
    {
        $tenantId = $request->user()->tenant_id ?? null;

        $data = $request->validate([
            'name'        => ['required','string','max:150',
                Rule::unique('roles')->where(function ($q) use ($tenantId) {
                    if ($tenantId) $q->where('tenant_id', $tenantId);
                })
            ],
            'description' => 'nullable|string|max:255',
        ]);

        $role = Role::create([
            'tenant_id'   => $tenantId,
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return $this->success($role, 'Role created', 201);
    }

    // GET /api/roles/{id}
    public function show(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id ?? null;

        $role = Role::where(function ($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })
            ->with('permissions')
            ->findOrFail($id);

        return $this->success($role);
    }

    // PUT /api/roles/{id}
    public function update(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id ?? null;

        $role = Role::where(function ($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })->findOrFail($id);

        $data = $request->validate([
            'name' => ['required','string','max:150',
                Rule::unique('roles')->ignore($role->id)->where(function($q) use ($role) {
                    if ($role->tenant_id) $q->where('tenant_id', $role->tenant_id);
                })
            ],
            'description' => 'nullable|string|max:255',
        ]);

        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return $this->success($role, 'Role updated');
    }

    // DELETE /api/roles/{id}
    public function destroy(Request $request, $id)
    {
        $tenantId = $request->user()->tenant_id ?? null;

        $role = Role::where(function ($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })->findOrFail($id);

        // detach relations safely
        if (method_exists($role, 'permissions')) {
            $role->permissions()->detach();
        }
        if (method_exists($role, 'users')) {
            $role->users()->detach();
        }

        $role->delete();

        return $this->success(null, 'Role deleted');
    }

    // PUT /api/roles/{id}/permissions   (replace entire permission set)
    public function updatePermissions(Request $request, $id)
    {
        $user = $request->user();
        $tenantId = $user->tenant_id;

        // find role in tenant scope or global
        $role = Role::where(function ($q) use ($tenantId) {
                    $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
                })
                ->findOrFail($id);

        $data = $request->validate([
            'permission_ids'   => 'array',
            'permission_ids.*' => 'integer|exists:permissions,id',
        ]);

        $ids = $data['permission_ids'] ?? [];

        // sync pivot - this will insert/delete rows in role_permissions table
        $role->permissions()->sync($ids);

        // optionally return full role + permissions
        $role->load('permissions');
        return response()->json([
            'status' => true,
            'message' => 'Permissions updated',
            'data' => $role
        ]);
    }

    // GET /api/permissions  (list all permissions so frontend can render checkboxes)
    public function listPermissions()
    {
        $perms = Permission::orderBy('module')->orderBy('action')->get();
        return $this->success($perms);
    }

    // POST /api/roles/{id}/permissions  (alternate route that does the same as PUT)
    public function assignPermissions(Request $request, $id)
    {
        // just reuse the updatePermissions behavior
        return $this->updatePermissions($request, $id);
    }
}
