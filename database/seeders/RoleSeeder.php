<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the roles you want to seed
        $roles = [
            'admin',
            'employer',
            'user',
        ];

        // Loop through the roles and create them
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
