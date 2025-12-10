<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Test;
use App\Models\Candidate;

class TenantController extends ApiController
{
    /**
     * GET /api/tenants
     * Supports filters: ?search=...&status=active|suspended&plan=Starter|Professional|Enterprise
     */
    public function index(Request $request)
    {
        $query = Tenant::query()->withCount('users');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('domain', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($plan = $request->get('plan')) {
            $query->where('plan', $plan);
        }

        $tenants = $query
            ->orderBy('created_at', 'desc')
            ->get();

        // Shape for frontend TenantList
        $items = $tenants->map(function (Tenant $t) {
            return [
                'id'        => $t->id,
                'name'      => $t->name,
                'domain'    => $t->domain,
                'status'    => $t->status,
                'users'     => $t->users_count ?? 0,
                'plan'      => $t->plan ?? 'Enterprise',
                'createdOn' => optional($t->created_at)->toDateString(),
            ];
        });

        return $this->success($items, 'Tenants list');
    }

    /**
     * POST /api/tenants
     * Body: { name, subdomain, plan, adminEmail }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'subdomain'  => 'required|string|max:100|alpha_dash',
            'plan'       => 'nullable|string|max:100',
            'adminEmail' => 'nullable|email|max:255',
        ]);

        $domain = $data['subdomain'] . '.assesshub.com';

        $tenant = Tenant::create([
            'name'   => $data['name'],
            'domain' => $domain,
            'status' => 'active',
            'plan'   => $data['plan'] ?? 'Starter',
        ]);

        $adminUser = null;
        $generatedPassword = null;

        if (!empty($data['adminEmail'])) {
            $generatedPassword = str()->random(10);

            $adminUser = User::create([
                'tenant_id'      => $tenant->id,
                'name'           => 'Tenant Admin',
                'email'          => $data['adminEmail'],
                'password'       => bcrypt($generatedPassword),
                'phone'          => null,
                'is_super_admin' => 0,
                'status'         => 'active',
            ]);
        }

        return $this->success([
            'tenant'          => $tenant,
            'admin_user'      => $adminUser,
            'admin_password'  => $generatedPassword,
        ], 'Tenant created', 201);
    }

    /**
     * GET /api/tenants/{id}
     * Returns details + simple stats used in TenantDetail
     */
    public function show($id)
    {
        $tenant = Tenant::withCount('users')->findOrFail($id);

        $testsCount      = Test::where('tenant_id', $tenant->id)->count();
        $candidatesCount = Candidate::where('tenant_id', $tenant->id)->count();

        $data = [
            'id'        => $tenant->id,
            'name'      => $tenant->name,
            'domain'    => $tenant->domain,
            'status'    => $tenant->status,
            'plan'      => $tenant->plan,
            'createdOn' => optional($tenant->created_at)->toDateString(),

            'stats' => [
                'totalUsers'         => $tenant->users_count ?? 0,
                'testsCreated'       => $testsCount,
                'candidatesAssessed' => $candidatesCount,
            ],

            // branding fields
            'logo_url'       => $tenant->logo_url,
            'primary_color'  => $tenant->primary_color,
            'secondary_color'=> $tenant->secondary_color,

            // JSON fields (auto cast to array)
            'feature_flags'  => $tenant->feature_flags,
            'sso_config'     => $tenant->sso_config,
        ];

        return $this->success($data, 'Tenant detail');
    }

    /**
     * PUT /api/tenants/{id}
     * Body: { name?, domain?, status?, plan? }
     * (You can extend later with branding, feature flags, etc.)
     */
    public function update(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);

        $data = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'domain'         => 'sometimes|string|max:255',
            'status'         => 'sometimes|string|max:50',
            'plan'           => 'sometimes|string|max:100',

            'logo_url'       => 'sometimes|nullable|string|max:500',
            'primary_color'  => 'sometimes|nullable|string|max:20',
            'secondary_color'=> 'sometimes|nullable|string|max:20',

            'feature_flags'  => 'sometimes|array',
            'sso_config'     => 'sometimes|array',
        ]);

        $tenant->update($data);

        return $this->success($tenant, 'Tenant updated');
    }

    /**
     * DELETE /api/tenants/{id}
     */
    public function destroy($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();

        return $this->success(null, 'Tenant deleted');
    }
}
