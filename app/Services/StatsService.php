<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\Facility;
use App\Models\House;
use App\Models\Resident;
use App\Models\Village;
use Illuminate\Database\Eloquent\Builder;

class StatsService
{
    public function publicAggregate(): array
    {
        return [
            'total_residents' => Resident::where('resident_status', 'active')->count(),
            'total_houses' => House::count(),
            'total_villages' => Village::count(),
            'facilities' => Facility::with('facilityType')
                ->get()
                ->groupBy(fn ($f) => $f->facilityType->slug)
                ->map->count(),
        ];
    }

    public function adminDashboard(): array
    {
        return $this->dashboardData();
    }

    public function dashboardData(?int $houseId = null): array
    {
        $scoped = (bool) $houseId;
        $query = fn () => $this->baseResidents($houseId);

        $data = [
            'scoped_to_house' => $scoped,
            'total_residents' => $query()->count(),
            'donation_givers' => $query()->where('is_donation_giver_eligible', true)->count(),
            'donation_receivers' => $query()->where('is_donation_receiver_eligible', true)->count(),
            'zakat_payers' => $query()->where('zakat_status', 'pays')->count(),
            'zakat_non_payers' => $query()->where('zakat_status', 'does_not_pay')->count(),
            'urgent_aid' => $query()->where('needs_urgent_aid', true)->count(),
            'govt_running' => $query()->where('employment_sector', 'govt_job_holder')->where('employment_status', 'running')->count(),
            'govt_retired' => $query()->where('employment_sector', 'govt_job_holder')->where('employment_status', 'retired')->count(),
            'private_running' => $query()->where('employment_sector', 'private_job_holder')->where('employment_status', 'running')->count(),
            'private_retired' => $query()->where('employment_sector', 'private_job_holder')->where('employment_status', 'retired')->count(),
            'complete_profiles' => $query()->where('profile_status', 'complete')->count(),
            'incomplete_profiles' => $query()->where('profile_status', '!=', 'complete')->count(),
            'charts' => $this->comparisonCharts($houseId),
        ];

        if ($scoped) {
            $data['total_members'] = $data['total_residents'];
        } else {
            $data['total_houses'] = House::count();
            $data['donations_given'] = Donation::where('type', 'given')->sum('amount');
            $data['donations_received'] = Donation::where('type', 'received')->sum('amount');
            $data['village_population'] = Village::withCount('houses')->orderBy('ward_number')->get()->map(function ($v) {
                $v->residents_count = Resident::whereHas('house', fn ($q) => $q->where('village_id', $v->id))
                    ->where('resident_status', 'active')->count();

                return $v;
            });
            $data['profession_breakdown'] = Resident::where('resident_status', 'active')
                ->with('professionCategories')
                ->get()
                ->flatMap(fn ($r) => $r->professionCategories)
                ->groupBy('slug')
                ->map->count();
        }

        return $data;
    }

    public function comparisonCharts(?int $houseId = null): array
    {
        $q = fn () => $this->baseResidents($houseId);

        return [
            [
                'key' => 'zakat',
                'title_bn' => 'জাকাত তুলনা',
                'title_en' => 'Zakat Comparison',
                'labels_bn' => ['জাকাত দেন', 'জাকাত দেন না', 'প্রযোজ্য নয়'],
                'labels_en' => ['Pays Zakat', 'Does Not Pay', 'N/A'],
                'values' => [
                    $q()->where('zakat_status', 'pays')->count(),
                    $q()->where('zakat_status', 'does_not_pay')->count(),
                    $q()->where('zakat_status', 'not_applicable')->count(),
                ],
                'colors' => ['#0F766E', '#F59E0B', '#94A3B8'],
            ],
            [
                'key' => 'employment_sector',
                'title_bn' => 'চাকরি খাত',
                'title_en' => 'Employment Sector',
                'labels_bn' => ['সরকারি', 'বেসরকারি', 'স্বনিয়োজিত', 'ব্যবসা', 'কৃষি'],
                'labels_en' => ['Govt Job', 'Private Job', 'Self Employed', 'Business', 'Agriculture'],
                'values' => [
                    $q()->where('employment_sector', 'govt_job_holder')->count(),
                    $q()->where('employment_sector', 'private_job_holder')->count(),
                    $q()->where('employment_sector', 'self_employed')->count(),
                    $q()->where('employment_sector', 'business')->count(),
                    $q()->where('employment_sector', 'agriculture')->count(),
                ],
                'colors' => ['#0F766E', '#0369A1', '#7C3AED', '#DB2777', '#65A30D'],
            ],
            [
                'key' => 'donation_eligibility',
                'title_bn' => 'দান দাতা বনাম গ্রহীতা',
                'title_en' => 'Donation Givers vs Receivers',
                'labels_bn' => ['দান দাতা', 'দান গ্রহীতা'],
                'labels_en' => ['Givers', 'Receivers'],
                'values' => [
                    $q()->where('is_donation_giver_eligible', true)->count(),
                    $q()->where('is_donation_receiver_eligible', true)->count(),
                ],
                'colors' => ['#0F766E', '#EA580C'],
            ],
            [
                'key' => 'gender',
                'title_bn' => 'লিঙ্গ তুলনা',
                'title_en' => 'Gender Comparison',
                'labels_bn' => ['পুরুষ', 'মহিলা', 'অন্যান্য'],
                'labels_en' => ['Male', 'Female', 'Other'],
                'values' => [
                    $q()->where('gender', 'male')->count(),
                    $q()->where('gender', 'female')->count(),
                    $q()->where('gender', 'other')->count(),
                ],
                'colors' => ['#0369A1', '#DB2777', '#94A3B8'],
            ],
            [
                'key' => 'income_level',
                'title_bn' => 'আয়ের স্তর',
                'title_en' => 'Income Level',
                'labels_bn' => ['খুব কম', 'কম', 'মাঝারি', 'উচ্চ'],
                'labels_en' => ['Very Low', 'Low', 'Medium', 'High'],
                'values' => [
                    $q()->where('income_level', 'very_low')->count(),
                    $q()->where('income_level', 'low')->count(),
                    $q()->where('income_level', 'medium')->count(),
                    $q()->where('income_level', 'high')->count(),
                ],
                'colors' => ['#DC2626', '#F59E0B', '#0F766E', '#0369A1'],
            ],
            [
                'key' => 'govt_private_running',
                'title_bn' => 'সরকারি বনাম বেসরকারি (চাকরিতে)',
                'title_en' => 'Govt vs Private (Running)',
                'labels_bn' => ['সরকারি চাকরি', 'বেসরকারি চাকরি'],
                'labels_en' => ['Govt (Running)', 'Private (Running)'],
                'values' => [
                    $q()->where('employment_sector', 'govt_job_holder')->where('employment_status', 'running')->count(),
                    $q()->where('employment_sector', 'private_job_holder')->where('employment_status', 'running')->count(),
                ],
                'colors' => ['#0F766E', '#0369A1'],
            ],
            [
                'key' => 'vulnerability',
                'title_bn' => 'দুর্বল জনগোষ্ঠী',
                'title_en' => 'Vulnerable Groups',
                'labels_bn' => ['বিধবা', 'এতিম', 'প্রতিবন্ধী', 'জরুরি সহায়তা'],
                'labels_en' => ['Widow', 'Orphan', 'Disability', 'Urgent Aid'],
                'values' => [
                    $q()->where('is_widow', true)->count(),
                    $q()->where('is_orphan', true)->count(),
                    $q()->where('has_disability', true)->count(),
                    $q()->where('needs_urgent_aid', true)->count(),
                ],
                'colors' => ['#A855F7', '#F59E0B', '#6366F1', '#DC2626'],
            ],
            [
                'key' => 'profile_status',
                'title_bn' => 'প্রোফাইল অবস্থা',
                'title_en' => 'Profile Status',
                'labels_bn' => ['সম্পূর্ণ', 'খসড়া', 'পর্যালোচনা দরকার'],
                'labels_en' => ['Complete', 'Draft', 'Needs Review'],
                'values' => [
                    $q()->where('profile_status', 'complete')->count(),
                    $q()->where('profile_status', 'draft')->count(),
                    $q()->where('profile_status', 'needs_review')->count(),
                ],
                'colors' => ['#16A34A', '#F59E0B', '#DC2626'],
            ],
        ];
    }

    protected function baseResidents(?int $houseId = null): Builder
    {
        return Resident::query()
            ->where('resident_status', 'active')
            ->when($houseId, fn ($q) => $q->where('house_id', $houseId));
    }
}
