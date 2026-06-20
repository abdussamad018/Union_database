<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case BariRepresentative = 'bari_representative';
    case SocialOrganization = 'social_organization';
    case Elite = 'elite';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::SuperAdmin => $locale === 'bn' ? 'সুপার অ্যাডমিন' : 'Super Admin',
            self::BariRepresentative => $locale === 'bn' ? 'বাড়ি প্রতিনিধি' : 'House Representative',
            self::SocialOrganization => $locale === 'bn' ? 'সামাজিক সংগঠন' : 'Social Organization',
            self::Elite => $locale === 'bn' ? 'এলিট শ্রেণী' : 'Elite Class',
        };
    }
}
