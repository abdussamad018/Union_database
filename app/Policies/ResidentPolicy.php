<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Resident;
use App\Models\User;

class ResidentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::BariRepresentative,
            UserRole::SocialOrganization,
            UserRole::Elite,
        ]);
    }

    public function view(User $user, Resident $resident): bool
    {
        if ($user->role === UserRole::SuperAdmin) {
            return true;
        }

        if ($user->role === UserRole::BariRepresentative) {
            return $user->house_id === $resident->house_id;
        }

        if (in_array($user->role, [UserRole::SocialOrganization, UserRole::Elite])) {
            return $resident->resident_status === 'active'
                && $resident->profile_status === 'complete'
                && $resident->consent_for_charity_contact;
        }

        return false;
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
