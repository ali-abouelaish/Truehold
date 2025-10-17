<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource for agents.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $agent = $user->agent;
        
        if (!$agent) {
            return redirect()->route('agent.profile.dashboard')->with('error', 'Agent profile not found.');
        }

        $query = $agent->payments()->orderBy('date', 'desc');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $payments = $query->paginate(15);

        // Calculate earnings summary
        $earningsSummary = Payment::calculateAgentEarnings(
            $agent->id,
            $request->get('start_date', now()->startOfMonth()),
            $request->get('end_date', now()->endOfMonth())
        );

        return view('agent.profile.payments', compact('payments', 'earningsSummary'));
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

        return view('agent.profile.payments.create');
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
            'type' => 'required|in:bonus,letting_deal,renewal,marketing,referral,other',
            'invoice_sent_to_management' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $payment = $agent->payments()->create([
            'date' => $request->date,
            'landlord' => $request->landlord,
            'property' => $request->property,
            'client' => $request->client,
            'full_commission' => $request->full_commission,
            'agent_commission' => $request->agent_commission,
            'type' => $request->type,
            'invoice_sent_to_management' => $request->has('invoice_sent_to_management'),
            'notes' => $request->notes,
        ]);

        return redirect()->route('agent.profile.payments')->with('success', 'Payment record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        
        return view('agent.profile.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $this->authorize('update', $payment);
        
        if (!$payment->canBeEdited()) {
            return redirect()->route('agent.profile.payments')->with('error', 'This payment cannot be edited as it has been marked as paid.');
        }
        
        return view('agent.profile.payments.edit', compact('payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $this->authorize('update', $payment);

        if (!$payment->canBeEdited()) {
            return redirect()->route('agent.profile.payments')->with('error', 'This payment cannot be edited as it has been marked as paid.');
        }

        $request->validate([
            'date' => 'required|date',
            'landlord' => 'nullable|string|max:255',
            'property' => 'nullable|string|max:255',
            'client' => 'nullable|string|max:255',
            'full_commission' => 'required|numeric|min:0',
            'agent_commission' => 'required|numeric|min:0',
            'type' => 'required|in:bonus,letting_deal,renewal,marketing,referral,other',
            'invoice_sent_to_management' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $payment->update([
            'date' => $request->date,
            'landlord' => $request->landlord,
            'property' => $request->property,
            'client' => $request->client,
            'full_commission' => $request->full_commission,
            'agent_commission' => $request->agent_commission,
            'type' => $request->type,
            'invoice_sent_to_management' => $request->has('invoice_sent_to_management'),
            'notes' => $request->notes,
        ]);

        return redirect()->route('agent.profile.payments')->with('success', 'Payment record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $this->authorize('delete', $payment);
        
        if (!$payment->canBeEdited()) {
            return redirect()->route('agent.profile.payments')->with('error', 'This payment cannot be deleted as it has been marked as paid.');
        }
        
        $payment->delete();
        
        return redirect()->route('agent.profile.payments')->with('success', 'Payment record deleted successfully.');
    }

    /**
     * Admin: Display all payments
     */
    public function adminIndex(Request $request)
    {
        $query = Payment::with('agent')->orderBy('date', 'desc');

        // Apply filters
        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $payments = $query->paginate(20);
        $agents = Agent::with('user')->get();

        return view('admin.payments.index', compact('payments', 'agents'));
    }

    /**
     * Admin: Show monthly summary
     */
    public function adminMonthlySummary(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $summary = Payment::getMonthlySummary($startDate, $endDate);

        return view('admin.payments.monthly-summary', compact('summary', 'startDate', 'endDate'));
    }

    /**
     * Admin: Edit payment
     */
    public function adminEdit(Payment $payment)
    {
        return view('admin.payments.edit', compact('payment'));
    }

    /**
     * Admin: Update payment
     */
    public function adminUpdate(Request $request, Payment $payment)
    {
        if (!$payment->canBeEdited()) {
            return redirect()->route('admin.payments.index')->with('error', 'This payment cannot be edited as it has been marked as paid.');
        }

        $request->validate([
            'date' => 'required|date',
            'landlord' => 'nullable|string|max:255',
            'property' => 'nullable|string|max:255',
            'client' => 'nullable|string|max:255',
            'full_commission' => 'required|numeric|min:0',
            'agent_commission' => 'required|numeric|min:0',
            'type' => 'required|in:bonus,letting_deal,renewal,marketing,referral,other',
            'invoice_sent_to_management' => 'boolean',
            'notes' => 'nullable|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $payment->update([
            'date' => $request->date,
            'landlord' => $request->landlord,
            'property' => $request->property,
            'client' => $request->client,
            'full_commission' => $request->full_commission,
            'agent_commission' => $request->agent_commission,
            'type' => $request->type,
            'invoice_sent_to_management' => $request->has('invoice_sent_to_management'),
            'notes' => $request->notes,
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.payments.index')->with('success', 'Payment record updated successfully.');
    }

    /**
     * Admin: Mark payment as paid
     */
    public function adminMarkPaid(Request $request, Payment $payment)
    {
        $request->validate([
            'payment_method' => 'required|in:transfer,cash',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $payment->markAsPaid($request->payment_method, $request->admin_notes);

        return redirect()->route('admin.payments.index')->with('success', 'Payment marked as paid successfully.');
    }

    /**
     * Admin: Mark payment as rolled over
     */
    public function adminMarkRolled(Request $request, Payment $payment)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $payment->markAsRolled($request->admin_notes);

        return redirect()->route('admin.payments.index')->with('success', 'Payment marked as rolled over successfully.');
    }

    /**
     * Admin: Delete payment
     */
    public function adminDestroy(Payment $payment)
    {
        if (!$payment->canBeEdited()) {
            return redirect()->route('admin.payments.index')->with('error', 'This payment cannot be deleted as it has been marked as paid.');
        }
        
        $payment->delete();
        
        return redirect()->route('admin.payments.index')->with('success', 'Payment record deleted successfully.');
    }
}
