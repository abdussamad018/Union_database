<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\Facility;
use App\Models\House;
use App\Models\Resident;
use App\Models\UnionProfile;
use App\Models\Village;

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
        $residents = Resident::where('resident_status', 'active');

        return [
            'total_residents' => $residents->count(),
            'total_houses' => House::count(),
            'donation_givers' => Resident::where('is_donation_giver_eligible', true)->count(),
            'donation_receivers' => Resident::where('is_donation_receiver_eligible', true)->count(),
            'zakat_payers' => Resident::where('zakat_status', 'pays')->count(),
            'urgent_aid' => Resident::where('needs_urgent_aid', true)->count(),
            'govt_running' => Resident::where('employment_sector', 'govt_job_holder')->where('employment_status', 'running')->count(),
            'govt_retired' => Resident::where('employment_sector', 'govt_job_holder')->where('employment_status', 'retired')->count(),
            'private_running' => Resident::where('employment_sector', 'private_job_holder')->where('employment_status', 'running')->count(),
            'private_retired' => Resident::where('employment_sector', 'private_job_holder')->where('employment_status', 'retired')->count(),
            'donations_given' => Donation::where('type', 'given')->sum('amount'),
            'donations_received' => Donation::where('type', 'received')->sum('amount'),
            'village_population' => Village::withCount('houses')->orderBy('ward_number')->get()->map(function ($v) {
                $v->residents_count = Resident::whereHas('house', fn ($q) => $q->where('village_id', $v->id))
                    ->where('resident_status', 'active')->count();
                return $v;
            }),
            'profession_breakdown' => Resident::where('resident_status', 'active')
                ->with('professionCategories')
                ->get()
                ->flatMap(fn ($r) => $r->professionCategories)
                ->groupBy('slug')
                ->map->count(),
        ];
    }
}
