<?php

namespace App\Http\Controllers;

use App\Models\RentalCode;
use App\Models\Agent;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RentalCodeController extends Controller
{
    /**
     * Display a listing of rental codes
     */
    public function index()
    {
        $rentalCodes = RentalCode::with('client')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.rental-codes.index', compact('rentalCodes'));
    }

    /**
     * Show the form for creating a new rental code
     */
    public function create()
    {
        // Get users who are agents (either by role or by having an agent profile)
        // Simplified approach: get all users with agent role OR agent profile
        $agentUsers = User::where('role', 'agent')->with('agent')->get();

      
        
       
        
        
        return view('admin.rental-codes.create', compact('agentUsers'));
    }

    /**
     * Store a newly created rental code
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rental_code' => 'required|string|unique:rental_codes,rental_code',
            'rental_date' => 'required|date',
            'consultation_fee' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:Cash,Transfer',
            'property' => 'nullable|string',
            'licensor' => 'nullable|string',
            'client_full_name' => 'required|string|max:255',
            'client_date_of_birth' => 'required|date',
            'client_phone_number' => 'required|string|max:20',
            'client_email' => 'required|email|max:255',
            'client_nationality' => 'required|string|max:100',
            'client_current_address' => 'required|string',
            'client_company_university_name' => 'nullable|string|max:255',
            'client_company_university_address' => 'nullable|string',
            'client_position_role' => 'nullable|string|max:255',
            'rent_by_agent' => 'required|string|max:255',
            'client_by_agent' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:pending,approved,completed,cancelled',
        ]);

        // Handle client creation or retrieval
        $client = $this->handleClient($validated);

        // Create rental code with client_id
        $rentalCodeData = $validated;
        $rentalCodeData['client_id'] = $client->id;
        
        // Remove client fields from rental code data as they're now in the client record
        $clientFields = [
            'client_full_name', 'client_date_of_birth', 'client_phone_number',
            'client_email', 'client_nationality', 'client_current_address',
            'client_company_university_name', 'client_company_university_address',
            'client_position_role'
        ];
        
        foreach ($clientFields as $field) {
            unset($rentalCodeData[$field]);
        }

        $rentalCode = RentalCode::create($rentalCodeData);

        return redirect()->route('rental-codes.index')
            ->with('success', 'Rental code created successfully!');
    }

    /**
     * Display the specified rental code
     */
    public function show(RentalCode $rentalCode)
    {
        $rentalCode->load('client');
        return view('admin.rental-codes.show', compact('rentalCode'));
    }

    /**
     * Show the form for editing the specified rental code
     */
    public function edit(RentalCode $rentalCode)
    {
        // Get users who are agents (either by role or by having an agent profile)
        $usersWithAgentRole = User::where('role', 'agent')->get();
        $usersWithAgentProfile = User::whereHas('agent')->get();
        
        // Combine and remove duplicates
        $agentUsers = $usersWithAgentRole->merge($usersWithAgentProfile)->unique('id')->load('agent');
        
        return view('admin.rental-codes.edit', compact('rentalCode', 'agentUsers'));
    }

    /**
     * Update the specified rental code
     */
    public function update(Request $request, RentalCode $rentalCode)
    {
        $validated = $request->validate([
            'rental_code' => [
                'required',
                'string',
                Rule::unique('rental_codes', 'rental_code')->ignore($rentalCode->id)
            ],
            'rental_date' => 'required|date',
            'consultation_fee' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:Cash,Transfer',
            'property' => 'nullable|string',
            'licensor' => 'nullable|string',
            'client_full_name' => 'required|string|max:255',
            'client_date_of_birth' => 'required|date',
            'client_phone_number' => 'required|string|max:20',
            'client_email' => 'required|email|max:255',
            'client_nationality' => 'required|string|max:100',
            'client_current_address' => 'required|string',
            'client_company_university_name' => 'nullable|string|max:255',
            'client_company_university_address' => 'nullable|string',
            'client_position_role' => 'nullable|string|max:255',
            'rent_by_agent' => 'required|string|max:255',
            'client_by_agent' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:pending,approved,completed,cancelled',
        ]);

        // Handle client creation or retrieval
        $client = $this->handleClient($validated);

        // Update rental code with client_id
        $rentalCodeData = $validated;
        $rentalCodeData['client_id'] = $client->id;
        
        // Remove client fields from rental code data as they're now in the client record
        $clientFields = [
            'client_full_name', 'client_date_of_birth', 'client_phone_number',
            'client_email', 'client_nationality', 'client_current_address',
            'client_company_university_name', 'client_company_university_address',
            'client_position_role'
        ];
        
        foreach ($clientFields as $field) {
            unset($rentalCodeData[$field]);
        }

        $rentalCode->update($rentalCodeData);

        return redirect()->route('rental-codes.index')
            ->with('success', 'Rental code updated successfully!');
    }

    /**
     * Remove the specified rental code
     */
    public function destroy(RentalCode $rentalCode)
    {
        $rentalCode->delete();

        return redirect()->route('rental-codes.index')
            ->with('success', 'Rental code deleted successfully!');
    }

    /**
     * Handle client creation or retrieval
     */
    private function handleClient(array $data)
    {
        // Try to find existing client by phone number
        $client = Client::where('phone_number', $data['client_phone_number'])->first();
        
        // Prepare client data with proper date formatting
        $clientData = [
            'full_name' => $data['client_full_name'],
            'date_of_birth' => $data['client_date_of_birth'],
            'email' => $data['client_email'],
            'nationality' => $data['client_nationality'],
            'current_address' => $data['client_current_address'],
            'company_university_name' => $data['client_company_university_name'],
            'company_university_address' => $data['client_company_university_address'],
            'position_role' => $data['client_position_role'],
        ];
        
        // Add phone number only for new clients
        if (!$client) {
            $clientData['phone_number'] = $data['client_phone_number'];
        }
        
        if ($client) {
            // Log the current date of birth before update
            \Log::info("Updating client: {$client->full_name} (ID: {$client->id})");
            \Log::info("Current DOB: {$client->date_of_birth}");
            \Log::info("New DOB: {$data['client_date_of_birth']}");
            
            // Update existing client with new information
            $client->update($clientData);
            
            \Log::info("Updated existing client: {$client->full_name} (ID: {$client->id}) - New DOB: {$client->fresh()->date_of_birth}");
        } else {
            // Create new client
            $client = Client::create($clientData);
            
            \Log::info("Created new client: {$client->full_name} (ID: {$client->id}) - DOB: {$client->date_of_birth}");
        }
        
        return $client;
    }

    /**
     * Generate the next rental code
     */
    public function generateCode()
    {
        try {
            // Get the last rental code
            $lastRentalCode = RentalCode::orderBy('id', 'desc')->first();
            
            if (!$lastRentalCode) {
                // First rental code
                $nextNumber = 1;
            } else {
                // Extract number from last code (e.g., "CC0051" -> 51)
                preg_match('/CC(\d+)/', $lastRentalCode->rental_code, $matches);
                $nextNumber = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
            }
            
            // Format as CC0001, CC0002, etc.
            $newCode = 'CC' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            
            return response()->json(['code' => $newCode]);
        } catch (\Exception $e) {
            \Log::error('Error generating rental code: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate rental code'], 500);
        }
    }

    /**
     * Show agent earnings report (completely rebuilt)
     */
    public function agentEarnings(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:pending,approved,completed,cancelled'],
            'payment_method' => ['nullable', 'in:Cash,Transfer'],
            'agent_search' => ['nullable', 'string', 'max:255'],
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? now()->format('Y-m-d');
        $status = $validated['status'] ?? null;
        $paymentMethod = $validated['payment_method'] ?? null;
        $agentSearch = $validated['agent_search'] ?? null;

        // Build query with filters
        $query = RentalCode::with('client');

        if ($startDate) {
            $query->where('rental_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('rental_date', '<=', $endDate);
        }
        if ($status) {
            $query->where('status', $status);
        } else {
            $query->where('status', '!=', 'cancelled');
        }
        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $rentalCodes = $query->get();

        // Get all agent users from the users table
        $agentUsers = User::where('role', 'agent')->pluck('name', 'id')->toArray();
        $agentUserIds = User::where('role', 'agent')->pluck('id')->toArray();

        // Aggregate by agent name - only count actual agent users with proper commission structure
        $byAgent = [];
        $agencyTotal = 0.0;
        
        foreach ($rentalCodes as $code) {
            $totalFee = (float) $code->consultation_fee;
            $rentalDate = $code->rental_date;
            $paymentMethod = $code->payment_method;

            // Calculate base commission after VAT (for Transfer payments)
            $baseCommission = $totalFee;
            if ($paymentMethod === 'Transfer') {
                // Subtract 20% VAT for transfer payments
                $baseCommission = $totalFee * 0.8;
            }

            // Determine the agent (prioritize client_by_agent, fallback to rent_by_agent)
            $agentId = $code->client_by_agent ?: $code->rent_by_agent;
            $agentName = null;
            
            // Check if agent is a valid agent user
            if (is_numeric($agentId) && in_array((int)$agentId, $agentUserIds)) {
                $agentName = $agentUsers[(int)$agentId];
            } elseif (is_string($agentId)) {
                foreach ($agentUsers as $id => $name) {
                    if (strcasecmp(trim($agentId), $name) === 0) {
                        $agentName = $name;
                        break;
                    }
                }
            }
            
            
            if ($agentName) {
                if (!isset($byAgent[$agentName])) {
                    $byAgent[$agentName] = [
                        'name' => $agentName,
                        'agency_earnings' => 0.0,
                        'agent_earnings' => 0.0,
                        'total_earnings' => 0.0,
                        'transaction_count' => 0,
                        'transactions' => [],
                        'monthly_earnings' => [],
                        'avg_transaction_value' => 0.0,
                        'last_transaction_date' => null,
                        'vat_deductions' => 0.0,
                        'marketing_deductions' => 0.0,
                    ];
                }
                
                // Calculate commission split: Agency 45%, Agent 55%
                $agencyCut = $baseCommission * 0.45;
                $agentCut = $baseCommission * 0.55;
                
                // Check if this is a marketing agent scenario (different rent and client agents)
                $rentAgentId = $code->rent_by_agent;
                $clientAgentId = $code->client_by_agent;
                $isMarketingAgent = false;
                
                if ($rentAgentId && $clientAgentId && $rentAgentId !== $clientAgentId) {
                    // Check if both are valid agents and different
                    $rentAgentName = null;
                    $clientAgentName = null;
                    
                    if (is_numeric($rentAgentId) && in_array((int)$rentAgentId, $agentUserIds)) {
                        $rentAgentName = $agentUsers[(int)$rentAgentId];
                    } elseif (is_string($rentAgentId)) {
                        foreach ($agentUsers as $id => $name) {
                            if (strcasecmp(trim($rentAgentId), $name) === 0) {
                                $rentAgentName = $name;
                                break;
                            }
                        }
                    }
                    
                    if (is_numeric($clientAgentId) && in_array((int)$clientAgentId, $agentUserIds)) {
                        $clientAgentName = $agentUsers[(int)$clientAgentId];
                    } elseif (is_string($clientAgentId)) {
                        foreach ($agentUsers as $id => $name) {
                            if (strcasecmp(trim($clientAgentId), $name) === 0) {
                                $clientAgentName = $name;
                                break;
                            }
                        }
                    }
                    
                    // If both agents are valid and different, this is a marketing agent scenario
                    if ($rentAgentName && $clientAgentName && $rentAgentName !== $clientAgentName) {
                        $isMarketingAgent = true;
                    }
                }
                
                // Apply marketing agent deduction (£30 from agent's cut)
                if ($isMarketingAgent) {
                    $marketingDeduction = min(30.0, $agentCut); // Don't deduct more than agent's cut
                    $agentCut -= $marketingDeduction;
                    $byAgent[$agentName]['marketing_deductions'] += $marketingDeduction;
                }
                
                // Track VAT deductions
                if ($paymentMethod === 'Transfer') {
                    $vatAmount = $totalFee - $baseCommission;
                    $byAgent[$agentName]['vat_deductions'] += $vatAmount;
                }
                
                // Add to agent totals
                $byAgent[$agentName]['agency_earnings'] += $agencyCut;
                $byAgent[$agentName]['agent_earnings'] += $agentCut;
                $byAgent[$agentName]['total_earnings'] += $baseCommission;
                $byAgent[$agentName]['transaction_count'] += 1;
                
                $byAgent[$agentName]['transactions'][] = [
                    'total_fee' => $totalFee,
                    'base_commission' => $baseCommission,
                    'agency_cut' => $agencyCut,
                    'agent_cut' => $agentCut,
                    'vat_amount' => $paymentMethod === 'Transfer' ? ($totalFee - $baseCommission) : 0,
                    'marketing_deduction' => $isMarketingAgent ? min(30.0, $baseCommission * 0.55) : 0,
                    'date' => $rentalDate,
                    'code' => $code->rental_code,
                    'status' => $code->status,
                    'payment_method' => $paymentMethod,
                    'is_marketing_agent' => $isMarketingAgent,
                ];
                
                // Track monthly earnings
                $monthKey = $rentalDate->format('Y-m');
                if (!isset($byAgent[$agentName]['monthly_earnings'][$monthKey])) {
                    $byAgent[$agentName]['monthly_earnings'][$monthKey] = [
                        'agency' => 0,
                        'agent' => 0,
                        'total' => 0
                    ];
                }
                $byAgent[$agentName]['monthly_earnings'][$monthKey]['agency'] += $agencyCut;
                $byAgent[$agentName]['monthly_earnings'][$monthKey]['agent'] += $agentCut;
                $byAgent[$agentName]['monthly_earnings'][$monthKey]['total'] += $baseCommission;
                
                // Update last transaction date
                if (!$byAgent[$agentName]['last_transaction_date'] || $rentalDate > $byAgent[$agentName]['last_transaction_date']) {
                    $byAgent[$agentName]['last_transaction_date'] = $rentalDate;
                }
                
                // Add to agency total
                $agencyTotal += $agencyCut;
            }
        }

        // Calculate averages and apply agent search filter
        $filteredAgents = [];
        foreach ($byAgent as $agentName => $agentData) {
            // Apply agent search filter
            if ($agentSearch && stripos($agentName, $agentSearch) === false) {
                continue;
            }
            
            // Calculate average transaction value
            $agentData['avg_transaction_value'] = $agentData['total_count'] > 0 
                ? $agentData['total_earnings'] / $agentData['total_count'] 
                : 0.0;
            
            // Sort transactions by date
            usort($agentData['transactions'], function($a, $b) {
                return $b['date'] <=> $a['date'];
            });
            
            $filteredAgents[$agentName] = $agentData;
        }

        // Sort by total earnings desc
        uasort($filteredAgents, function ($a, $b) {
            return $b['total_earnings'] <=> $a['total_earnings'];
        });


        // Calculate summary statistics
        $summary = [
            'total_agents' => count($filteredAgents),
            'total_rental_codes' => $rentalCodes->count(),
            'total_earnings' => array_sum(array_map(fn($x) => $x['total_earnings'], $filteredAgents)),
            'total_agency_earnings' => array_sum(array_map(fn($x) => $x['agency_earnings'], $filteredAgents)),
            'total_agent_earnings' => array_sum(array_map(fn($x) => $x['agent_earnings'], $filteredAgents)),
            'total_transactions' => array_sum(array_map(fn($x) => $x['transaction_count'], $filteredAgents)),
            'total_vat_deductions' => array_sum(array_map(fn($x) => $x['vat_deductions'], $filteredAgents)),
            'total_marketing_deductions' => array_sum(array_map(fn($x) => $x['marketing_deductions'], $filteredAgents)),
            'avg_earnings_per_agent' => count($filteredAgents) > 0 
                ? array_sum(array_map(fn($x) => $x['total_earnings'], $filteredAgents)) / count($filteredAgents) 
                : 0,
            'top_earner' => count($filteredAgents) > 0 ? array_values($filteredAgents)[0] : null,
        ];

        // Prepare chart data
        $chartData = [
            'monthly_totals' => [],
            'agent_comparison' => array_slice($filteredAgents, 0, 10, true), // Top 10 agents
        ];

        // Calculate monthly totals - only for actual agent users with proper commission structure
        $monthlyTotals = [];
        foreach ($rentalCodes as $code) {
            $totalFee = (float) $code->consultation_fee;
            $monthKey = $code->rental_date->format('Y-m');
            $paymentMethod = $code->payment_method;
            
            // Calculate base commission after VAT (for Transfer payments)
            $baseCommission = $totalFee;
            if ($paymentMethod === 'Transfer') {
                $baseCommission = $totalFee * 0.8;
            }
            
            // Determine the agent (prioritize client_by_agent, fallback to rent_by_agent)
            $agentId = $code->client_by_agent ?: $code->rent_by_agent;
            $agentName = null;
            
            // Check if agent is a valid agent user
            if (is_numeric($agentId) && in_array((int)$agentId, $agentUserIds)) {
                $agentName = $agentUsers[(int)$agentId];
            } elseif (is_string($agentId)) {
                foreach ($agentUsers as $id => $name) {
                    if (strcasecmp(trim($agentId), $name) === 0) {
                        $agentName = $name;
                        break;
                    }
                }
            }
            
            // Only add to monthly totals if agent is valid
            if ($agentName) {
                if (!isset($monthlyTotals[$monthKey])) {
                    $monthlyTotals[$monthKey] = 0;
                }
                $monthlyTotals[$monthKey] += $baseCommission;
            }
        }
        ksort($monthlyTotals);
        $chartData['monthly_totals'] = $monthlyTotals;

        return view('admin.rental-codes.agent-earnings', [
            'agentEarnings' => $filteredAgents,
            'summary' => $summary,
            'chartData' => $chartData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
            'paymentMethod' => $paymentMethod,
            'agentSearch' => $agentSearch,
            'totalRentalCodes' => $rentalCodes->count(),
            'totalEarnings' => $summary['total_earnings'],
        ]);
    }
}
