<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $permissions = Permission::all();
        
        $superAdmin = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Full access to everything - cannot be modified',
            'is_super' => true,
        ]);

        $superAdmin->permissions()->sync($permissions->pluck('id'));
    }
}
