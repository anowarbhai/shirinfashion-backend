<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'is_admin',
        'nid',
        'date_of_birth',
        'join_date',
        'address',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'phone' => 'string',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }
        return $this->roles->contains($role);
    }

    public function hasPermission($permission)
    {
        // Check if user is admin (backward compatibility)
        if ($this->is_admin) {
            return true;
        }

        // Load roles with permissions if not loaded
        if (!$this->relationLoaded('roles')) {
            $this->load('roles.permissions');
        }

        // Check if user is super admin
        foreach ($this->roles as $role) {
            if ($role->is_super) {
                return true;
            }
        }

        // Check if user has the permission through any role
        foreach ($this->roles as $role) {
            if ($role->relationLoaded('permissions')) {
                foreach ($role->permissions as $perm) {
                    if ($perm->slug === $permission || $perm->name === $permission) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function isSuperAdmin()
    {
        if ($this->is_admin) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->is_super) {
                return true;
            }
        }
        return false;
    }
}
