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
        $agentUserNames = array_values($agentUsers);

        // Aggregate by agent name - only count actual agent users
        $byAgent = [];
        $agentStats = [];
        
        foreach ($rentalCodes as $code) {
            $fee = (float) $code->consultation_fee;
            $rentalDate = $code->rental_date;

            // Process rent agent - only if they are a registered agent user
            $rentAgent = trim($code->rent_by_agent_name);
            if (!empty($rentAgent) && $rentAgent !== 'N/A' && in_array($rentAgent, $agentUserNames)) {
                if (!isset($byAgent[$rentAgent])) {
                    $byAgent[$rentAgent] = [
                        'name' => $rentAgent,
                        'rent_earnings' => 0.0,
                        'client_earnings' => 0.0,
                        'total_earnings' => 0.0,
                        'rent_count' => 0,
                        'client_count' => 0,
                        'total_count' => 0,
                        'transactions' => [],
                        'monthly_earnings' => [],
                        'avg_transaction_value' => 0.0,
                        'last_transaction_date' => null,
                    ];
                }
                
                $byAgent[$rentAgent]['rent_earnings'] += $fee;
                $byAgent[$rentAgent]['rent_count'] += 1;
                $byAgent[$rentAgent]['total_earnings'] += $fee;
                $byAgent[$rentAgent]['total_count'] += 1;
                $byAgent[$rentAgent]['transactions'][] = [
                    'type' => 'rent',
                    'fee' => $fee,
                    'date' => $rentalDate,
                    'code' => $code->rental_code,
                    'status' => $code->status,
                ];
                
                // Track monthly earnings
                $monthKey = $rentalDate->format('Y-m');
                if (!isset($byAgent[$rentAgent]['monthly_earnings'][$monthKey])) {
                    $byAgent[$rentAgent]['monthly_earnings'][$monthKey] = 0;
                }
                $byAgent[$rentAgent]['monthly_earnings'][$monthKey] += $fee;
                
                // Update last transaction date
                if (!$byAgent[$rentAgent]['last_transaction_date'] || $rentalDate > $byAgent[$rentAgent]['last_transaction_date']) {
                    $byAgent[$rentAgent]['last_transaction_date'] = $rentalDate;
                }
            }

            // Process client agent - only if they are a registered agent user
            $clientAgent = trim($code->client_by_agent_name);
            if (!empty($clientAgent) && $clientAgent !== 'N/A' && in_array($clientAgent, $agentUserNames)) {
                if (!isset($byAgent[$clientAgent])) {
                    $byAgent[$clientAgent] = [
                        'name' => $clientAgent,
                        'rent_earnings' => 0.0,
                        'client_earnings' => 0.0,
                        'total_earnings' => 0.0,
                        'rent_count' => 0,
                        'client_count' => 0,
                        'total_count' => 0,
                        'transactions' => [],
                        'monthly_earnings' => [],
                        'avg_transaction_value' => 0.0,
                        'last_transaction_date' => null,
                    ];
                }
                
                $byAgent[$clientAgent]['client_earnings'] += $fee;
                $byAgent[$clientAgent]['client_count'] += 1;
                $byAgent[$clientAgent]['total_earnings'] += $fee;
                $byAgent[$clientAgent]['total_count'] += 1;
                $byAgent[$clientAgent]['transactions'][] = [
                    'type' => 'client',
                    'fee' => $fee,
                    'date' => $rentalDate,
                    'code' => $code->rental_code,
                    'status' => $code->status,
                ];
                
                // Track monthly earnings
                $monthKey = $rentalDate->format('Y-m');
                if (!isset($byAgent[$clientAgent]['monthly_earnings'][$monthKey])) {
                    $byAgent[$clientAgent]['monthly_earnings'][$monthKey] = 0;
                }
                $byAgent[$clientAgent]['monthly_earnings'][$monthKey] += $fee;
                
                // Update last transaction date
                if (!$byAgent[$clientAgent]['last_transaction_date'] || $rentalDate > $byAgent[$clientAgent]['last_transaction_date']) {
                    $byAgent[$clientAgent]['last_transaction_date'] = $rentalDate;
                }
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
            'total_rent_transactions' => array_sum(array_map(fn($x) => $x['rent_count'], $filteredAgents)),
            'total_client_transactions' => array_sum(array_map(fn($x) => $x['client_count'], $filteredAgents)),
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

        // Calculate monthly totals - only for registered agent users
        $monthlyTotals = [];
        foreach ($rentalCodes as $code) {
            $monthKey = $code->rental_date->format('Y-m');
            $rentAgent = trim($code->rent_by_agent_name);
            $clientAgent = trim($code->client_by_agent_name);
            
            // Only include if at least one agent is a registered agent user
            $hasRegisteredAgent = false;
            if ((!empty($rentAgent) && $rentAgent !== 'N/A' && in_array($rentAgent, $agentUserNames)) ||
                (!empty($clientAgent) && $clientAgent !== 'N/A' && in_array($clientAgent, $agentUserNames))) {
                $hasRegisteredAgent = true;
            }
            
            if ($hasRegisteredAgent) {
                if (!isset($monthlyTotals[$monthKey])) {
                    $monthlyTotals[$monthKey] = 0;
                }
                $monthlyTotals[$monthKey] += (float) $code->consultation_fee;
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
