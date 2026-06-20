<?php

namespace App\Http\Controllers\BariRep;

use App\Http\Controllers\Controller;
use App\Models\ProfessionCategory;
use App\Models\Resident;
use App\Services\CustomFieldService;
use App\Services\ResidentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResidentController extends Controller
{
    public function __construct(
        protected ResidentService $residentService,
        protected CustomFieldService $customFieldService,
    ) {}

    public function index(Request $request)
    {
        $houseId = auth()->user()->house_id;
        $residents = Resident::where('house_id', $houseId)
            ->with('professionCategories')
            ->when($request->search, fn ($q, $s) => $q->where('name_bn', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('BariRep/Residents/Index', [
            'residents' => $residents,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('BariRep/Residents/Form', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateResident($request);
        $data['house_id'] = auth()->user()->house_id;
        $this->authorize('create', Resident::class);
        $this->residentService->create($data, 'bari_representative');
        return redirect()->route('bari.residents.index')->with('success', 'Resident added.');
    }

    public function edit(Resident $resident)
    {
        $this->authorize('update', $resident);
        $resident->load(['professionCategories', 'customFieldValues.definition']);
        return Inertia::render('BariRep/Residents/Form', [
            ...$this->formData(),
            'resident' => $resident,
            'customFieldValues' => $this->customFieldService->getValuesForForm($resident),
        ]);
    }

    public function update(Request $request, Resident $resident)
    {
        $this->authorize('update', $resident);
        $data = $this->validateResident($request);
        unset($data['house_id']);
        $this->residentService->update($resident, $data, 'bari_representative');
        return redirect()->route('bari.residents.index')->with('success', 'Resident updated.');
    }

    public function destroy(Resident $resident)
    {
        $this->authorize('delete', $resident);
        $resident->delete();
        return back()->with('success', 'Resident removed.');
    }

    public function verify(Resident $resident)
    {
        $this->authorize('update', $resident);
        $this->residentService->verify($resident);
        return back()->with('success', 'Profile verified and marked complete.');
    }

    protected function formData(): array
    {
        return [
            'professionCategories' => ProfessionCategory::all(),
            'customFieldDefinitions' => $this->customFieldService->getDefinitions('resident', 'bari_representative'),
            'formOptions' => $this->residentService->formOptions(),
            'house' => auth()->user()->house?->load('village'),
        ];
    }

    protected function validateResident(Request $request): array
    {
        return $request->validate([
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
            'consent_for_charity_contact' => 'boolean',
            'profile_status' => 'required|string',
            'notes' => 'nullable|string',
            'profession_category_ids' => 'array',
            'profession_category_ids.*' => 'exists:profession_categories,id',
            'custom_fields' => 'array',
        ]);
    }
}
