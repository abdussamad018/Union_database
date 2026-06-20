<?php

namespace App\Http\Controllers;

use App\Models\UnionProfile;
use App\Services\StatsService;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    public function __construct(protected StatsService $stats) {}

    public function index(): Response
    {
        $union = UnionProfile::first();

        return Inertia::render('Landing/Index', [
            'union' => $union,
            'stats' => $this->stats->publicAggregate(),
        ]);
    }

    public function publicStats()
    {
        return response()->json($this->stats->publicAggregate());
    }
}
