<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder {
    public function run() {
        $admin = Role::firstOrCreate(['name'=>'Admin','tenant_id'=>1], ['description'=>'Platform admin','is_system'=>1]);
        $manager = Role::firstOrCreate(['name'=>'Manager','tenant_id'=>1], ['description'=>'Manager','is_system'=>0]);
        $viewer = Role::firstOrCreate(['name'=>'Viewer','tenant_id'=>1], ['description'=>'Viewer','is_system'=>0]);

        $all = Permission::pluck('id')->toArray();
        if ($all) {
            $admin->permissions()->sync($all);
        }

        $managerPerms = Permission::whereIn('module', ['tests','items','users'])->pluck('id')->toArray();
        $manager->permissions()->sync($managerPerms);
    }
}
