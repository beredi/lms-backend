<?php

namespace App\Policies;

use App\Models\Borrow;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BorrowPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('VIEW_BORROWS');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Borrow $borrow): bool
    {
        return $user->hasPermissionTo('VIEW_BORROW') || $user->id === $borrow->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('CREATE_BORROW');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->hasPermissionTo('EDIT_BORROW');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Borrow $borrow): bool
    {
        return $user->hasPermissionTo('DELETE_BORROW') || $user->id === $borrow->user_id;
    }
}
