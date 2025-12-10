<?php

// namespace App\Http\Controllers\Api;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use App\Models\User;
// use App\Models\Tenant;
// use App\Models\Role;

// class UserController extends ApiController
// {
//     public function index(Request $request)
//     {
//         $tenantId = $request->user()->tenant_id;

//         $users = User::where('tenant_id', $tenantId)
//             ->with('roles')
//             ->paginate(20);

//         return $this->success($users);
//     }

//     public function store(Request $request)
//     {
//         $tenantId = $request->user()->tenant_id;

//         $data = $request->validate([
//             'name'      => 'required|string',
//             'email'     => 'required|email|unique:users,email',
//             'phone'     => 'nullable|string',
//             'password'  => 'nullable|string|min:6',
//             'role_ids'  => 'array',
//             'role_ids.*'=> 'integer',
//         ]);

//         $user = User::create([
//             'tenant_id' => $tenantId,
//             'name'      => $data['name'],
//             'email'     => $data['email'],
//             'phone'     => $data['phone'] ?? null,
//             'password'  => Hash::make($data['password'] ?? 'ChangeMe123!'),
//             'status'    => 'invited',
//         ]);

//         if (!empty($data['role_ids'])) {
//             $user->roles()->sync($data['role_ids']);
//         }

//         return $this->success($user->load('roles'), 'User created', 201);
//     }

//     public function show(Request $request, $id)
//     {
//         $tenantId = $request->user()->tenant_id;

//         $user = User::where('tenant_id', $tenantId)
//             ->where('id', $id)
//             ->with('roles')
//             ->firstOrFail();

//         return $this->success($user);
//     }

//     public function update(Request $request, $id)
//     {
//         $tenantId = $request->user()->tenant_id;

//         $user = User::where('tenant_id', $tenantId)->findOrFail($id);

//         $data = $request->validate([
//             'name'      => 'sometimes|string',
//             'phone'     => 'nullable|string',
//             'status'    => 'nullable|in:active,inactive,invited',
//             'role_ids'  => 'array',
//             'role_ids.*'=> 'integer',
//         ]);

//         $user->update($request->only(['name','phone','status']));

//         if (isset($data['role_ids'])) {
//             $user->roles()->sync($data['role_ids']);
//         }

//         return $this->success($user->load('roles'), 'User updated');
//     }

//     public function destroy(Request $request, $id)
//     {
//         $tenantId = $request->user()->tenant_id;

//         $user = User::where('tenant_id', $tenantId)->findOrFail($id);
//         $user->delete();

//         return $this->success(null, 'User deleted');
//     }

//     public function resetPassword(Request $request, $id)
//     {
//         $tenantId = $request->user()->tenant_id;

//         $user = User::where('tenant_id', $tenantId)->findOrFail($id);

//         $data = $request->validate([
//             'password' => 'required|string|min:6'
//         ]);

//         $user->password = Hash::make($data['password']);
//         $user->save();

//         return $this->success(null, 'Password reset');
//     }
// }


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // List users (with filters)
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $users = $query->orderBy('name')->get();

        $data = $users->map(function (User $user) {
            return [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->role ?? 'Viewer',
                'status'    => $user->status ?? 'active',
                'lastLogin' => $user->last_login_at
                    ? $user->last_login_at->format('Y-m-d H:i')
                    : 'Never',
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Users list',
            'data'    => $data,
        ]);
    }

    // User detail for profile screen
    public function show(Request $request, User $user)
    {
        $this->authorizeTenant($request, $user);

        $data = [
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'phone'     => $user->phone,
            'roles'     => $user->role ? [$user->role] : [],
            'groups'    => $user->groups ?? [],
            'status'    => $user->status,
            'twoFactorEnabled' => (bool) $user->two_factor_enabled,
        ];

        return response()->json([
            'status'  => true,
            'message' => 'User detail',
            'data'    => $data,
        ]);
    }

    // Invite/create user
    public function store(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255|unique:users,email',
            'role'   => 'required|string|max:50',
            'status' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'tenant_id'         => $tenantId,
            'name'              => $data['name'],
            'email'             => $data['email'],
            'role'              => $data['role'],
            'status'            => $data['status'] ?? 'pending',
            'password'          => bcrypt(Str::random(16)), // temp password
            'two_factor_enabled'=> 0,
            'groups'            => [],
        ]);

        // TODO: send real invite email with reset link if you want
        return response()->json([
            'status'  => true,
            'message' => 'User invited',
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
                'status'=> $user->status,
            ],
        ], 201);
    }

    // Update user profile from admin UI
    public function update(Request $request, User $user)
    {
        $this->authorizeTenant($request, $user);

        $data = $request->validate([
            'name'   => 'sometimes|string|max:255',
            'email'  => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'phone'  => 'sometimes|nullable|string|max:50',
            'status' => 'sometimes|string|max:20',
            'roles'  => 'sometimes|array',
            'groups' => 'sometimes|array',
            'twoFactorEnabled' => 'sometimes|boolean',
        ]);

        if (isset($data['roles'])) {
            $user->role = $data['roles'][0] ?? null;
            unset($data['roles']);
        }

        if (isset($data['groups'])) {
            $user->groups = $data['groups'];
            unset($data['groups']);
        }

        if (array_key_exists('twoFactorEnabled', $data)) {
            $user->two_factor_enabled = $data['twoFactorEnabled'] ? 1 : 0;
            unset($data['twoFactorEnabled']);
        }

        $user->fill($data)->save();

        return response()->json([
            'status'  => true,
            'message' => 'User updated',
            'data'    => $user,
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorizeTenant($request, $user);

        $user->delete();

        return response()->json([
            'status'  => true,
            'message' => 'User deleted',
        ]);
    }

    public function sendResetPassword(Request $request, User $user)
    {
        $this->authorizeTenant($request, $user);

        Password::sendResetLink(['email' => $user->email]);

        return response()->json([
            'status'  => true,
            'message' => 'Password reset email sent',
        ]);
    }

    protected function authorizeTenant(Request $request, User $user)
    {
        if ($request->user()->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized');
        }
    }
}
