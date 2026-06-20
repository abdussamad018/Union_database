<?php

namespace App\Enums;

class ResidentEnums
{
    public const GENDERS = ['male', 'female', 'other'];
    public const BLOOD_GROUPS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    public const RELIGIONS = ['islam', 'hindu', 'christian', 'buddhist', 'other'];
    public const EDUCATION_LEVELS = ['illiterate', 'primary', 'secondary', 'higher_secondary', 'graduate', 'post_graduate'];
    public const MARITAL_STATUSES = ['single', 'married', 'widowed', 'divorced'];
    public const RESIDENT_STATUSES = ['active', 'deceased', 'migrated', 'temporarily_absent'];
    public const EMPLOYMENT_SECTORS = ['govt_job_holder', 'private_job_holder', 'self_employed', 'business', 'agriculture', 'none'];
    public const EMPLOYMENT_STATUSES = ['running', 'retired', 'not_applicable'];
    public const INCOME_LEVELS = ['very_low', 'low', 'medium', 'high'];
    public const ZAKAT_STATUSES = ['pays', 'does_not_pay', 'not_applicable'];
    public const AID_PRIORITIES = ['normal', 'medium', 'high', 'critical'];
    public const PROFILE_STATUSES = ['draft', 'complete', 'needs_review'];
}
