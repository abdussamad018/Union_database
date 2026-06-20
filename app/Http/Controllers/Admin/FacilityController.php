<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityType;
use App\Models\UnionProfile;
use App\Models\Village;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacilityController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Facilities/Index', [
            'facilities' => Facility::with(['facilityType', 'village'])->latest()->paginate(20),
            'facilityTypes' => FacilityType::all(),
            'villages' => Village::orderBy('ward_number')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'facility_type_id' => 'required|exists:facility_types,id',
            'village_id' => 'nullable|exists:villages,id',
            'name_bn' => 'required|string',
            'name_en' => 'nullable|string',
            'address' => 'nullable|string',
            'contact_phone' => 'nullable|string',
        ]);
        $data['union_id'] = UnionProfile::first()->id;
        Facility::create($data);
        return back()->with('success', 'Facility added.');
    }
}
