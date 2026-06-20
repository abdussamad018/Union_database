<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\ProfessionCategory;
use App\Models\Resident;
use App\Models\Village;
use App\Services\ResidentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResidentViewerController extends Controller
{
    public function __construct(protected ResidentService $residentService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Resident::class);

        $query = $this->residentService->buildFilteredQuery($request, [
            'active_only' => true,
            'complete_only' => true,
            'consent_only' => true,
        ]);

        $residents = $query->paginate(20)->withQueryString();
        $residents->getCollection()->transform(fn ($r) => $this->formatResident($r));

        return Inertia::render('Residents/Viewer/Index', [
            'residents' => $residents,
            'villages' => Village::orderBy('ward_number')->get(['id', 'ward_number', 'name_bn', 'name_en']),
            'houses' => House::orderBy('house_name')->get(['id', 'village_id', 'house_name', 'address']),
            'professionCategories' => ProfessionCategory::orderBy('name_bn')->get(),
            'filters' => $request->only($this->residentService->filterParams()),
            'summary' => [
                'zakat_payers' => Resident::where('resident_status', 'active')->where('zakat_status', 'pays')->count(),
                'zakat_receivers' => Resident::where('resident_status', 'active')->where('is_donation_receiver_eligible', true)->count(),
                'donation_givers' => Resident::where('resident_status', 'active')->where('is_donation_giver_eligible', true)->count(),
                'donation_receivers' => Resident::where('resident_status', 'active')->where('is_donation_receiver_eligible', true)->count(),
            ],
            'presets' => $this->residentService->presets(),
        ]);
    }

    public function show(Resident $resident)
    {
        $this->authorize('view', $resident);

        $resident->load(['house.village', 'professionCategories']);
        $data = $this->formatResident($resident, detailed: true);

        return Inertia::render('Residents/Viewer/Show', ['resident' => $data]);
    }

    protected function formatResident(Resident $resident, bool $detailed = false): array
    {
        $data = [
            'id' => $resident->id,
            'name_bn' => $resident->name_bn,
            'name_en' => $resident->name_en,
            'father_name' => $resident->father_name,
            'gender' => $resident->gender,
            'phone' => $resident->phone,
            'age' => $resident->age,
            'village' => $resident->house?->village,
            'house_name' => $resident->house?->house_name,
            'profession_categories' => $resident->professionCategories,
            'employment_sector' => $resident->employment_sector,
            'employment_status' => $resident->employment_status,
            'income_level' => $resident->income_level,
            'zakat_status' => $resident->zakat_status,
            'is_donation_giver_eligible' => $resident->is_donation_giver_eligible,
            'is_donation_receiver_eligible' => $resident->is_donation_receiver_eligible,
            'needs_urgent_aid' => $resident->needs_urgent_aid,
            'aid_priority' => $resident->aid_priority,
            'is_widow' => $resident->is_widow,
            'is_orphan' => $resident->is_orphan,
            'has_disability' => $resident->has_disability,
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'education_level' => $resident->education_level,
                'marital_status' => $resident->marital_status,
                'designation' => $resident->designation,
                'organization_name' => $resident->organization_name,
                'dependents_count' => $resident->dependents_count,
                'household_head' => $resident->household_head,
                'blood_group' => $resident->blood_group,
                'religion' => $resident->religion,
                'disability_type' => $resident->disability_type,
                'last_aid_received_at' => $resident->last_aid_received_at?->format('Y-m-d'),
            ]);
        }

        return $data;
    }
}
