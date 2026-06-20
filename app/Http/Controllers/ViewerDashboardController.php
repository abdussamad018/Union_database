<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Village;
use App\Services\StatsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ViewerDashboardController extends Controller
{
    public function __construct(protected StatsService $stats) {}

    public function index(Request $request)
    {
        return Inertia::render('Dashboard/Viewer', [
            'stats' => $this->stats->dashboardData([
                'village_id' => $request->village_id,
                'house_id' => $request->house_id,
            ]),
            'villages' => Village::orderBy('ward_number')->get(['id', 'ward_number', 'name_bn', 'name_en']),
            'houses' => House::orderBy('house_name')->get(['id', 'village_id', 'house_name']),
            'filters' => $request->only(['village_id', 'house_id']),
        ]);
    }
}
