<?php

namespace App\Http\Controllers;

use App\Models\LandlordBonus;
use App\Models\Agent;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LandlordBonusController extends Controller
{
    /**
     * Bulk mark selected landlord bonuses as paid (admin only)
     */
    public function bulkMarkPaid(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only administrators can perform bulk updates.',
                ], 403);
            }
            return redirect()->back()->with('error', 'Only administrators can perform bulk updates.');
        }

        $validated = $request->validate([
            'landlord_bonus_ids' => 'required|array',
            'landlord_bonus_ids.*' => 'integer|exists:landlord_bonuses,id',
        ]);

        $ids = $validated['landlord_bonus_ids'];

        try {
            \Log::info('Bulk mark landlord bonuses as paid', ['count' => count($ids)]);
            LandlordBonus::whereIn('id', $ids)->update([
                'status' => 'paid',
                'updated_at' => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Selected landlord bonuses marked as paid.',
                ]);
            }
            return redirect()->back()->with('success', 'Selected landlord bonuses marked as paid.');
        } catch (\Exception $e) {
            \Log::error('Bulk mark landlord bonuses as paid failed', ['error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark selected landlord bonuses as paid: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to mark selected landlord bonuses as paid.');
        }
    }

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

    /**
     * Generate invoice from selected landlord bonuses
     */
    public function generateInvoice(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            return redirect()->back()->with('error', 'Only administrators can generate invoices.');
        }

        $validated = $request->validate([
            'bonus_ids' => 'required|string',
        ]);

        $bonusIds = json_decode($validated['bonus_ids'], true);
        
        if (empty($bonusIds) || !is_array($bonusIds)) {
            return redirect()->back()->with('error', 'Please select at least one bonus to generate an invoice.');
        }

        try {
            // Load bonuses
            $bonuses = LandlordBonus::whereIn('id', $bonusIds)->get();

            if ($bonuses->isEmpty()) {
                return redirect()->back()->with('error', 'No valid bonuses found.');
            }

            // Group bonuses by landlord name
            $bonusesByLandlord = $bonuses->groupBy('landlord');

            DB::beginTransaction();

            $createdInvoices = [];

            foreach ($bonusesByLandlord as $landlordName => $landlordBonuses) {
                $firstBonus = $landlordBonuses->first();

                // Prepare invoice items from bonuses
                $invoiceItems = [];
                $totalAmount = 0;

                foreach ($landlordBonuses as $bonus) {
                    $description = "Landlord Bonus - {$bonus->bonus_code}";
                    if ($bonus->property) {
                        $description .= " ({$bonus->property})";
                    }
                    if ($bonus->client) {
                        $description .= " - Client: {$bonus->client}";
                    }

                    $invoiceItems[] = [
                        'description' => $description,
                        'quantity' => 1,
                        'rate' => (float) $bonus->commission,
                    ];

                    $totalAmount += (float) $bonus->commission;
                }

                // Use landlord name as client (receiver)
                $clientName = $landlordName ?? 'Landlord';
                $clientAddress = null; // Landlord address not available in bonus data
                $clientEmail = null; // Landlord email not available in bonus data
                $clientPhone = null; // Landlord phone not available in bonus data

                // Create invoice
                $invoice = new Invoice();
                $invoice->invoice_number = $invoice->generateInvoiceNumber();
                $invoice->invoice_date = now()->toDateString();
                $invoice->due_date = now()->addDays(30)->toDateString();
                $invoice->payment_terms = '30 days';
                $invoice->client_name = $clientName;
                $invoice->client_address = $clientAddress;
                $invoice->client_email = $clientEmail;
                $invoice->client_phone = $clientPhone;
                $invoice->agent_name = auth()->user()->name ?? 'System';
                $invoice->items = $invoiceItems;
                $invoice->tax_rate = 0;
                
                // Set company details
                $invoice->company_name = 'Truehold Group Limited';
                $invoice->company_address = 'Business Banking';
                $invoice->account_holder_name = 'TRUEHOLD GROUP LTD';
                $invoice->account_number = '63935841';
                $invoice->sort_code = '20-41-50';
                $invoice->bank_name = 'Business Banking';
                
                // Add notes about the bonuses
                $bonusCodes = $landlordBonuses->pluck('bonus_code')->implode(', ');
                $invoice->notes = "Invoice generated from Landlord Bonuses: {$bonusCodes}";
                
                $invoice->calculateTotals();
                $invoice->save();

                // Mark bonuses as paid and link to invoice
                $bonusIds = $landlordBonuses->pluck('id')->toArray();
                LandlordBonus::whereIn('id', $bonusIds)->update([
                    'status' => 'paid',
                    'updated_at' => now(),
                ]);

                $createdInvoices[] = $invoice;
            }

            DB::commit();

            if (count($createdInvoices) === 1) {
                // Single invoice created, redirect to it
                return redirect()->route('admin.invoices.show', $createdInvoices[0])
                    ->with('success', 'Invoice generated successfully from selected bonuses!');
            } else {
                // Multiple invoices created, redirect to invoice index
                return redirect()->route('admin.invoices.index')
                    ->with('success', count($createdInvoices) . ' invoices generated successfully from selected bonuses!');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to generate invoice from landlord bonuses', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }
}
