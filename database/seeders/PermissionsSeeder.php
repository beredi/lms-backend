<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $userPermissions = User::getModelPermissions();
        $permissions = array_merge($userPermissions);
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        Role::findByName('employer')->syncPermissions($permissions);
    }
}
