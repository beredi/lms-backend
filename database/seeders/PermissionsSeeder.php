<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
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
        $authorPermissions = Author::getModelPermissions();
        $bookPermissions = Book::getModelPermissions();
        $permissions = array_merge($userPermissions, $authorPermissions, $bookPermissions);
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $employerPermissions = array_merge($userPermissions, $authorPermissions, $bookPermissions);
        Role::findByName('employer')->syncPermissions($employerPermissions);

        $userPermissions = [
            $authorPermissions['VIEW_AUTHOR'],
            $authorPermissions['VIEW_AUTHORS'],
            $bookPermissions['VIEW_BOOKS'],
            $bookPermissions['VIEW_BOOK']
        ];
        Role::findByName('user')->syncPermissions($employerPermissions);
    }
}
