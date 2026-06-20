<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Donation;
use App\Models\Resident;
use App\Services\StatsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(protected StatsService $stats) {}

    public function index()
    {
        return Inertia::render('Dashboard/Admin', [
            'stats' => $this->stats->adminDashboard(),
        ]);
    }

    public function quickDecision(Request $request)
    {
        $query = Resident::with(['house.village', 'professionCategories'])
            ->where('is_donation_receiver_eligible', true)
            ->where('profile_status', 'complete')
            ->where('is_aid_blacklisted', false)
            ->where('resident_status', 'active');

        if ($request->village_id) {
            $query->whereHas('house', fn ($q) => $q->where('village_id', $request->village_id));
        }

        $residents = $query->get()->map(function ($r) {
            return [
                ...$r->toArray(),
                'vulnerability_score' => $r->vulnerability_score,
                'village' => $r->house?->village,
                'recent_aid' => $r->last_aid_received_at && $r->last_aid_received_at->gt(now()->subDays(90)),
                'profession_categories' => $r->professionCategories,
            ];
        })->sortByDesc('vulnerability_score')->values();

        return Inertia::render('Admin/QuickDecision/Index', [
            'residents' => $residents,
            'villages' => \App\Models\Village::orderBy('ward_number')->get(),
        ]);
    }

    public function assignAid(Request $request)
    {
        $data = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'amount' => 'required|numeric|min:1',
            'description_bn' => 'nullable|string',
            'category' => 'required|string',
        ]);

        $resident = Resident::findOrFail($data['resident_id']);
        $union = \App\Models\UnionProfile::first();

        Donation::create([
            'union_id' => $union->id,
            'resident_id' => $resident->id,
            'type' => 'received',
            'amount' => $data['amount'],
            'date' => now()->format('Y-m-d'),
            'description_bn' => $data['description_bn'] ?? 'সহায়তা প্রদান',
            'description_en' => 'Aid assigned',
            'donor_or_recipient_name' => $resident->name_bn,
            'category' => $data['category'],
            'recorded_by' => auth()->id(),
        ]);

        $resident->update(['last_aid_received_at' => now()]);

        return back()->with('success', 'Aid assigned successfully.');
    }

    public function activityLogs()
    {
        $logs = ActivityLog::with('user')->latest('created_at')->paginate(30);

        return Inertia::render('Admin/ActivityLogs/Index', ['logs' => $logs]);
    }
}
