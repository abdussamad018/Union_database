<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\UnionProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DonationViewerController extends Controller
{
    public function index(Request $request)
    {
        $query = Donation::with(['resident', 'recorder'])
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->category, fn ($q, $c) => $q->where('category', $c))
            ->when($request->from, fn ($q, $f) => $q->where('date', '>=', $f))
            ->when($request->to, fn ($q, $t) => $q->where('date', '<=', $t))
            ->latest('date');

        return Inertia::render('Donations/Index', [
            'donations' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only(['type', 'category', 'from', 'to']),
            'summary' => [
                'total_given' => Donation::where('type', 'given')->sum('amount'),
                'total_received' => Donation::where('type', 'received')->sum('amount'),
            ],
        ]);
    }

    public function show(Donation $donation)
    {
        $this->authorize('view', $donation);
        $donation->load(['resident.house.village', 'recorder']);
        return Inertia::render('Donations/Show', ['donation' => $donation]);
    }
}
