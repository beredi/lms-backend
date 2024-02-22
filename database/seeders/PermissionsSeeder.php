<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Category;
use App\Models\Payment;
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
        $categoryPermissions = Category::getModelPermissions();
        $borrowPermissions = Borrow::getModelPermissions();
        $paymentPermissions = Payment::getModelPermissions();
        $permissions = array_merge($userPermissions, $authorPermissions, $bookPermissions, $categoryPermissions, $borrowPermissions, $paymentPermissions);
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $employerPermissions = array_merge($userPermissions, $authorPermissions, $bookPermissions, $categoryPermissions, $borrowPermissions, $paymentPermissions);
        Role::findByName('employer')->syncPermissions($employerPermissions);

        $userPermissions = [
            'VIEW_AUTHOR',
            'VIEW_AUTHORS',
            'VIEW_BOOKS',
            'VIEW_BOOK',
            'VIEW_CATEGORY',
            'VIEW_CATEGORIES',
        ];
        Role::findByName('user')->syncPermissions($userPermissions);
    }
}
