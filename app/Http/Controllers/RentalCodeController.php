<?php

namespace App\Http\Controllers;

use App\Models\RentalCode;
use App\Models\Agent;
use App\Models\User;
use App\Models\Client;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $marketingUsers = User::where('role', 'marketing_agent')->orWhereJsonContains('roles', 'marketing_agent')->get();
        
        // Get existing clients for selection
        $existingClients = Client::orderBy('full_name')->get();
        
        return view('admin.rental-codes.create', compact('agentUsers', 'marketingUsers', 'existingClients'));
    }

    /**
     * Store a newly created rental code
     */
    public function store(Request $request)
    {
        \Log::info('Rental code store method called', [
            'has_files' => $request->hasFile('client_contract') || $request->hasFile('payment_proof') || $request->hasFile('client_id_document'),
            'client_contract_files' => $request->hasFile('client_contract') ? count($request->file('client_contract')) : 0,
            'payment_proof_files' => $request->hasFile('payment_proof') ? count($request->file('payment_proof')) : 0,
            'client_id_document_files' => $request->hasFile('client_id_document') ? count($request->file('client_id_document')) : 0
        ]);
        
        // Build validation rules dynamically
        $validationRules = [
            'rental_code' => 'nullable|string|unique:rental_codes,rental_code',
            'rental_date' => 'required|date',
            'consultation_fee' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:Cash,Transfer,Card machine',
            'property' => 'required|string',
            'licensor' => 'required|string',
            'client_selection_type' => 'required|in:existing,new',
            'existing_client_id' => 'required_if:client_selection_type,existing|exists:clients,id',
            'rental_agent_id' => 'required|exists:users,id',
            'marketing_agent_id' => 'nullable|exists:users,id',
            'client_count' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string',
            // Document upload validation
            'client_contract.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'payment_proof.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'client_id_document.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
        
        // Add dynamic client validation if creating new clients
        if ($request->input('client_selection_type') === 'new') {
            $clientCount = $request->input('client_count', 1);
            for ($i = 1; $i <= $clientCount; $i++) {
                $validationRules["client_{$i}_full_name"] = 'required|string|max:255';
                $validationRules["client_{$i}_date_of_birth"] = 'required|date';
                $validationRules["client_{$i}_phone_number"] = 'required|string|max:20';
                $validationRules["client_{$i}_email"] = 'required|email|max:255';
                $validationRules["client_{$i}_nationality"] = 'required|string|max:100';
                $validationRules["client_{$i}_current_address"] = 'required|string';
                $validationRules["client_{$i}_company_university_name"] = 'required|string|max:255';
                $validationRules["client_{$i}_company_university_address"] = 'required|string';
                $validationRules["client_{$i}_position_role"] = 'required|string|max:255';
                $validationRules["client_{$i}_current_landlord_name"] = 'required|string|max:255';
                $validationRules["client_{$i}_current_landlord_contact_info"] = 'required|string';
            }
        }
        
        // Build custom error messages
        $customMessages = [
            'rental_code.required' => 'Rental code is required.',
            'rental_code.unique' => 'This rental code already exists.',
            'rental_date.required' => 'Rental date is required.',
            'consultation_fee.required' => 'Consultation fee is required.',
            'payment_method.required' => 'Payment method is required.',
            'property.required' => 'Property is required.',
            'licensor.required' => 'Licensor is required.',
            'client_selection_type.required' => 'Please select whether to use an existing client or create a new one.',
            'existing_client_id.required_if' => 'Please select an existing client.',
            'rental_agent_id.required' => 'Rental agent is required.',
            'rental_agent_id.exists' => 'Selected rental agent is invalid.',
            'marketing_agent_id.exists' => 'Selected marketing agent is invalid.',
            'client_count.required' => 'Client count is required.',
            'status.required' => 'Status is required.',
        ];
        
        // Add dynamic client error messages
        if ($request->input('client_selection_type') === 'new') {
            $clientCount = $request->input('client_count', 1);
            for ($i = 1; $i <= $clientCount; $i++) {
                $customMessages["client_{$i}_full_name.required"] = "Client {$i} full name is required.";
                $customMessages["client_{$i}_date_of_birth.required"] = "Client {$i} date of birth is required.";
                $customMessages["client_{$i}_phone_number.required"] = "Client {$i} phone number is required.";
                $customMessages["client_{$i}_email.required"] = "Client {$i} email is required.";
                $customMessages["client_{$i}_nationality.required"] = "Client {$i} nationality is required.";
                $customMessages["client_{$i}_current_address.required"] = "Client {$i} current address is required.";
                $customMessages["client_{$i}_company_university_name.required"] = "Client {$i} company/university name is required.";
                $customMessages["client_{$i}_company_university_address.required"] = "Client {$i} company/university address is required.";
                $customMessages["client_{$i}_position_role.required"] = "Client {$i} position/role is required.";
                $customMessages["client_{$i}_current_landlord_name.required"] = "Client {$i} current landlord name is required.";
                $customMessages["client_{$i}_current_landlord_contact_info.required"] = "Client {$i} current landlord contact info is required.";
            }
        }
        
        $validated = $request->validate($validationRules, $customMessages);

        // Auto-generate rental code if not provided
        if (empty($validated['rental_code'])) {
            $validated['rental_code'] = RentalCode::generateRentalCode();
        }

        // Handle client creation or retrieval
        if ($validated['client_selection_type'] === 'existing') {
            $client = Client::find($validated['existing_client_id']);
        } else {
            // Handle multiple new clients
            $clients = $this->handleMultipleClients($validated);
            $client = $clients->first(); // Use first client as primary
        }

        // Create rental code with client_id
        $rentalCodeData = $validated;
        $rentalCodeData['client_id'] = $client->id;
        
        // Set the rental agent ID (use current user if not specified)
        if (empty($rentalCodeData['rental_agent_id'])) {
            $currentUser = auth()->user();
            if ($currentUser) {
                $rentalCodeData['rental_agent_id'] = $currentUser->id;
            }
        }
        
        // Set the rent_by_agent display name for backward compatibility
        $rentalAgent = User::find($rentalCodeData['rental_agent_id']);
        if ($rentalAgent) {
            if ($rentalAgent->agent && $rentalAgent->agent->company_name) {
                $rentalCodeData['rent_by_agent'] = $rentalAgent->agent->company_name;
            } else {
                $rentalCodeData['rent_by_agent'] = $rentalAgent->name;
            }
        } else {
            $rentalCodeData['rent_by_agent'] = 'Unknown Agent';
        }
        
        // Set default status to pending for new rental codes
        $rentalCodeData['status'] = 'pending';
        
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
        
        // Remove file upload fields from rental code data as they're handled separately
        $fileFields = ['client_contract', 'payment_proof', 'client_id_document'];
        foreach ($fileFields as $field) {
            unset($rentalCodeData[$field]);
        }

        $rentalCode = RentalCode::create($rentalCodeData);
        
        \Log::info('Rental code created, about to handle file uploads', [
            'rental_code_id' => $rentalCode->id,
            'rental_code' => $rentalCode->rental_code
        ]);

        // Handle file uploads
        $this->handleFileUploads($request, $rentalCode);
        
        \Log::info('File upload handling completed', [
            'rental_code_id' => $rentalCode->id
        ]);

        // Send email notification to board@truehold.co.uk
        $this->sendRentalCodeNotification($rentalCode);

        // Send WhatsApp notifications
        $this->sendWhatsAppNotifications($rentalCode, $client);

        return redirect()->route('rental-codes.index')
            ->with('success', 'Rental code created successfully and notifications sent!');
    }

    /**
     * Display the specified rental code
     */
    public function show(RentalCode $rentalCode)
    {
        $rentalCode->load(['client', 'marketingAgentUser']);
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
        $marketingUsers = User::where('role', 'marketing_agent')->orWhereJsonContains('roles', 'marketing_agent')->get();
        
        // Get existing clients for selection
        $existingClients = Client::orderBy('full_name')->get();
        
        return view('admin.rental-codes.edit', compact('rentalCode', 'agentUsers', 'marketingUsers', 'existingClients'));
    }

    /**
     * Update the specified rental code
     */
    public function update(Request $request, RentalCode $rentalCode)
    {
        // Build validation rules based on user role
        $validationRules = [
            'rental_code' => [
                'required',
                'string',
                Rule::unique('rental_codes', 'rental_code')->ignore($rentalCode->id)
            ],
            'rental_date' => 'required|date',
            'consultation_fee' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:Cash,Transfer,Card machine',
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
        ];

        // Only admin users can change status
        if (auth()->user()->role === 'admin') {
            $validationRules['status'] = 'required|string|in:pending,approved,paid';
        }

        $validated = $request->validate($validationRules);

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
            'current_landlord_name' => $data['client_current_landlord_name'] ?? null,
            'current_landlord_contact_info' => $data['client_current_landlord_contact_info'] ?? null,
        ];
        
        // Add phone number and assign current user as agent for new clients
        if (!$client) {
            $clientData['phone_number'] = $data['client_phone_number'];
            // Assign current user as the agent for new clients
            $currentUser = auth()->user();
            if ($currentUser) {
                // Check if user has an agent profile
                if ($currentUser->agent) {
                    $clientData['agent_id'] = $currentUser->agent->id;
                } else {
                    // If user doesn't have agent profile but has agent role, create one or find existing
                    if ($currentUser->hasRole('agent')) {
                        // Try to find or create an agent profile for this user
                        $agent = \App\Models\Agent::firstOrCreate(
                            ['user_id' => $currentUser->id],
                            [
                                'company_name' => $currentUser->name,
                                'is_verified' => false,
                                'is_featured' => false,
                            ]
                        );
                        $clientData['agent_id'] = $agent->id;
                    }
                }
            }
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
     * Handle multiple client creation
     */
    private function handleMultipleClients(array $data)
    {
        $clients = collect();
        $clientCount = $data['client_count'] ?? 1;
        
        for ($i = 1; $i <= $clientCount; $i++) {
            $clientData = [
                'full_name' => $data["client_{$i}_full_name"] ?? '',
                'date_of_birth' => $data["client_{$i}_date_of_birth"] ?? null,
                'phone_number' => $data["client_{$i}_phone_number"] ?? '',
                'email' => $data["client_{$i}_email"] ?? '',
                'nationality' => $data["client_{$i}_nationality"] ?? '',
                'current_address' => $data["client_{$i}_current_address"] ?? '',
                'company_university_name' => $data["client_{$i}_company_university_name"] ?? '',
                'company_university_address' => $data["client_{$i}_company_university_address"] ?? '',
                'position_role' => $data["client_{$i}_position_role"] ?? '',
                'current_landlord_name' => $data["client_{$i}_current_landlord_name"] ?? null,
                'current_landlord_contact_info' => $data["client_{$i}_current_landlord_contact_info"] ?? null,
            ];
            
            // Assign current user as the agent for new clients
            $currentUser = auth()->user();
            if ($currentUser) {
                if ($currentUser->agent) {
                    $clientData['agent_id'] = $currentUser->agent->id;
                } else {
                    if ($currentUser->hasRole('agent')) {
                        $agent = \App\Models\Agent::firstOrCreate(
                            ['user_id' => $currentUser->id],
                            [
                                'company_name' => $currentUser->name,
                                'is_verified' => false,
                                'is_featured' => false,
                            ]
                        );
                        $clientData['agent_id'] = $agent->id;
                    }
                }
            }
            
            $client = Client::create($clientData);
            $clients->push($client);
            
            \Log::info("Created client {$i}: {$client->full_name} (ID: {$client->id})");
        }
        
        return $clients;
    }

    /**
     * Generate the next rental code
     */
public function generateCode()
    {
        try {
            \Log::info('GenerateCode method called');
            
            // Get the last rental code
            $lastRentalCode = RentalCode::orderBy('id', 'desc')->first();
            \Log::info('Last rental code: ' . ($lastRentalCode ? $lastRentalCode->rental_code : 'None'));
            
            if (!$lastRentalCode) {
                // First rental code starts from CC0121
                $nextNumber = 121;
                \Log::info('No existing rental codes, starting from 121');
            } else {
                // Extract number from last code (e.g., "CC0121" -> 121)
                preg_match('/CC(\d+)/', $lastRentalCode->rental_code, $matches);
                if (isset($matches[1])) {
                    $lastNumber = (int)$matches[1];
                    // If the last number is less than 121, start from 121
                    $nextNumber = $lastNumber >= 121 ? $lastNumber + 1 : 121;
                    \Log::info('Last number: ' . $lastNumber . ', next number: ' . $nextNumber);
                } else {
                    // If no valid number found, start from 121
                    $nextNumber = 121;
                    \Log::info('No valid number found, starting from 121');
                }
            }
            
            // Format as CC0121, CC0122, etc.
            $newCode = 'CC' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            \Log::info('Generated code: ' . $newCode);
            
            return response()->json(['code' => $newCode]);
        } catch (\Exception $e) {
            \Log::error('Error generating rental code: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate rental code: ' . $e->getMessage()], 500);
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

        // Authorization: Check if user can view payroll
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';
        
        // If user is not admin and trying to view specific agent payroll
        if (!empty($agentSearch) && !$isAdmin) {
            // Check if the agent name matches the current user's name
            if ($agentSearch !== $user->name) {
                return redirect()->route('rental-codes.agent-earnings')
                    ->with('error', 'You can only view your own payroll.');
            }
        }
        
        // If user is not admin and no specific agent selected, show only their own data
        if (empty($agentSearch) && !$isAdmin) {
            $agentSearch = $user->name;
        }

        // Check if we're viewing a specific agent's payroll
        $isPayrollView = !empty($agentSearch);
        
        // Build query with filters
        $query = RentalCode::with('client');

        if ($startDate) {
            $query->where('rental_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('rental_date', '<=', $endDate);
        }
        
        // For payroll view, only show approved rentals for the specific agent
        if ($isPayrollView) {
            $query->where('status', 'approved');
            
            // Get the agent user ID for filtering
            $agentUser = User::where('role', 'agent')->where('name', $agentSearch)->first();
            
            if ($agentUser) {
                // Filter by foreign key fields
                $query->where(function($q) use ($agentUser) {
                    $q->where('rental_agent_id', $agentUser->id)
                      ->orWhere('marketing_agent_id', $agentUser->id);
                });
            } else {
                // If agent not found, return empty result
                $query->whereRaw('1 = 0'); // This will return no results
            }
        } else {
            // For general earnings view, exclude cancelled
            if ($status) {
                $query->where('status', $status);
            } else {
                $query->where('status', '!=', 'cancelled');
            }
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
            if (!empty($marketingAgent) && $marketingAgent != $code->rent_by_agent) {
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
                
                // If no marketing agent found in registered users, try to resolve by ID
                if (empty($marketingAgentName)) {
                    if (is_numeric($marketingAgent)) {
                        // Try to get user name by ID
                        $user = \App\Models\User::find((int)$marketingAgent);
                        $marketingAgentName = $user ? $user->name : "Marketing-{$marketingAgent}";
                    } else {
                        $marketingAgentName = is_string($marketingAgent) ? trim($marketingAgent) : "Marketing-{$marketingAgent}";
                    }
                }
            }

            // Determine the agent (use rent_by_agent)
            $agentId = $code->rent_by_agent;
            $agentName = null;
            
            // Get client agent name if available
            $clientAgentName = $code->client && $code->client->agent ? $code->client->agent->company_name : null;
            $rentAgentName = $code->rent_by_agent_name;
            
            // Debug logging
            \Log::info('Processing rental code', [
                'code' => $code->rental_code ?? 'N/A',
                'rent_by_agent' => $code->rent_by_agent,
                'rent_by_agent_name' => $rentAgentName,
                'client_agent_name' => $clientAgentName,
                'agent_id' => $agentId,
                'agent_users_count' => count($agentUsers)
            ]);
            
            // First try to find agent by ID
            if (!empty($agentId) && is_numeric($agentId) && in_array((int)$agentId, $agentUserIds)) {
                $agentName = $agentUsers[(int)$agentId] ?? null;
                \Log::info('Found agent by ID', ['agent_id' => $agentId, 'agent_name' => $agentName]);
            } else {
                // Try to find agent by name from the rental code
                
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
                    if (is_numeric($agentId)) {
                        // Try to get user name by ID
                        $user = \App\Models\User::find((int)$agentId);
                        $agentName = $user ? $user->name : "Agent-{$agentId}";
                    } else {
                        $agentName = is_string($agentId) ? trim($agentId) : "Agent-{$agentId}";
                    }
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
            // For payroll view, only show the selected agent (exact match)
            if ($isPayrollView && $agentSearch && $agentName !== $agentSearch) {
                continue;
            }
            
            // For general earnings view, apply agent search filter (substring match)
            if (!$isPayrollView && $agentSearch && stripos($agentName, $agentSearch) === false) {
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
            
            // Get client agent name if available
            $clientAgentName = $code->client && $code->client->agent ? $code->client->agent->company_name : null;
            $rentAgentName = $code->rent_by_agent_name;
            
            // First try to find agent by ID
            if (!empty($agentId) && is_numeric($agentId) && in_array((int)$agentId, $agentUserIds)) {
                $agentName = $agentUsers[(int)$agentId] ?? null;
            } else {
                // Try to find agent by name from the rental code
                
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
            'isPayrollView' => $isPayrollView,
        ]);
    }

    /**
     * Mark a rental code as paid
     */
    public function markAsPaid(RentalCode $rentalCode)
    {
        // Only admin users can mark rental codes as paid
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators can mark rental codes as paid.',
            ], 403);
        }

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
        // Only admin users can mark rental codes as unpaid
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators can mark rental codes as unpaid.',
            ], 403);
        }

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
        // Only admin users can update rental code status
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators can update rental code status.',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'status' => 'required|string|in:pending,approved,paid',
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

        // Filter rentals for this specific agent (both as rental agent and marketing agent)
        $agentRentals = $rentalCodes->filter(function ($code) use ($agentName) {
            $rentAgentName = $code->rent_by_agent_name;
            $marketingAgentName = $code->marketing_agent_name;
            
            return $rentAgentName === $agentName || $marketingAgentName === $agentName;
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

            $agentEarnings = 0;
            $marketingEarnings = 0;
            
            // Check if agent is the rental agent
            $rentAgentName = $code->rent_by_agent_name;
            $marketingAgentName = $code->marketing_agent_name;
            
            if ($rentAgentName === $agentName) {
                // Agent is the rental agent - calculate rental earnings
                $agentEarnings = $baseCommission * 0.55;
                
                // Check for marketing deduction if there's a different marketing agent
                if (!empty($marketingAgentName) && $marketingAgentName !== $agentName) {
                    $marketingDeduction = $clientCount > 1 ? 40.0 : 30.0;
                    $agentEarnings -= $marketingDeduction;
                }
            }
            
            if ($marketingAgentName === $agentName && $marketingAgentName !== $rentAgentName) {
                // Agent is the marketing agent (and not the rental agent) - calculate marketing earnings
                $marketingEarnings = $clientCount > 1 ? 40.0 : 30.0;
            }
            
            $totalEarningsForThisCode = $agentEarnings + $marketingEarnings;
            $totalEarnings += $totalEarningsForThisCode;
            
            if ($code->paid) {
                $paidAmount += $totalEarningsForThisCode;
            } else {
                $outstandingAmount += $totalEarningsForThisCode;
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

            $agentEarnings = 0;
            $marketingEarnings = 0;
            
            // Check if agent is the rental agent
            $rentAgentName = $code->rent_by_agent_name;
            $marketingAgentName = $code->marketing_agent_name;
            
            if ($rentAgentName === $agentName) {
                // Agent is the rental agent - calculate rental earnings
                $agentEarnings = $baseCommission * 0.55;
                
                // Check for marketing deduction if there's a different marketing agent
                if (!empty($marketingAgentName) && $marketingAgentName !== $agentName) {
                    $marketingDeduction = $clientCount > 1 ? 40.0 : 30.0;
                    $agentEarnings -= $marketingDeduction;
                }
            }
            
            if ($marketingAgentName === $agentName && $marketingAgentName !== $rentAgentName) {
                // Agent is the marketing agent (and not the rental agent) - calculate marketing earnings
                $marketingEarnings = $clientCount > 1 ? 40.0 : 30.0;
            }
            
            $totalEarningsForThisCode = $agentEarnings + $marketingEarnings;
            $monthlyBreakdown[$monthKey]['total_earnings'] += $totalEarningsForThisCode;
            $monthlyBreakdown[$monthKey]['transaction_count'] += 1;
            
            if ($code->paid) {
                $monthlyBreakdown[$monthKey]['paid_amount'] += $totalEarningsForThisCode;
            } else {
                $monthlyBreakdown[$monthKey]['outstanding_amount'] += $totalEarningsForThisCode;
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
        $marketingAgents = User::where('role', 'marketing_agent')->orWhereJsonContains('roles', 'marketing_agent')->get();
        $allUsers = User::whereIn('role', ['agent', 'user'])->orWhereJsonContains('roles', 'agent')->get();
        
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
        $user->addRole('marketing_agent');

        return redirect()->route('marketing-agents.index')
            ->with('success', 'User has been assigned as a marketing agent.');
    }

    /**
     * Remove marketing agent role
     */
    public function removeMarketingAgent(User $user)
    {
        $user->removeRole('marketing_agent');
        
        return redirect()->route('marketing-agents.index')
            ->with('success', 'Marketing agent role has been removed.');
    }

    /**
     * Send rental code notification email to board@truehold.co.uk
     */
    private function sendRentalCodeNotification(RentalCode $rentalCode)
    {
        try {
            // Get agent name from the rental code data
            $agentName = $rentalCode->agent_name ?? $rentalCode->rent_by_agent ?? auth()->user()->name ?? 'Unknown Agent';
            
            // Generate PDF
            $pdf = Pdf::loadView('admin.rental-codes.pdf', [
                'rentalCode' => $rentalCode,
                'agentName' => $agentName,
                'client' => $rentalCode->client
            ]);
            
            // Try to send email, but don't fail if email service is not configured
            try {
                Mail::send('emails.rental-code-notification', [
                    'rentalCode' => $rentalCode,
                    'agentName' => $agentName,
                    'client' => $rentalCode->client
                ], function ($message) use ($rentalCode, $agentName, $pdf) {
                    $message->from('crm@truehold.co.uk', 'Truehold Group System')
                            ->to('board@truehold.co.uk')
                            ->subject('New Rental Code Generated - ' . $rentalCode->rental_code)
                            ->attachData($pdf->output(), "rental-code-{$rentalCode->rental_code}.pdf", [
                                'mime' => 'application/pdf',
                            ]);
                });
                
                \Log::info('Rental code email notification sent successfully', [
                    'rental_code' => $rentalCode->rental_code
                ]);
            } catch (\Exception $emailException) {
                // Log email error but continue with WhatsApp notifications
                \Log::warning('Email notification failed, but continuing with WhatsApp notifications', [
                    'rental_code' => $rentalCode->rental_code,
                    'email_error' => $emailException->getMessage()
                ]);
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the rental code creation
            \Log::error('Failed to send rental code notification: ' . $e->getMessage());
        }
    }

    /**
     * Send WhatsApp notifications for rental code (Admin only)
     */
    private function sendWhatsAppNotifications(RentalCode $rentalCode, $client)
    {
        // Temporarily disabled Twilio/WhatsApp notifications per request
        \Log::info('WhatsApp notifications are temporarily disabled; skipping send', [
            'rental_code' => $rentalCode->rental_code ?? null,
        ]);
        return;
    }

    /**
     * Bulk update status for multiple rental codes
     */
    public function bulkUpdateStatus(Request $request)
    {
        // Only admin users can perform bulk status updates
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators can perform bulk status updates.',
            ], 403);
        }

        $validated = $request->validate([
            'rental_code_ids' => 'required|array|min:1',
            'rental_code_ids.*' => 'integer|exists:rental_codes,id',
            'status' => 'required|string|in:pending,approved,paid',
        ]);

        try {
            $rentalCodeIds = $validated['rental_code_ids'];
            $newStatus = $validated['status'];
            
            // Update rental codes
            $updatedCount = RentalCode::whereIn('id', $rentalCodeIds)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now()
                ]);

            \Log::info('Bulk status update completed', [
                'rental_code_ids' => $rentalCodeIds,
                'new_status' => $newStatus,
                'updated_count' => $updatedCount,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} rental code(s) to {$newStatus}.",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk status update failed', [
                'error' => $e->getMessage(),
                'rental_code_ids' => $request->input('rental_code_ids'),
                'status' => $request->input('status'),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update rental codes. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle file uploads for rental codes
     */
    private function handleFileUploads(Request $request, RentalCode $rentalCode)
    {
        \Log::info('handleFileUploads called', [
            'rental_code_id' => $rentalCode->id,
            'has_client_contract' => $request->hasFile('client_contract'),
            'has_payment_proof' => $request->hasFile('payment_proof'),
            'has_client_id_document' => $request->hasFile('client_id_document')
        ]);
        
        try {
            // Handle client contract uploads
            if ($request->hasFile('client_contract')) {
                \Log::info('Processing client contract uploads', ['count' => count($request->file('client_contract'))]);
                $contractPaths = [];
                foreach ($request->file('client_contract') as $file) {
                    $path = $file->store('rental-codes/documents', 'public');
                    $contractPaths[] = $path;
                    \Log::info('Stored client contract file', ['path' => $path]);
                }
                $rentalCode->update(['client_contract' => json_encode($contractPaths)]);
                \Log::info('Updated client_contract field', ['paths' => $contractPaths]);
            }

            // Handle payment proof uploads
            if ($request->hasFile('payment_proof')) {
                \Log::info('Processing payment proof uploads', ['count' => count($request->file('payment_proof'))]);
                $proofPaths = [];
                foreach ($request->file('payment_proof') as $file) {
                    $path = $file->store('rental-codes/documents', 'public');
                    $proofPaths[] = $path;
                    \Log::info('Stored payment proof file', ['path' => $path]);
                }
                $rentalCode->update(['payment_proof' => json_encode($proofPaths)]);
                \Log::info('Updated payment_proof field', ['paths' => $proofPaths]);
            }

            // Handle client ID document uploads
            if ($request->hasFile('client_id_document')) {
                \Log::info('Processing client ID document uploads', ['count' => count($request->file('client_id_document'))]);
                $idPaths = [];
                foreach ($request->file('client_id_document') as $file) {
                    $path = $file->store('rental-codes/documents', 'public');
                    $idPaths[] = $path;
                    \Log::info('Stored client ID document file', ['path' => $path]);
                }
                $rentalCode->update(['client_id_document' => json_encode($idPaths)]);
                \Log::info('Updated client_id_document field', ['paths' => $idPaths]);
            }

            \Log::info('File uploads processed for rental code', ['rental_code_id' => $rentalCode->id]);
        } catch (\Exception $e) {
            \Log::error('Error handling file uploads', [
                'rental_code_id' => $rentalCode->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
