<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\CustomFieldDefinition;
use App\Models\User;

class CustomFieldDefinitionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function update(User $user, CustomFieldDefinition $definition): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function delete(User $user, CustomFieldDefinition $definition): bool
    {
        return false;
    }
}
