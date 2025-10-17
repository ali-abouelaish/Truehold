<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BonusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $agent = $user->agent;
        
        if (!$agent) {
            return redirect()->route('agent.profile.dashboard')->with('error', 'Agent profile not found.');
        }

        $bonuses = $agent->bonuses()->orderBy('date', 'desc')->paginate(10);
        
        return view('agent.profile.bonuses', compact('bonuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $agent = $user->agent;
        
        if (!$agent) {
            return redirect()->route('agent.profile.dashboard')->with('error', 'Agent profile not found.');
        }

        return view('agent.profile.bonuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $agent = $user->agent;
        
        if (!$agent) {
            return redirect()->route('agent.profile.dashboard')->with('error', 'Agent profile not found.');
        }

        $request->validate([
            'date' => 'required|date',
            'landlord' => 'nullable|string|max:255',
            'property' => 'nullable|string|max:255',
            'client' => 'nullable|string|max:255',
            'full_commission' => 'required|numeric|min:0',
            'agent_commission' => 'required|numeric|min:0',
            'invoice_sent_to_management' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $bonus = $agent->bonuses()->create([
            'date' => $request->date,
            'landlord' => $request->landlord,
            'property' => $request->property,
            'client' => $request->client,
            'full_commission' => $request->full_commission,
            'agent_commission' => $request->agent_commission,
            'invoice_sent_to_management' => $request->has('invoice_sent_to_management'),
            'notes' => $request->notes,
        ]);

        return redirect()->route('agent.profile.bonuses')->with('success', 'Bonus record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bonus $bonus)
    {
        $this->authorize('view', $bonus);
        
        return view('agent.profile.bonuses.show', compact('bonus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bonus $bonus)
    {
        $this->authorize('update', $bonus);
        
        return view('agent.profile.bonuses.edit', compact('bonus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bonus $bonus)
    {
        $this->authorize('update', $bonus);

        $request->validate([
            'date' => 'required|date',
            'landlord' => 'nullable|string|max:255',
            'property' => 'nullable|string|max:255',
            'client' => 'nullable|string|max:255',
            'full_commission' => 'required|numeric|min:0',
            'agent_commission' => 'required|numeric|min:0',
            'invoice_sent_to_management' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $bonus->update([
            'date' => $request->date,
            'landlord' => $request->landlord,
            'property' => $request->property,
            'client' => $request->client,
            'full_commission' => $request->full_commission,
            'agent_commission' => $request->agent_commission,
            'invoice_sent_to_management' => $request->has('invoice_sent_to_management'),
            'notes' => $request->notes,
        ]);

        return redirect()->route('agent.profile.bonuses')->with('success', 'Bonus record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bonus $bonus)
    {
        $this->authorize('delete', $bonus);
        
        $bonus->delete();
        
        return redirect()->route('agent.profile.bonuses')->with('success', 'Bonus record deleted successfully.');
    }

    /**
     * Admin: Display all bonuses
     */
    public function adminIndex()
    {
        $bonuses = Bonus::with('agent')->orderBy('date', 'desc')->paginate(20);
        
        return view('admin.bonuses.index', compact('bonuses'));
    }

    /**
     * Admin: Edit bonus
     */
    public function adminEdit(Bonus $bonus)
    {
        return view('admin.bonuses.edit', compact('bonus'));
    }

    /**
     * Admin: Update bonus
     */
    public function adminUpdate(Request $request, Bonus $bonus)
    {
        $request->validate([
            'date' => 'required|date',
            'landlord' => 'nullable|string|max:255',
            'property' => 'nullable|string|max:255',
            'client' => 'nullable|string|max:255',
            'full_commission' => 'required|numeric|min:0',
            'agent_commission' => 'required|numeric|min:0',
            'invoice_sent_to_management' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $bonus->update([
            'date' => $request->date,
            'landlord' => $request->landlord,
            'property' => $request->property,
            'client' => $request->client,
            'full_commission' => $request->full_commission,
            'agent_commission' => $request->agent_commission,
            'invoice_sent_to_management' => $request->has('invoice_sent_to_management'),
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.bonuses.index')->with('success', 'Bonus record updated successfully.');
    }

    /**
     * Admin: Delete bonus
     */
    public function adminDestroy(Bonus $bonus)
    {
        $bonus->delete();
        
        return redirect()->route('admin.bonuses.index')->with('success', 'Bonus record deleted successfully.');
    }
}
