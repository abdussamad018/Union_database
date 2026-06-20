<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnionProfile;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class VillageController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Villages/Index', [
            'villages' => Village::withCount('houses')
                ->orderBy('ward_number')
                ->paginate(20),
            'union' => UnionProfile::first(['id', 'name_bn', 'name_en']),
        ]);
    }

    public function store(Request $request)
    {
        $union = UnionProfile::firstOrFail();

        $data = $request->validate([
            'ward_number' => [
                'required', 'integer', 'min:1', 'max:99',
                Rule::unique('villages', 'ward_number')->where('union_id', $union->id),
            ],
            'name_bn' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        Village::create([
            ...$data,
            'union_id' => $union->id,
        ]);

        return back()->with('success', 'Village created.');
    }

    public function update(Request $request, Village $village)
    {
        $data = $request->validate([
            'ward_number' => [
                'required', 'integer', 'min:1', 'max:99',
                Rule::unique('villages', 'ward_number')
                    ->where('union_id', $village->union_id)
                    ->ignore($village->id),
            ],
            'name_bn' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $village->update($data);

        return back()->with('success', 'Village updated.');
    }
}
