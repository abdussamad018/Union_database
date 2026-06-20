<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfessionCategory;
use App\Models\Resident;
use App\Models\Village;
use App\Services\CustomFieldService;
use App\Services\ResidentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResidentController extends Controller
{
    public function __construct(
        protected ResidentService $residentService,
        protected CustomFieldService $customFieldService,
    ) {}

    public function index(Request $request)
    {
        $query = Resident::with(['house.village', 'professionCategories'])
            ->when($request->search, fn ($q, $s) => $q->where('name_bn', 'like', "%{$s}%")->orWhere('name_en', 'like', "%{$s}%"))
            ->when($request->village_id, fn ($q, $v) => $q->whereHas('house', fn ($h) => $h->where('village_id', $v)))
            ->when($request->needs_urgent_aid, fn ($q) => $q->where('needs_urgent_aid', true))
            ->when($request->is_donation_receiver_eligible, fn ($q) => $q->where('is_donation_receiver_eligible', true));

        return Inertia::render('Admin/Residents/Index', [
            'residents' => $query->latest()->paginate(20)->withQueryString(),
            'villages' => Village::orderBy('ward_number')->get(),
            'filters' => $request->only(['search', 'village_id', 'needs_urgent_aid', 'is_donation_receiver_eligible']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Residents/Form', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateResident($request);
        $this->residentService->create($data);
        return redirect()->route('admin.residents.index')->with('success', 'Resident created.');
    }

    public function edit(Resident $resident)
    {
        $resident->load(['professionCategories', 'house.village', 'customFieldValues.definition']);
        return Inertia::render('Admin/Residents/Form', [
            ...$this->formData(),
            'resident' => $resident,
            'customFieldValues' => $this->customFieldService->getValuesForForm($resident),
        ]);
    }

    public function update(Request $request, Resident $resident)
    {
        $data = $this->validateResident($request);
        $this->residentService->update($resident, $data);
        return redirect()->route('admin.residents.index')->with('success', 'Resident updated.');
    }

    public function destroy(Resident $resident)
    {
        $resident->delete();
        return back()->with('success', 'Resident deleted.');
    }

    public function export(): StreamedResponse
    {
        $headers = ['Name BN', 'Name EN', 'Village', 'House', 'Gender', 'Phone', 'Income', 'Receiver', 'Giver', 'Aid Priority'];

        return response()->streamDownload(function () use ($headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            Resident::with(['house.village'])->chunk(100, function ($residents) use ($handle) {
                foreach ($residents as $r) {
                    fputcsv($handle, [
                        $r->name_bn, $r->name_en,
                        $r->house?->village?->name_bn, $r->house?->house_name,
                        $r->gender, $r->phone, $r->income_level,
                        $r->is_donation_receiver_eligible ? 'Yes' : 'No',
                        $r->is_donation_giver_eligible ? 'Yes' : 'No',
                        $r->aid_priority,
                    ]);
                }
            });
            fclose($handle);
        }, 'residents.csv');
    }

    protected function formData(): array
    {
        return [
            'houses' => \App\Models\House::with('village')->get(),
            'professionCategories' => ProfessionCategory::all(),
            'customFieldDefinitions' => $this->customFieldService->getDefinitions('resident'),
            'formOptions' => $this->residentService->formOptions(),
        ];
    }

    protected function validateResident(Request $request): array
    {
        return $request->validate([
            'house_id' => 'required|exists:houses,id',
            'name_bn' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'nid' => 'nullable|string|max:30|unique:residents,nid,' . ($request->route('resident')?->id ?? 'NULL'),
            'blood_group' => 'nullable|string',
            'religion' => 'nullable|string',
            'education_level' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'resident_status' => 'required|string',
            'household_head' => 'boolean',
            'dependents_count' => 'integer|min:0',
            'employment_sector' => 'nullable|string',
            'employment_status' => 'nullable|string',
            'organization_name' => 'nullable|string',
            'designation' => 'nullable|string',
            'monthly_income' => 'nullable|numeric',
            'income_level' => 'nullable|string',
            'is_donation_giver_eligible' => 'boolean',
            'is_donation_receiver_eligible' => 'boolean',
            'zakat_status' => 'required|string',
            'is_probashi' => 'boolean',
            'migration_country' => 'nullable|string',
            'has_disability' => 'boolean',
            'disability_type' => 'nullable|string',
            'is_widow' => 'boolean',
            'is_orphan' => 'boolean',
            'needs_urgent_aid' => 'boolean',
            'aid_priority' => 'required|string',
            'is_aid_blacklisted' => 'boolean',
            'blacklist_reason' => 'nullable|string',
            'consent_for_charity_contact' => 'boolean',
            'profile_status' => 'required|string',
            'notes' => 'nullable|string',
            'profession_category_ids' => 'array',
            'profession_category_ids.*' => 'exists:profession_categories,id',
            'custom_fields' => 'array',
        ]);
    }
}
