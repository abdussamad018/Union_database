<?php

namespace App\Http\Controllers;

use App\Services\StatsService;
use Inertia\Inertia;

class ViewerDashboardController extends Controller
{
    public function __construct(protected StatsService $stats) {}

    public function index()
    {
        return Inertia::render('Dashboard/Viewer', [
            'stats' => $this->stats->dashboardData(),
        ]);
    }
}
