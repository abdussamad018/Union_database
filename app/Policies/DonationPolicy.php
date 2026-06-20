<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Donation;
use App\Models\User;

class DonationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canViewDonations();
    }

    public function view(User $user, Donation $donation): bool
    {
        return $user->canViewDonations();
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function update(User $user, Donation $donation): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function delete(User $user, Donation $donation): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }
}
