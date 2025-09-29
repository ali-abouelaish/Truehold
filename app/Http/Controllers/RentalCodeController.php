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
        $agentUsers = User::where('role', 'agent')->with('agent')->get();
        
        // Get users who are marketing agents
        $marketingUsers = User::where('role', 'marketing_agent')->get();
        
        // Get existing clients for selection
        $existingClients = Client::orderBy('full_name')->get();
        
        return view('admin.rental-codes.create', compact('agentUsers', 'marketingUsers', 'existingClients'));
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
            'client_selection_type' => 'required|in:existing,new',
            'existing_client_id' => 'required_if:client_selection_type,existing|exists:clients,id',
            'client_full_name' => 'required_if:client_selection_type,new|string|max:255',
            'client_date_of_birth' => 'required_if:client_selection_type,new|date',
            'client_phone_number' => 'required_if:client_selection_type,new|string|max:20',
            'client_email' => 'required_if:client_selection_type,new|email|max:255',
            'client_nationality' => 'required_if:client_selection_type,new|string|max:100',
            'client_current_address' => 'required_if:client_selection_type,new|string',
            'client_company_university_name' => 'nullable|string|max:255',
            'client_company_university_address' => 'nullable|string',
            'client_position_role' => 'nullable|string|max:255',
            'rent_by_agent' => 'required|string|max:255',
            'marketing_agent' => 'nullable|string|max:255',
            'client_count' => 'required|integer|min:1|max:10',
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
        
        // Get users who are marketing agents
        $marketingUsers = User::where('role', 'marketing_agent')->get();
        
        // Get existing clients for selection
        $existingClients = Client::orderBy('full_name')->get();
        
        return view('admin.rental-codes.edit', compact('rentalCode', 'agentUsers', 'marketingUsers', 'existingClients'));
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
            'client_selection_type' => 'required|in:existing,new',
            'existing_client_id' => 'required_if:client_selection_type,existing|exists:clients,id',
            'client_full_name' => 'required_if:client_selection_type,new|string|max:255',
            'client_date_of_birth' => 'required_if:client_selection_type,new|date',
            'client_phone_number' => 'required_if:client_selection_type,new|string|max:20',
            'client_email' => 'required_if:client_selection_type,new|email|max:255',
            'client_nationality' => 'required_if:client_selection_type,new|string|max:100',
            'client_current_address' => 'required_if:client_selection_type,new|string',
            'client_company_university_name' => 'nullable|string|max:255',
            'client_company_university_address' => 'nullable|string',
            'client_position_role' => 'nullable|string|max:255',
            'rent_by_agent' => 'required|string|max:255',
            'marketing_agent' => 'nullable|string|max:255',
            'client_count' => 'required|integer|min:1|max:10',
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
        // If selecting existing client
        if ($data['client_selection_type'] === 'existing') {
            $client = Client::findOrFail($data['existing_client_id']);
            \Log::info("Using existing client: {$client->full_name} (ID: {$client->id})");
            return $client;
        }
        
        // If creating new client
        // Try to find existing client by phone number first
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
            'marketing_agent_filter' => ['nullable', 'string', 'in:marketing_only,rent_only,both'],
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? now()->format('Y-m-d');
        $status = $validated['status'] ?? null;
        $paymentMethod = $validated['payment_method'] ?? null;
        $agentSearch = $validated['agent_search'] ?? null;
        $marketingAgentFilter = $validated['marketing_agent_filter'] ?? null;

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

        // Get all agent users from the users table with null checks
        $agentUsers = User::where('role', 'agent')->pluck('name', 'id')->toArray();
        $agentUserIds = User::where('role', 'agent')->pluck('id')->toArray();
        $agentUserNames = array_values($agentUsers);
        
        // Debug logging
        \Log::info('Agent users found', [
            'agent_users_count' => count($agentUsers),
            'agent_users' => $agentUsers,
            'agent_user_ids' => $agentUserIds
        ]);
        
        // If no registered agents found, show all agents from rental codes
        if (empty($agentUsers)) {
            \Log::warning('No registered agents found, will show all agents from rental codes');
        }

        // Aggregate by agent name - only count actual agent users
        $byAgent = [];
        $agentStats = [];
        
        // Get current month and calculate earnings period (up to 10th of current month)
        $currentDate = now();
        $currentMonth = $currentDate->format('Y-m');
        $earningsCutoffDate = $currentDate->copy()->day(10);
        
        // If we're past the 10th, use next month's 10th as cutoff
        if ($currentDate->day > 10) {
            $earningsCutoffDate = $currentDate->copy()->addMonth()->day(10);
        }
        
        foreach ($rentalCodes as $code) {
            // Add null checks for all data
            $totalFee = (float) ($code->consultation_fee ?? 0);
            $rentalDate = $code->rental_date ?? now();
            $paymentMethod = $code->payment_method ?? 'Cash';
            
            // Skip if no consultation fee
            if ($totalFee <= 0) {
                \Log::warning('Skipping rental code with no consultation fee', [
                    'code' => $code->rental_code ?? 'N/A',
                    'consultation_fee' => $code->consultation_fee
                ]);
                continue;
            }

            // Calculate base commission after VAT (for Transfer payments)
            $baseCommission = $totalFee;
            if ($paymentMethod === 'Transfer') {
                // Subtract 20% VAT for transfer payments
                $baseCommission = $totalFee * 0.8;
            }

            // Calculate commission split: Agency 45%, Agent 55%
            $agencyCut = $baseCommission * 0.45;
            $agentCut = $baseCommission * 0.55;
            
            // Check if marketing agent is different from rental agent
            $marketingAgent = $code->marketing_agent;
            $marketingDeduction = 0;
            $marketingAgentName = null;
            $clientCount = $code->client_count ?? 1;
            
            // If marketing agent exists and is different from the rental agent
            if (!empty($marketingAgent) && $marketingAgent != $agentId) {
                // £30 for single client, £40 for multiple clients
                $marketingDeduction = $clientCount > 1 ? 40.0 : 30.0;
                $agentCut -= $marketingDeduction; // Deduct from agent
                
                // Get marketing agent name
                if (is_numeric($marketingAgent) && in_array((int)$marketingAgent, $agentUserIds)) {
                    $marketingAgentName = $agentUsers[(int)$marketingAgent] ?? null;
                } elseif (is_string($marketingAgent) && !empty(trim($marketingAgent))) {
                    foreach ($agentUsers as $id => $name) {
                        if (!empty($name) && strcasecmp(trim($marketingAgent), trim($name)) === 0) {
                            $marketingAgentName = $name;
                            break;
                        }
                    }
                }
                
                // If no marketing agent found in registered users, use the value as is
                if (empty($marketingAgentName)) {
                    $marketingAgentName = is_string($marketingAgent) ? trim($marketingAgent) : "Marketing-{$marketingAgent}";
                }
            }

            // Determine the agent (use rent_by_agent)
            $agentId = $code->rent_by_agent;
            $agentName = null;
            
            // Debug logging
            \Log::info('Processing rental code', [
                'code' => $code->rental_code ?? 'N/A',
                'rent_by_agent' => $code->rent_by_agent,
                'rent_by_agent_name' => $code->rent_by_agent_name,
                'agent_id' => $agentId,
                'agent_users_count' => count($agentUsers)
            ]);
            
            // First try to find agent by ID
            if (!empty($agentId) && is_numeric($agentId) && in_array((int)$agentId, $agentUserIds)) {
                $agentName = $agentUsers[(int)$agentId] ?? null;
                \Log::info('Found agent by ID', ['agent_id' => $agentId, 'agent_name' => $agentName]);
            } else {
                // Try to find agent by name from the rental code
                $rentAgentName = $code->rent_by_agent_name;
                
                // Prioritize client agent name
                if (!empty($clientAgentName) && in_array($clientAgentName, $agentUserNames)) {
                    $agentName = $clientAgentName;
                    \Log::info('Found agent by client name', ['agent_name' => $agentName]);
                } elseif (!empty($rentAgentName) && in_array($rentAgentName, $agentUserNames)) {
                    $agentName = $rentAgentName;
                    \Log::info('Found agent by rent name', ['agent_name' => $agentName]);
                } else {
                    // Try to match by name in agent users
                    if (!empty($clientAgentName)) {
                        foreach ($agentUsers as $id => $name) {
                            if (!empty($name) && strcasecmp(trim($clientAgentName), trim($name)) === 0) {
                                $agentName = $name;
                                \Log::info('Found agent by client name match', ['client_name' => $clientAgentName, 'agent_name' => $agentName]);
                                break;
                            }
                        }
                    }
                    
                    if (empty($agentName) && !empty($rentAgentName)) {
                        foreach ($agentUsers as $id => $name) {
                            if (!empty($name) && strcasecmp(trim($rentAgentName), trim($name)) === 0) {
                                $agentName = $name;
                                \Log::info('Found agent by rent name match', ['rent_name' => $rentAgentName, 'agent_name' => $agentName]);
                                break;
                            }
                        }
                    }
                }
            }
            
            // If no agent found, create a fallback
            if (empty($agentName)) {
                \Log::warning('No valid agent found for rental code', [
                    'code' => $code->rental_code ?? 'N/A',
                    'agent_id' => $agentId,
                    'rent_by_agent' => $code->rent_by_agent,
                    'rent_by_agent_name' => $code->rent_by_agent_name
                ]);
                
                // Create a fallback agent name for unregistered agents
                if (!empty($code->rent_by_agent_name)) {
                    $agentName = $code->rent_by_agent_name;
                } elseif (!empty($agentId)) {
                    $agentName = is_string($agentId) ? trim($agentId) : "Agent-{$agentId}";
                } else {
                    $agentName = "Unknown Agent";
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
                        'marketing_agent_earnings' => 0.0,
                        'paid_amount' => 0.0,
                        'entitled_amount' => 0.0,
                        'outstanding_amount' => 0.0,
                    ];
                }
                
                // Add to agent totals
                $byAgent[$agentName]['agency_earnings'] += $agencyCut;
                $byAgent[$agentName]['agent_earnings'] += $agentCut;
                $byAgent[$agentName]['total_earnings'] += $baseCommission;
                $byAgent[$agentName]['transaction_count'] += 1;
                
                // Track VAT deductions
                if ($paymentMethod === 'Transfer') {
                    $vatAmount = $totalFee - $baseCommission;
                    $byAgent[$agentName]['vat_deductions'] += $vatAmount;
                }
                
                // Track marketing deductions
                if ($marketingDeduction > 0) {
                    $byAgent[$agentName]['marketing_deductions'] += $marketingDeduction;
                }
                
                // Track paid amounts and entitled amounts
                $isPaid = $code->paid ?? false;
                $agentEarnings = $agentCut; // Agent's portion of earnings
                
                if ($isPaid) {
                    $byAgent[$agentName]['paid_amount'] += $agentEarnings;
                } else {
                    $byAgent[$agentName]['entitled_amount'] += $agentEarnings;
                }
                
                // Calculate outstanding amount (never negative)
                $byAgent[$agentName]['outstanding_amount'] = max(0, $byAgent[$agentName]['entitled_amount'] - $byAgent[$agentName]['paid_amount']);
                
                $byAgent[$agentName]['transactions'][] = [
                    'total_fee' => $totalFee,
                    'base_commission' => $baseCommission,
                    'agency_cut' => $agencyCut,
                    'agent_cut' => $agentCut,
                    'vat_amount' => $paymentMethod === 'Transfer' ? ($totalFee - $baseCommission) : 0,
                    'marketing_deduction' => $marketingDeduction,
                    'marketing_agent' => $marketingAgentName,
                    'client_count' => $clientCount,
                    'paid' => $isPaid,
                    'paid_at' => $code->paid_at,
                    'date' => $rentalDate,
                    'code' => $code->rental_code ?? 'N/A',
                    'status' => $code->status ?? 'Unknown',
                    'payment_method' => $paymentMethod,
                ];
                
                // Track monthly earnings
                $monthKey = $rentalDate->format('Y-m');
                if (!isset($byAgent[$agentName]['monthly_earnings'][$monthKey])) {
                    $byAgent[$agentName]['monthly_earnings'][$monthKey] = 0;
                }
                $byAgent[$agentName]['monthly_earnings'][$monthKey] += $baseCommission;
                
                // Update last transaction date
                if (!$byAgent[$agentName]['last_transaction_date'] || $rentalDate > $byAgent[$agentName]['last_transaction_date']) {
                    $byAgent[$agentName]['last_transaction_date'] = $rentalDate;
                }
            }
            
            // Handle marketing agent earnings (only if different from rental agent)
            if ($marketingDeduction > 0 && $marketingAgentName && $marketingAgentName !== $agentName) {
                if (!isset($byAgent[$marketingAgentName])) {
                    $byAgent[$marketingAgentName] = [
                        'name' => $marketingAgentName,
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
                        'marketing_agent_earnings' => 0.0,
                        'paid_amount' => 0.0,
                        'entitled_amount' => 0.0,
                        'outstanding_amount' => 0.0,
                    ];
                }
                
                // Add marketing agent earnings
                $byAgent[$marketingAgentName]['marketing_agent_earnings'] += $marketingDeduction;
                $byAgent[$marketingAgentName]['total_earnings'] += $marketingDeduction;
                
                // Only increment transaction count if this is a different agent
                if ($marketingAgentName !== $agentName) {
                    $byAgent[$marketingAgentName]['transaction_count'] += 1;
                }
                
                // Track paid amounts for marketing agent
                $marketingIsPaid = $code->paid ?? false;
                if ($marketingIsPaid) {
                    $byAgent[$marketingAgentName]['paid_amount'] += $marketingDeduction;
                } else {
                    $byAgent[$marketingAgentName]['entitled_amount'] += $marketingDeduction;
                }
                
                // Calculate outstanding amount for marketing agent (never negative)
                $byAgent[$marketingAgentName]['outstanding_amount'] = max(0, $byAgent[$marketingAgentName]['entitled_amount'] - $byAgent[$marketingAgentName]['paid_amount']);
                
                $byAgent[$marketingAgentName]['transactions'][] = [
                    'total_fee' => $totalFee,
                    'base_commission' => $marketingDeduction,
                    'agency_cut' => 0,
                    'agent_cut' => $marketingDeduction,
                    'vat_amount' => 0,
                    'marketing_deduction' => 0,
                    'marketing_agent' => $marketingAgentName,
                    'client_count' => $clientCount,
                    'paid' => $marketingIsPaid,
                    'paid_at' => $code->paid_at,
                    'date' => $rentalDate,
                    'code' => $code->rental_code ?? 'N/A',
                    'status' => $code->status ?? 'Unknown',
                    'payment_method' => $paymentMethod,
                    'is_marketing_earnings' => true,
                ];
                
                // Track monthly earnings for marketing agent
                $monthKey = $rentalDate->format('Y-m');
                if (!isset($byAgent[$marketingAgentName]['monthly_earnings'][$monthKey])) {
                    $byAgent[$marketingAgentName]['monthly_earnings'][$monthKey] = 0;
                }
                $byAgent[$marketingAgentName]['monthly_earnings'][$monthKey] += $marketingDeduction;
                
                // Update last transaction date for marketing agent
                if (!$byAgent[$marketingAgentName]['last_transaction_date'] || $rentalDate > $byAgent[$marketingAgentName]['last_transaction_date']) {
                    $byAgent[$marketingAgentName]['last_transaction_date'] = $rentalDate;
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
            
            // Apply marketing agent filter
            if ($marketingAgentFilter) {
                $hasMarketingEarnings = $agentData['marketing_agent_earnings'] > 0;
                $hasRentEarnings = $agentData['agent_earnings'] > 0;
                
                if ($marketingAgentFilter === 'marketing_only' && !$hasMarketingEarnings) {
                    continue;
                } elseif ($marketingAgentFilter === 'rent_only' && !$hasRentEarnings) {
                    continue;
                } elseif ($marketingAgentFilter === 'both' && (!$hasMarketingEarnings || !$hasRentEarnings)) {
                    continue;
                }
            }
            
            // Calculate average transaction value
            $agentData['avg_transaction_value'] = $agentData['transaction_count'] > 0 
                ? $agentData['total_earnings'] / $agentData['transaction_count'] 
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
            'total_marketing_earnings' => array_sum(array_map(fn($x) => $x['marketing_agent_earnings'], $filteredAgents)),
            'total_marketing_deductions' => array_sum(array_map(fn($x) => $x['marketing_deductions'], $filteredAgents)),
            'total_paid_amount' => array_sum(array_map(fn($x) => $x['paid_amount'], $filteredAgents)),
            'total_entitled_amount' => array_sum(array_map(fn($x) => $x['entitled_amount'], $filteredAgents)),
            'total_outstanding_amount' => array_sum(array_map(fn($x) => $x['outstanding_amount'], $filteredAgents)),
            'total_transactions' => array_sum(array_map(fn($x) => $x['transaction_count'], $filteredAgents)),
            'total_vat_deductions' => array_sum(array_map(fn($x) => $x['vat_deductions'], $filteredAgents)),
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

        // Calculate monthly totals - only for registered agent users with proper commission structure
        $monthlyTotals = [];
        foreach ($rentalCodes as $code) {
            $totalFee = (float) ($code->consultation_fee ?? 0);
            $rentalDate = $code->rental_date ?? now();
            $monthKey = $rentalDate->format('Y-m');
            $paymentMethod = $code->payment_method ?? 'Cash';
            
            // Skip if no consultation fee
            if ($totalFee <= 0) {
                continue;
            }
            
            // Calculate base commission after VAT (for Transfer payments)
            $baseCommission = $totalFee;
            if ($paymentMethod === 'Transfer') {
                $baseCommission = $totalFee * 0.8;
            }
            
            // Determine the agent (use rent_by_agent)
            $agentId = $code->rent_by_agent;
            $agentName = null;
            
            // First try to find agent by ID
            if (!empty($agentId) && is_numeric($agentId) && in_array((int)$agentId, $agentUserIds)) {
                $agentName = $agentUsers[(int)$agentId] ?? null;
            } else {
                // Try to find agent by name from the rental code
                $rentAgentName = $code->rent_by_agent_name;
                
                // Prioritize client agent name
                if (!empty($clientAgentName) && in_array($clientAgentName, $agentUserNames)) {
                    $agentName = $clientAgentName;
                } elseif (!empty($rentAgentName) && in_array($rentAgentName, $agentUserNames)) {
                    $agentName = $rentAgentName;
                } else {
                    // Try to match by name in agent users
                    if (!empty($clientAgentName)) {
                        foreach ($agentUsers as $id => $name) {
                            if (!empty($name) && strcasecmp(trim($clientAgentName), trim($name)) === 0) {
                                $agentName = $name;
                                break;
                            }
                        }
                    }
                    
                    if (empty($agentName) && !empty($rentAgentName)) {
                        foreach ($agentUsers as $id => $name) {
                            if (!empty($name) && strcasecmp(trim($rentAgentName), trim($name)) === 0) {
                                $agentName = $name;
                                break;
                            }
                        }
                    }
                }
            }
            
            // Create fallback agent name if needed
            if (empty($agentName)) {
                if (!empty($code->rent_by_agent_name)) {
                    $agentName = $code->rent_by_agent_name;
                } elseif (!empty($agentId)) {
                    $agentName = is_string($agentId) ? trim($agentId) : "Agent-{$agentId}";
                } else {
                    $agentName = "Unknown Agent";
                }
            }
            
            // Add to monthly totals
            if ($agentName) {
                if (!isset($monthlyTotals[$monthKey])) {
                    $monthlyTotals[$monthKey] = 0;
                }
                $monthlyTotals[$monthKey] += $baseCommission;
            }
        }
        ksort($monthlyTotals);
        $chartData['monthly_totals'] = $monthlyTotals;

        // Final debug logging
        \Log::info('Agent earnings summary', [
            'total_rental_codes' => $rentalCodes->count(),
            'agents_found' => count($filteredAgents),
            'agent_names' => array_keys($filteredAgents),
            'monthly_totals' => $monthlyTotals
        ]);

        return view('admin.rental-codes.agent-earnings', [
            'agentEarnings' => $filteredAgents,
            'summary' => $summary,
            'chartData' => $chartData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
            'paymentMethod' => $paymentMethod,
            'agentSearch' => $agentSearch,
            'marketingAgentFilter' => $marketingAgentFilter,
            'totalRentalCodes' => $rentalCodes->count(),
            'totalEarnings' => $summary['total_earnings'],
        ]);
    }

    /**
     * Mark a rental code as paid
     */
    public function markAsPaid(RentalCode $rentalCode)
    {
        try {
            \Log::info('Marking rental code as paid', ['rental_code_id' => $rentalCode->id]);
            
            $rentalCode->update([
                'paid' => true,
                'paid_at' => now(),
            ]);

            \Log::info('Rental code marked as paid successfully', ['rental_code_id' => $rentalCode->id]);

            return response()->json([
                'success' => true,
                'message' => 'Rental code marked as paid successfully',
                'paid_at' => $rentalCode->paid_at->format('d M Y H:i'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking rental code as paid', [
                'rental_code_id' => $rentalCode->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error marking rental code as paid: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark a rental code as unpaid
     */
    public function markAsUnpaid(RentalCode $rentalCode)
    {
        try {
            \Log::info('Marking rental code as unpaid', ['rental_code_id' => $rentalCode->id]);
            
            $rentalCode->update([
                'paid' => false,
                'paid_at' => null,
            ]);

            \Log::info('Rental code marked as unpaid successfully', ['rental_code_id' => $rentalCode->id]);

            return response()->json([
                'success' => true,
                'message' => 'Rental code marked as unpaid successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking rental code as unpaid', [
                'rental_code_id' => $rentalCode->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error marking rental code as unpaid: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update rental code status
     */
    public function updateStatus(Request $request, RentalCode $rentalCode)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|string|in:pending,approved,completed,cancelled',
            ]);

            \Log::info('Updating rental code status', [
                'rental_code_id' => $rentalCode->id,
                'old_status' => $rentalCode->status,
                'new_status' => $validated['status']
            ]);

            $rentalCode->update([
                'status' => $validated['status'],
            ]);

            \Log::info('Rental code status updated successfully', [
                'rental_code_id' => $rentalCode->id,
                'new_status' => $rentalCode->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rental code status updated successfully',
                'new_status' => $rentalCode->status,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating rental code status', [
                'rental_code_id' => $rentalCode->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating rental code status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show detailed agent earnings page
     */
    public function agentDetails(Request $request, $agentName)
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:pending,approved,completed,cancelled'],
            'payment_method' => ['nullable', 'in:Cash,Transfer'],
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? now()->format('Y-m-d');
        $status = $validated['status'] ?? null;
        $paymentMethod = $validated['payment_method'] ?? null;

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

        // Filter rentals for this specific agent
        $agentRentals = $rentalCodes->filter(function ($code) use ($agentName) {
            $rentAgentName = $code->rent_by_agent_name;
            
            return $rentAgentName === $agentName;
        });

        // Calculate agent statistics
        $totalEarnings = 0;
        $paidAmount = 0;
        $outstandingAmount = 0;
        $totalTransactions = $agentRentals->count();
        $paidTransactions = $agentRentals->where('paid', true)->count();
        $unpaidTransactions = $totalTransactions - $paidTransactions;

        foreach ($agentRentals as $code) {
            $totalFee = (float) ($code->consultation_fee ?? 0);
            $paymentMethod = $code->payment_method ?? 'Cash';
            $clientCount = $code->client_count ?? 1;
            
            // Calculate base commission after VAT
            $baseCommission = $totalFee;
            if ($paymentMethod === 'Transfer') {
                $baseCommission = $totalFee * 0.8;
            }

            // Calculate agent earnings (55% of base commission)
            $agentEarnings = $baseCommission * 0.55;
            
            // Check for marketing deduction and marketing earnings
            $marketingAgent = $code->marketing_agent;
            $agentId = $code->rent_by_agent;
            
            if (!empty($marketingAgent) && $marketingAgent != $agentId) {
                $marketingDeduction = $clientCount > 1 ? 40.0 : 30.0;
                $agentEarnings -= $marketingDeduction;
            }
            
            // No extra marketing earnings if agent is both rent and marketing agent
            // (They already get the full commission, no need for extra marketing money)
            
            $totalEarnings += $agentEarnings;
            
            if ($code->paid) {
                $paidAmount += $agentEarnings;
            } else {
                $outstandingAmount += $agentEarnings;
            }
        }

        // Performance metrics
        $performanceMetrics = [
            'total_earnings' => $totalEarnings,
            'paid_amount' => $paidAmount,
            'outstanding_amount' => max(0, $outstandingAmount),
            'total_transactions' => $totalTransactions,
            'paid_transactions' => $paidTransactions,
            'unpaid_transactions' => $unpaidTransactions,
            'payment_rate' => $totalTransactions > 0 ? ($paidTransactions / $totalTransactions) * 100 : 0,
            'avg_earnings_per_transaction' => $totalTransactions > 0 ? $totalEarnings / $totalTransactions : 0,
        ];

        // Monthly breakdown
        $monthlyBreakdown = [];
        foreach ($agentRentals as $code) {
            $monthKey = $code->rental_date->format('Y-m');
            if (!isset($monthlyBreakdown[$monthKey])) {
                $monthlyBreakdown[$monthKey] = [
                    'month' => $monthKey,
                    'total_earnings' => 0,
                    'paid_amount' => 0,
                    'outstanding_amount' => 0,
                    'transaction_count' => 0,
                ];
            }
            
            $totalFee = (float) ($code->consultation_fee ?? 0);
            $paymentMethod = $code->payment_method ?? 'Cash';
            $clientCount = $code->client_count ?? 1;
            
            $baseCommission = $totalFee;
            if ($paymentMethod === 'Transfer') {
                $baseCommission = $totalFee * 0.8;
            }

            $agentEarnings = $baseCommission * 0.55;
            
            $marketingAgent = $code->marketing_agent;
            $agentId = $code->rent_by_agent;
            
            if (!empty($marketingAgent) && $marketingAgent != $agentId) {
                $marketingDeduction = $clientCount > 1 ? 40.0 : 30.0;
                $agentEarnings -= $marketingDeduction;
            }
            
            $monthlyBreakdown[$monthKey]['total_earnings'] += $agentEarnings;
            $monthlyBreakdown[$monthKey]['transaction_count'] += 1;
            
            if ($code->paid) {
                $monthlyBreakdown[$monthKey]['paid_amount'] += $agentEarnings;
            } else {
                $monthlyBreakdown[$monthKey]['outstanding_amount'] += $agentEarnings;
            }
        }

        ksort($monthlyBreakdown);

        return view('admin.rental-codes.agent-details', [
            'agentName' => $agentName,
            'agentRentals' => $agentRentals,
            'performanceMetrics' => $performanceMetrics,
            'monthlyBreakdown' => $monthlyBreakdown,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
            'paymentMethod' => $paymentMethod,
        ]);
    }

    /**
     * Get rental code details as JSON
     */
    public function getRentalDetails(RentalCode $rentalCode)
    {
        \Log::info('Getting rental details', ['rental_code_id' => $rentalCode->id]);
        
        try {
            $data = [
            'id' => $rentalCode->id,
            'rental_code' => $rentalCode->rental_code,
            'rental_date' => $rentalCode->rental_date,
            'consultation_fee' => $rentalCode->consultation_fee,
            'payment_method' => $rentalCode->payment_method,
            'property' => $rentalCode->property,
            'licensor' => $rentalCode->licensor,
            'status' => $rentalCode->status,
            'paid' => $rentalCode->paid,
            'paid_at' => $rentalCode->paid_at,
            'notes' => $rentalCode->notes,
            'rent_by_agent_name' => $rentalCode->rent_by_agent_name,
            'marketing_agent_name' => $rentalCode->marketing_agent_name,
            'client_count' => $rentalCode->client_count,
            'client' => $rentalCode->client ? [
                'id' => $rentalCode->client->id,
                'full_name' => $rentalCode->client->full_name,
                'email' => $rentalCode->client->email,
                'phone' => $rentalCode->client->phone,
            ] : null,
        ];
        
        \Log::info('Rental details data prepared', ['data' => $data]);
        
        return response()->json($data);
        
        } catch (\Exception $e) {
            \Log::error('Error getting rental details', [
                'rental_code_id' => $rentalCode->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to get rental details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show marketing agents management page
     */
    public function marketingAgents()
    {
        $marketingAgents = User::where('role', 'marketing_agent')->get();
        $allUsers = User::whereIn('role', ['agent', 'user'])->get();
        
        return view('admin.marketing-agents.index', compact('marketingAgents', 'allUsers'));
    }

    /**
     * Store a new marketing agent
     */
    public function storeMarketingAgent(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->update(['role' => 'marketing_agent']);

        return redirect()->route('marketing-agents.index')
            ->with('success', 'User has been assigned as a marketing agent.');
    }

    /**
     * Remove marketing agent role
     */
    public function removeMarketingAgent(User $user)
    {
        $user->update(['role' => 'user']);
        
        return redirect()->route('marketing-agents.index')
            ->with('success', 'Marketing agent role has been removed.');
    }
}
