<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;

trait HasPermissions
{
    /**
     * Boot the trait
     */
    public static function bootHasPermissions()
    {
        // Load roles with permissions on every query
        static::addGlobalScope('roles', function ($builder) {
            $builder->with('roles.permissions');
        });
    }

    /**
     * Get all permissions for the user
     */
    public function getAllPermissions(): \Illuminate\Database\Eloquent\Collection
    {
        $permissions = collect();

        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        return $permissions->unique('id')->values();
    }

    /**
     * Check if user has a specific permission (by slug)
     */
    public function hasPermission(string $permission): bool
    {
        // Super admin (is_super role) has all permissions
        foreach ($this->roles as $role) {
            if ($role->is_super) {
                return true;
            }
        }

        // Check if user has the specific permission
        return $this->getAllPermissions()->contains('slug', $permission);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has any role
     */
    public function hasAnyRole(): bool
    {
        return $this->roles()->exists();
    }

    /**
     * Check if user is super admin (via is_super role)
     */
    public function isSuperAdmin(): bool
    {
        foreach ($this->roles as $role) {
            if ($role->is_super) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can access admin panel (is_admin flag OR has roles)
     */
    public function canAccessAdmin(): bool
    {
        return $this->is_admin || $this->hasAnyRole();
    }
}
