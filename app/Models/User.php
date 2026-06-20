<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'name_bn', 'email', 'phone', 'password', 'role', 'locale', 'is_active', 'house_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'role' => UserRole::class,
    ];

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function representedHouse(): HasOne
    {
        return $this->hasOne(House::class, 'representative_user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isBariRepresentative(): bool
    {
        return $this->role === UserRole::BariRepresentative;
    }

    public function canViewDonations(): bool
    {
        return in_array($this->role, [
            UserRole::SuperAdmin,
            UserRole::SocialOrganization,
            UserRole::Elite,
        ]);
    }
}
