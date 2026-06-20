<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\House;
use App\Models\Village;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HouseController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Houses/Index', [
            'houses' => House::with(['village', 'representative'])->withCount('residents')->latest()->paginate(20),
            'villages' => Village::orderBy('ward_number')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'village_id' => 'required|exists:villages,id',
            'house_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'representative_user_id' => 'nullable|exists:users,id',
        ]);
        House::create($data);
        return back()->with('success', 'House created.');
    }

    public function update(Request $request, House $house)
    {
        $data = $request->validate([
            'village_id' => 'required|exists:villages,id',
            'house_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'representative_user_id' => 'nullable|exists:users,id',
        ]);
        $house->update($data);
        return back()->with('success', 'House updated.');
    }
}
