<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Resident;
use App\Models\User;

class ResidentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Resident $resident): bool
    {
        return $user->role === UserRole::SuperAdmin
            || ($user->role === UserRole::BariRepresentative && $user->house_id === $resident->house_id);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::BariRepresentative]);
    }

    public function update(User $user, Resident $resident): bool
    {
        return $user->role === UserRole::SuperAdmin
            || ($user->role === UserRole::BariRepresentative && $user->house_id === $resident->house_id);
    }

    public function delete(User $user, Resident $resident): bool
    {
        return $this->update($user, $resident);
    }
}
