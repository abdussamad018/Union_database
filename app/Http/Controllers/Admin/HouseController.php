<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\House;
use App\Models\User;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HouseController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Houses/Index', [
            'houses' => House::with(['village', 'representative'])->withCount('residents')->orderBy('house_name')->paginate(20),
            'villages' => Village::orderBy('ward_number')->get(['id', 'ward_number', 'name_bn', 'name_en']),
            'bariRepresentatives' => User::where('role', UserRole::BariRepresentative)
                ->orderBy('name')
                ->get(['id', 'name', 'name_bn', 'house_id']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateHouse($request);

        DB::transaction(function () use ($data) {
            $house = House::create($data);
            $this->syncRepresentative($house, $data['representative_user_id'] ?? null);
        });

        return back()->with('success', 'House created.');
    }

    public function update(Request $request, House $house)
    {
        $data = $this->validateHouse($request);

        DB::transaction(function () use ($house, $data) {
            $house->update($data);
            $this->syncRepresentative($house, $data['representative_user_id'] ?? null);
        });

        return back()->with('success', 'House updated.');
    }

    protected function validateHouse(Request $request): array
    {
        return $request->validate([
            'village_id' => 'required|exists:villages,id',
            'house_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'representative_user_id' => 'nullable|exists:users,id',
        ]);
    }

    protected function syncRepresentative(House $house, ?int $representativeId): void
    {
        $previousRepId = $house->getOriginal('representative_user_id');

        if ($previousRepId && $previousRepId !== $representativeId) {
            User::where('id', $previousRepId)
                ->where('house_id', $house->id)
                ->update(['house_id' => null]);
        }

        if ($representativeId) {
            User::where('id', $representativeId)->update([
                'house_id' => $house->id,
                'role' => UserRole::BariRepresentative,
            ]);
        }
    }
}
