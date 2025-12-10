<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials', 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user'  => $user,
        ], 'Logged in');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out');
    }

    public function me(Request $request)
    {
        return $this->success($request->user());
    }
}
