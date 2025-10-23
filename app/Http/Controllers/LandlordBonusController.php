<?php

namespace App\Http\Controllers;

use App\Models\LandlordBonus;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandlordBonusController extends Controller
{
    /**
     * Display a listing of landlord bonuses
     */
    public function index()
    {
        $landlordBonuses = LandlordBonus::with(['agent', 'creator'])
            ->orderBy('date', 'desc')
            ->paginate(20);
        
        return view('admin.landlord-bonuses.index', compact('landlordBonuses'));
    }

    /**
     * Show the form for creating a new landlord bonus
     */
    public function create()
    {
        $agents = Agent::with('user')->get();
        return view('admin.landlord-bonuses.create', compact('agents'));
    }

    /**
     * Store a newly created landlord bonus
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'agent_id' => 'required|exists:agents,id',
            'landlord' => 'required|string|max:255',
            'property' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'commission' => 'required|numeric|min:0',
            'bonus_split' => 'required|in:55_45,100_0',
            'status' => 'required|in:pending,paid,cancelled',
            'notes' => 'nullable|string'
        ]);

        $validated['bonus_code'] = LandlordBonus::generateBonusCode();
        $validated['created_by'] = Auth::id();

        // Calculate commission splits
        if ($validated['bonus_split'] === '100_0') {
            $validated['agent_commission'] = $validated['commission'];
            $validated['agency_commission'] = 0;
        } else {
            $validated['agent_commission'] = $validated['commission'] * 0.55;
            $validated['agency_commission'] = $validated['commission'] * 0.45;
        }

        LandlordBonus::create($validated);

        return redirect()->route('landlord-bonuses.index')
            ->with('success', 'Landlord bonus created successfully.');
    }

    /**
     * Display the specified landlord bonus
     */
    public function show(LandlordBonus $landlordBonus)
    {
        $landlordBonus->load(['agent', 'creator']);
        return view('admin.landlord-bonuses.show', compact('landlordBonus'));
    }

    /**
     * Show the form for editing the specified landlord bonus
     */
    public function edit(LandlordBonus $landlordBonus)
    {
        $agents = Agent::with('user')->get();
        return view('admin.landlord-bonuses.edit', compact('landlordBonus', 'agents'));
    }

    /**
     * Update the specified landlord bonus
     */
    public function update(Request $request, LandlordBonus $landlordBonus)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'agent_id' => 'required|exists:agents,id',
            'landlord' => 'required|string|max:255',
            'property' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'commission' => 'required|numeric|min:0',
            'bonus_split' => 'required|in:55_45,100_0',
            'status' => 'required|in:pending,paid,cancelled',
            'notes' => 'nullable|string'
        ]);

        // Calculate commission splits
        if ($validated['bonus_split'] === '100_0') {
            $validated['agent_commission'] = $validated['commission'];
            $validated['agency_commission'] = 0;
        } else {
            $validated['agent_commission'] = $validated['commission'] * 0.55;
            $validated['agency_commission'] = $validated['commission'] * 0.45;
        }

        $landlordBonus->update($validated);

        return redirect()->route('landlord-bonuses.index')
            ->with('success', 'Landlord bonus updated successfully.');
    }

    /**
     * Remove the specified landlord bonus
     */
    public function destroy(LandlordBonus $landlordBonus)
    {
        $landlordBonus->delete();

        return redirect()->route('landlord-bonuses.index')
            ->with('success', 'Landlord bonus deleted successfully.');
    }
}
