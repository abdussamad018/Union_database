<?php

namespace App\Models;

use App\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resident extends Model
{
    use HasCustomFields, SoftDeletes;

    protected $fillable = [
        'house_id', 'name_bn', 'name_en', 'father_name', 'gender', 'date_of_birth',
        'phone', 'nid', 'blood_group', 'religion', 'education_level', 'marital_status',
        'resident_status', 'household_head', 'dependents_count', 'employment_sector',
        'employment_status', 'organization_name', 'designation', 'monthly_income',
        'income_level', 'is_donation_giver_eligible', 'is_donation_receiver_eligible',
        'zakat_status', 'is_probashi', 'migration_country', 'has_disability',
        'disability_type', 'is_widow', 'is_orphan', 'needs_urgent_aid', 'aid_priority',
        'last_aid_received_at', 'is_aid_blacklisted', 'blacklist_reason',
        'consent_for_charity_contact', 'profile_status', 'last_verified_at',
        'verified_by', 'created_by', 'updated_by', 'photo', 'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_aid_received_at' => 'date',
        'last_verified_at' => 'datetime',
        'household_head' => 'boolean',
        'is_donation_giver_eligible' => 'boolean',
        'is_donation_receiver_eligible' => 'boolean',
        'is_probashi' => 'boolean',
        'has_disability' => 'boolean',
        'is_widow' => 'boolean',
        'is_orphan' => 'boolean',
        'needs_urgent_aid' => 'boolean',
        'is_aid_blacklisted' => 'boolean',
        'consent_for_charity_contact' => 'boolean',
        'monthly_income' => 'decimal:2',
        'dependents_count' => 'integer',
    ];

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function professionCategories(): BelongsToMany
    {
        return $this->belongsToMany(ProfessionCategory::class, 'resident_profession_category');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getVulnerabilityScoreAttribute(): int
    {
        if ($this->is_aid_blacklisted) {
            return 0;
        }

        $score = 0;
        if ($this->needs_urgent_aid) $score += 30;
        $score += match ($this->aid_priority) {
            'critical' => 25, 'high' => 15, 'medium' => 8, default => 0,
        };
        if ($this->is_widow) $score += 10;
        if ($this->is_orphan) $score += 10;
        if ($this->has_disability) $score += 10;
        if ($this->dependents_count >= 3) $score += 10;
        $score += match ($this->income_level) {
            'very_low' => 15, 'low' => 10, default => 0,
        };
        if ($this->professionCategories->contains('slug', 'gorib')) $score += 10;

        return min(100, $score);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
}
