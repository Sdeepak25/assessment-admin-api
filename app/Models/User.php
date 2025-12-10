<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';
    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'groups',
        'two_factor_enabled',
        'last_login_at',
        'is_super_admin',
    ];

    protected $casts = [
        'email_verified_at'  => 'datetime',
        'last_login_at'      => 'datetime',
        'two_factor_enabled' => 'boolean',
        'groups'             => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasRole(string $name): bool
    {
        return $this->roles()->where('name', $name)->exists();
    }
}
