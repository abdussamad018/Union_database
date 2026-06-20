<?php

namespace App\Http\Controllers;

use App\Models\ProfessionCategory;
use App\Models\Resident;
use App\Models\Village;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResidentViewerController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Resident::class);

        $query = $this->buildQuery($request);

        $residents = $query->paginate(20)->withQueryString();
        $residents->getCollection()->transform(fn ($r) => $this->formatResident($r));

        return Inertia::render('Residents/Viewer/Index', [
            'residents' => $residents,
            'villages' => Village::orderBy('ward_number')->get(['id', 'ward_number', 'name_bn', 'name_en']),
            'professionCategories' => ProfessionCategory::orderBy('name_bn')->get(),
            'filters' => $request->only([
                'search', 'village_id', 'zakat_status', 'is_donation_giver_eligible',
                'is_donation_receiver_eligible', 'needs_urgent_aid', 'aid_priority',
                'employment_sector', 'income_level', 'is_widow', 'is_orphan',
                'has_disability', 'profession_category_id', 'preset',
            ]),
            'summary' => [
                'zakat_payers' => Resident::where('resident_status', 'active')->where('zakat_status', 'pays')->count(),
                'zakat_receivers' => Resident::where('resident_status', 'active')->where('is_donation_receiver_eligible', true)->count(),
                'donation_givers' => Resident::where('resident_status', 'active')->where('is_donation_giver_eligible', true)->count(),
                'donation_receivers' => Resident::where('resident_status', 'active')->where('is_donation_receiver_eligible', true)->count(),
            ],
            'presets' => $this->presets(),
        ]);
    }

    public function show(Resident $resident)
    {
        $this->authorize('view', $resident);

        $resident->load(['house.village', 'professionCategories']);
        $data = $this->formatResident($resident, detailed: true);

        return Inertia::render('Residents/Viewer/Show', ['resident' => $data]);
    }

    protected function buildQuery(Request $request)
    {
        $query = Resident::with(['house.village', 'professionCategories'])
            ->where('resident_status', 'active')
            ->where('profile_status', 'complete')
            ->where('consent_for_charity_contact', true);

        $this->applyPreset($query, $request->preset);

        return $query
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('name_bn', 'like', "%{$s}%")
                    ->orWhere('name_en', 'like', "%{$s}%")
                    ->orWhere('father_name', 'like', "%{$s}%");
            }))
            ->when($request->village_id, fn ($q, $v) => $q->whereHas('house', fn ($h) => $h->where('village_id', $v)))
            ->when($request->filled('zakat_status'), fn ($q) => $q->where('zakat_status', $request->zakat_status))
            ->when($request->boolean('is_donation_giver_eligible'), fn ($q) => $q->where('is_donation_giver_eligible', true))
            ->when($request->boolean('is_donation_receiver_eligible'), fn ($q) => $q->where('is_donation_receiver_eligible', true))
            ->when($request->boolean('needs_urgent_aid'), fn ($q) => $q->where('needs_urgent_aid', true))
            ->when($request->aid_priority, fn ($q, $p) => $q->where('aid_priority', $p))
            ->when($request->employment_sector, fn ($q, $s) => $q->where('employment_sector', $s))
            ->when($request->income_level, fn ($q, $l) => $q->where('income_level', $l))
            ->when($request->boolean('is_widow'), fn ($q) => $q->where('is_widow', true))
            ->when($request->boolean('is_orphan'), fn ($q) => $q->where('is_orphan', true))
            ->when($request->boolean('has_disability'), fn ($q) => $q->where('has_disability', true))
            ->when($request->profession_category_id, fn ($q, $id) => $q->whereHas(
                'professionCategories',
                fn ($p) => $p->where('profession_categories.id', $id)
            ))
            ->latest('name_bn');
    }

    protected function applyPreset($query, ?string $preset): void
    {
        match ($preset) {
            'zakat_payers' => $query->where('zakat_status', 'pays'),
            'zakat_receivers' => $query->where('is_donation_receiver_eligible', true)
                ->whereIn('zakat_status', ['does_not_pay', 'not_applicable']),
            'donation_givers' => $query->where('is_donation_giver_eligible', true),
            'donation_receivers' => $query->where('is_donation_receiver_eligible', true),
            'urgent_aid' => $query->where('needs_urgent_aid', true)->where('is_aid_blacklisted', false),
            default => null,
        };
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

    protected function presets(): array
    {
        return [
            ['key' => 'zakat_payers', 'label_bn' => 'জাকাত দাতা', 'label_en' => 'Zakat Payers'],
            ['key' => 'zakat_receivers', 'label_bn' => 'জাকাত পাওয়ার উপযুক্ত', 'label_en' => 'Zakat Eligible'],
            ['key' => 'donation_givers', 'label_bn' => 'দান দাতা', 'label_en' => 'Donation Givers'],
            ['key' => 'donation_receivers', 'label_bn' => 'দান গ্রহীতা', 'label_en' => 'Donation Receivers'],
            ['key' => 'urgent_aid', 'label_bn' => 'জরুরি সহায়তা', 'label_en' => 'Urgent Aid'],
        ];
    }
}
