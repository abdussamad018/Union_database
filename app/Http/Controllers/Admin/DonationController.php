<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Resident;
use App\Models\UnionProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $donations = Donation::with(['resident', 'recorder'])
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->latest('date')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Donations/Index', [
            'donations' => $donations,
            'filters' => $request->only('type'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Donations/Form', [
            'residents' => Resident::orderBy('name_bn')->get(['id', 'name_bn']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:given,received',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'resident_id' => 'nullable|exists:residents,id',
            'description_bn' => 'nullable|string',
            'description_en' => 'nullable|string',
            'donor_or_recipient_name' => 'nullable|string',
            'category' => 'required|string',
        ]);

        $union = UnionProfile::first();
        $data['union_id'] = $union->id;
        $data['recorded_by'] = auth()->id();

        $donation = Donation::create($data);

        if ($data['type'] === 'received' && $data['resident_id']) {
            Resident::where('id', $data['resident_id'])->update(['last_aid_received_at' => $data['date']]);
        }

        return redirect()->route('admin.donations.index')->with('success', 'Donation recorded.');
    }

    public function destroy(Donation $donation)
    {
        $donation->delete();
        return back()->with('success', 'Donation deleted.');
    }
}
