<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\CustomFieldDefinition;
use App\Models\Donation;
use App\Models\Facility;
use App\Models\FacilityType;
use App\Models\House;
use App\Models\ProfessionCategory;
use App\Models\Resident;
use App\Models\UnionProfile;
use App\Models\User;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'admin@union.test')->exists()) {
            $this->command?->info('Database already seeded. Skipping.');

            return;
        }

        $union = UnionProfile::create([
            'name_bn' => 'ডেমো ইউনিয়ন',
            'name_en' => 'Demo Union',
            'description_bn' => 'একটি দাতব্য ও জনগণ তথ্য ব্যবস্থাপনা ইউনিয়ন।',
            'description_en' => 'A charity and population data management union.',
            'contact_phone' => '01700000000',
            'contact_email' => 'union@example.com',
        ]);

        $villages = [];
        for ($i = 1; $i <= 9; $i++) {
            $villages[] = Village::create([
                'union_id' => $union->id,
                'ward_number' => $i,
                'name_bn' => "ওয়ার্ড {$i}",
                'name_en' => "Ward {$i}",
            ]);
        }

        $professions = [
            ['doctor', 'ডাক্তার', 'Doctor'],
            ['engineer', 'প্রকৌশলী', 'Engineer'],
            ['lawyer', 'আইনজীবী', 'Lawyer'],
            ['teacher', 'শিক্ষক', 'Teacher'],
            ['politician', 'রাজনীতিবিদ', 'Politician'],
            ['union_member', 'ইউনিয়ন সদস্য', 'Union Member'],
            ['chairman', 'চেয়ারম্যান', 'Chairman'],
            ['student', 'শিক্ষার্থী', 'Student'],
            ['farmer', 'কৃষক', 'Farmer'],
            ['housewife', 'গৃহিণী', 'Housewife'],
            ['businessman', 'ব্যবসায়ী', 'Businessman'],
            ['doni', 'ধনী', 'Wealthy'],
            ['gorib', 'দরিদ্র', 'Poor'],
            ['unemployed', 'বেকার', 'Unemployed'],
            ['other', 'অন্যান্য', 'Other'],
        ];
        foreach ($professions as [$slug, $bn, $en]) {
            ProfessionCategory::create(['slug' => $slug, 'name_bn' => $bn, 'name_en' => $en]);
        }

        $facilityTypes = [
            ['social_organization', 'সামাজিক সংগঠন', 'Social Organization'],
            ['high_school', 'উচ্চ বিদ্যালয়', 'High School'],
            ['primary_school', 'প্রাথমিক বিদ্যালয়', 'Primary School'],
            ['madrasa', 'মাদ্রাসা', 'Madrasa'],
            ['mosque', 'মসজিদ', 'Mosque'],
        ];
        $ftIds = [];
        foreach ($facilityTypes as [$slug, $bn, $en]) {
            $ftIds[$slug] = FacilityType::create(['slug' => $slug, 'name_bn' => $bn, 'name_en' => $en])->id;
        }

        $facilityCounts = ['high_school' => 2, 'primary_school' => 5, 'madrasa' => 3, 'mosque' => 8, 'social_organization' => 4];
        $n = 1;
        foreach ($facilityCounts as $slug => $count) {
            $ft = FacilityType::where('slug', $slug)->first();
            for ($i = 1; $i <= $count; $i++) {
                Facility::create([
                    'union_id' => $union->id,
                    'village_id' => $villages[($n - 1) % 9]->id,
                    'facility_type_id' => $ftIds[$slug],
                    'name_bn' => "{$ft->name_bn} {$i}",
                    'name_en' => "{$ft->name_en} {$i}",
                ]);
                $n++;
            }
        }

        $admin = User::create([
            'name' => 'Super Admin',
            'name_bn' => 'সুপার অ্যাডমিন',
            'email' => 'admin@union.test',
            'password' => Hash::make('password'),
            'role' => UserRole::SuperAdmin,
            'locale' => 'bn',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Social Org User',
            'name_bn' => 'সামাজিক সংগঠন',
            'email' => 'social@union.test',
            'password' => Hash::make('password'),
            'role' => UserRole::SocialOrganization,
            'locale' => 'bn',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Elite User',
            'name_bn' => 'এলিট ব্যবহারকারী',
            'email' => 'elite@union.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Elite,
            'locale' => 'en',
            'email_verified_at' => now(),
        ]);

        $professionIds = ProfessionCategory::pluck('id', 'slug');
        $houses = [];

        foreach ($villages as $village) {
            for ($h = 1; $h <= 3; $h++) {
                $house = House::create([
                    'village_id' => $village->id,
                    'house_name' => "বাড়ি {$h}",
                    'address' => "ওয়ার্ড {$village->ward_number}, বাড়ি নং {$h}",
                ]);
                $houses[] = $house;

                if ($h === 1 && count($houses) <= 3) {
                    $bariRep = User::create([
                        'name' => "Bari Rep W{$village->ward_number}",
                        'name_bn' => "প্রতিনিধি W{$village->ward_number}",
                        'email' => "bari{$village->ward_number}@union.test",
                        'phone' => '0170000000' . $village->ward_number,
                        'password' => Hash::make('password'),
                        'role' => UserRole::BariRepresentative,
                        'house_id' => $house->id,
                        'locale' => 'bn',
                        'email_verified_at' => now(),
                    ]);
                    $house->update(['representative_user_id' => $bariRep->id]);
                }

                $samples = [
                    ['name_bn' => 'আব্দুল করিম', 'gender' => 'male', 'professions' => ['farmer'], 'receiver' => true, 'income' => 'very_low'],
                    ['name_bn' => 'ফাতেমা বেগম', 'gender' => 'female', 'professions' => ['housewife'], 'widow' => true, 'receiver' => true, 'income' => 'low'],
                    ['name_bn' => 'রফিকুল ইসলাম', 'gender' => 'male', 'professions' => ['teacher'], 'sector' => 'govt_job_holder', 'status' => 'running', 'giver' => true, 'income' => 'medium'],
                ];

                foreach ($samples as $idx => $s) {
                    $resident = Resident::create([
                        'house_id' => $house->id,
                        'name_bn' => $s['name_bn'],
                        'name_en' => $s['name_bn'],
                        'gender' => $s['gender'],
                        'date_of_birth' => now()->subYears(25 + $idx * 10)->format('Y-m-d'),
                        'resident_status' => 'active',
                        'income_level' => $s['income'] ?? 'medium',
                        'employment_sector' => $s['sector'] ?? null,
                        'employment_status' => $s['status'] ?? 'not_applicable',
                        'is_donation_receiver_eligible' => $s['receiver'] ?? false,
                        'is_donation_giver_eligible' => $s['giver'] ?? false,
                        'is_widow' => $s['widow'] ?? false,
                        'needs_urgent_aid' => ($s['receiver'] ?? false) && $idx === 0,
                        'aid_priority' => ($s['receiver'] ?? false) ? 'high' : 'normal',
                        'profile_status' => 'complete',
                        'zakat_status' => ($s['giver'] ?? false) ? 'pays' : 'not_applicable',
                        'created_by' => $admin->id,
                    ]);
                    $resident->professionCategories()->sync(
                        collect($s['professions'])->map(fn ($p) => $professionIds[$p])->all()
                    );
                }
            }
        }

        for ($i = 1; $i <= 20; $i++) {
            $type = $i % 2 === 0 ? 'given' : 'received';
            $resident = Resident::inRandomOrder()->first();
            Donation::create([
                'union_id' => $union->id,
                'resident_id' => $type === 'received' ? $resident->id : null,
                'type' => $type,
                'amount' => rand(500, 50000),
                'date' => now()->subDays(rand(1, 180))->format('Y-m-d'),
                'description_bn' => $type === 'given' ? 'দান প্রদান' : 'দান গ্রহণ',
                'description_en' => $type === 'given' ? 'Donation given' : 'Donation received',
                'donor_or_recipient_name' => $resident->name_bn,
                'category' => ['charity', 'education', 'health'][rand(0, 2)],
                'recorded_by' => $admin->id,
            ]);
        }

        CustomFieldDefinition::create([
            'entity_type' => 'resident',
            'slug' => 'has_own_land',
            'label_bn' => 'নিজের জমি আছে কিনা',
            'label_en' => 'Has own land',
            'field_type' => 'boolean',
            'form_section' => 'household',
            'is_filterable' => true,
            'display_order' => 1,
            'created_by' => $admin->id,
        ]);

        CustomFieldDefinition::create([
            'entity_type' => 'resident',
            'slug' => 'livestock_count',
            'label_bn' => 'গবাদি পশু সংখ্যা',
            'label_en' => 'Livestock count',
            'field_type' => 'number',
            'form_section' => 'household',
            'display_order' => 2,
            'created_by' => $admin->id,
        ]);

        CustomFieldDefinition::create([
            'entity_type' => 'resident',
            'slug' => 'electricity_connection',
            'label_bn' => 'বিদ্যুৎ সংযোগ',
            'label_en' => 'Electricity connection',
            'field_type' => 'select',
            'options' => [
                ['value' => 'yes', 'label_bn' => 'হ্যাঁ', 'label_en' => 'Yes'],
                ['value' => 'no', 'label_bn' => 'না', 'label_en' => 'No'],
                ['value' => 'illegal', 'label_bn' => 'অননুমোদিত', 'label_en' => 'Illegal'],
            ],
            'form_section' => 'personal',
            'is_filterable' => true,
            'display_order' => 3,
            'created_by' => $admin->id,
        ]);
    }
}
