<?php

namespace App\Http\Controllers\BariRep;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $houseId = auth()->user()->house_id;
        $residents = Resident::where('house_id', $houseId)->with('professionCategories')->get();

        return Inertia::render('Dashboard/BariRep', [
            'stats' => [
                'total_members' => $residents->count(),
                'complete_profiles' => $residents->where('profile_status', 'complete')->count(),
                'incomplete_profiles' => $residents->where('profile_status', '!=', 'complete')->count(),
                'donation_receivers' => $residents->where('is_donation_receiver_eligible', true)->count(),
            ],
            'house' => auth()->user()->house?->load('village'),
        ]);
    }
}
