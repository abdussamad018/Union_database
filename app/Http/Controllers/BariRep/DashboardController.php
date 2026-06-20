<?php

namespace App\Http\Controllers\BariRep;

use App\Http\Controllers\Controller;
use App\Services\StatsService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(protected StatsService $stats) {}

    public function index()
    {
        $houseId = auth()->user()->house_id;

        return Inertia::render('Dashboard/BariRep', [
            'stats' => $this->stats->dashboardData(['house_id' => $houseId]),
            'house' => auth()->user()->house?->load('village'),
        ]);
    }
}
