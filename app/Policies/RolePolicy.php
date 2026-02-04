<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view roles');
    }

    public function create(User $user)
    {
        return $user->can('create roles');
    }
}