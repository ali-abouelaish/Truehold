<?php

namespace App\Http\Controllers;

use App\Models\RentalCode;
use App\Models\Agent;
use App\Models\User;
use App\Models\Client;
use App\Services\WhatsAppService;
use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class RentalCodeController extends Controller
{
    /**
     * Display a listing of rental codes
     */
    public function index(Request $request)
    {
        $query = RentalCode::with(['client', 'rentalAgent', 'marketingAgentUser']);

        // Server-side search across entire dataset
        $search = trim((string)$request->input('q', ''));
        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('rental_code', 'like', '%' . $search . '%')
                  ->orWhere('property', 'like', '%' . $search . '%')
                  ->orWhere('licensor', 'like', '%' . $search . '%')
                  ->orWhere('notes', 'like', '%' . $search . '%')
                  ->orWhere('payment_method', 'like', '%' . $search . '%')
                  ->orWhereHas('client', function($cq) use ($search) {
                      $cq->where('full_name', 'like', '%' . $search . '%')
                         ->orWhere('email', 'like', '%' . $search . '%')
                         ->orWhere('phone_number', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by payment method
        $paymentMethod = $request->input('payment_method');
        if (!empty($paymentMethod)) {
            $query->where('payment_method', $paymentMethod);
        }

        // Filter by agent (either rental or marketing agent)
        $agentId = $request->input('agent_id');
        if (!empty($agentId)) {
            $query->where(function($q) use ($agentId) {
                $q->where('rental_agent_id', (int)$agentId)
                  ->orWhere('marketing_agent_id', (int)$agentId);
            });
        }

        $rentalCodes = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Cycle stats (11th -> 10th) across all rentals, not just current page
        $today = now();
        if ($today->day <= 10) {
            $cycleStart = $today->copy()->subMonthNoOverflow()->day(11);
            $cycleEnd = $today->copy()->day(10)->endOfDay();
        } else {
            $cycleStart = $today->copy()->day(11);
            $cycleEnd = $today->copy()->addMonthNoOverflow()->day(10)->endOfDay();
        }
        // prevent future end date
        if ($cycleEnd->gt($today->copy()->endOfDay())) {
            $cycleEnd = $today->copy()->endOfDay();
        }
        $cycleStartDate = $cycleStart->toDateString();

        $baseMonthQuery = RentalCode::where(function($q) use ($cycleStartDate, $cycleEnd) {
            $q->where(function($q2) use ($cycleStartDate, $cycleEnd) {
                $q2->whereNotNull('rental_date')
                   ->where('rental_date', '>=', $cycleStartDate)
                   ->where('rental_date', '<=', $cycleEnd->toDateString());
            })->orWhere(function($q3) use ($cycleStartDate, $cycleEnd) {
                $q3->whereNull('rental_date')
                   ->where('created_at', '>=', $cycleStartDate)
                   ->where('created_at', '<=', $cycleEnd);
            });
        });

        $monthlyStats = [
            'total' => (clone $baseMonthQuery)->count(),
            'completed' => (clone $baseMonthQuery)->where('status', 'completed')->count(),
            'pending' => (clone $baseMonthQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseMonthQuery)->where('status', 'approved')->count(),
        ];

        // Agents list for filter
        $agents = User::where('role', 'agent')->pluck('name', 'id')->toArray();

        return view('admin.rental-codes.index', [
            'rentalCodes' => $rentalCodes,
            'monthlyStats' => $monthlyStats,
            'agents' => $agents,
            'search' => $search,
            'filterPaymentMethod' => $paymentMethod,
            'filterAgentId' => $agentId,
        ]);
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
            // Document upload validation - all required
            'client_contract.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'payment_proof.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'client_id_document.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
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
            // File upload error messages
            'client_contract.*.required' => 'Client contract document is required.',
            'payment_proof.*.required' => 'Payment proof document is required.',
            'client_contract.*.file' => 'Client contract must be a valid file.',
            'payment_proof.*.file' => 'Payment proof must be a valid file.',
            'client_id_document.*.required' => 'Client ID document is required.',
            'client_id_document.*.file' => 'Client ID document must be a valid file.',
            'client_contract.*.mimes' => 'Client contract must be a PDF, JPG, JPEG, or PNG file.',
            'payment_proof.*.mimes' => 'Payment proof must be a PDF, JPG, JPEG, or PNG file.',
            'client_id_document.*.mimes' => 'Client ID document must be a PDF, JPG, JPEG, or PNG file.',
            'client_contract.*.max' => 'Client contract file size must not exceed 10MB.',
            'payment_proof.*.max' => 'Payment proof file size must not exceed 10MB.',
            'client_id_document.*.max' => 'Client ID document file size must not exceed 10MB.',
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
        try {
            $this->handleFileUploads($request, $rentalCode);
            \Log::info('File upload handling completed successfully', [
                'rental_code_id' => $rentalCode->id
            ]);
        } catch (\Exception $e) {
            \Log::error('File upload handling failed', [
                'rental_code_id' => $rentalCode->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // Send email notification to board@truehold.co.uk
        $this->sendRentalCodeNotification($rentalCode);

        // Send WhatsApp notifications
        $this->sendWhatsAppNotifications($rentalCode, $client);

        // Send to Google Sheets
        try {
            $googleSheetsService = new GoogleSheetsService();
            $googleSheetsService->appendRentalCode($rentalCode);
        } catch (\Exception $e) {
            \Log::error('Failed to send rental code to Google Sheets', [
                'rental_code_id' => $rentalCode->id,
                'error' => $e->getMessage()
            ]);
            // Don't fail the request if Google Sheets fails
        }

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
        // Check if user can edit this rental code
        if (!auth()->user()->canEditRentalCode($rentalCode)) {
            abort(403, 'You are not authorized to edit this rental code.');
        }
        
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
        // Check if user can edit this rental code
        if (!auth()->user()->canEditRentalCode($rentalCode)) {
            abort(403, 'You are not authorized to edit this rental code.');
        }
        
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
            'existing_client_id' => 'required|exists:clients,id',
            'rent_by_agent' => 'required|string|max:255',
            'rental_agent_id' => 'nullable|exists:users,id',
            'marketing_agent_id' => 'nullable|exists:users,id',
            'client_count' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string',
            'contact_images.*' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            'client_id_image' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            'cash_receipt_image' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            // Regular document upload validation
            'client_contract.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'payment_proof.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'client_id_document.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];

        $validated = $request->validate($validationRules);

        // Get the selected client
        $client = \App\Models\Client::findOrFail($validated['existing_client_id']);

        // Update rental code data
        $rentalCodeData = $validated;
        $rentalCodeData['client_id'] = $client->id;
        
        // Remove client selection field from rental code data
        unset($rentalCodeData['existing_client_id']);

        // Handle document removal before updating
        $this->handleDocumentRemoval($request, $rentalCode);

        $rentalCode->update($rentalCodeData);

        // Handle file uploads for updates
        try {
            $this->handleFileUploads($request, $rentalCode);
            \Log::info('File upload handling completed for update', [
                'rental_code_id' => $rentalCode->id
            ]);
        } catch (\Exception $e) {
            \Log::error('File upload handling failed during update', [
                'rental_code_id' => $rentalCode->id,
                'error' => $e->getMessage()
            ]);
        }

        // Send WhatsApp notification for rental code update
        $this->sendWhatsAppUpdateNotification($rentalCode, $client);

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
            'payment_method' => ['nullable', 'in:Cash,Transfer,Card machine'],
            'agent_search' => ['nullable', 'string', 'max:255'],
            'marketing_agent_filter' => ['nullable', 'string', 'in:marketing_only,rent_only,both'],
        ]);

        // Define commission cycle: 11th -> 10th; default to current cycle if not provided
        $today = now();
        if ($today->day <= 10) {
            $cycleStart = $today->copy()->subMonthNoOverflow()->day(11);
            $cycleEnd = $today->copy()->day(10);
        } else {
            $cycleStart = $today->copy()->day(11);
            $cycleEnd = $today->copy()->addMonthNoOverflow()->day(10);
        }
        $cycleStartDate = $cycleStart->toDateString();
        // Do not extend into the future beyond today
        $cycleEndDate = min($today->toDateString(), $cycleEnd->toDateString());

        $startDate = $validated['start_date'] ?? $cycleStartDate;
        $endDate = $validated['end_date'] ?? $cycleEndDate;
        $status = $validated['status'] ?? null;
        $paymentMethod = $validated['payment_method'] ?? null;
        $agentSearch = $validated['agent_search'] ?? null;
        $marketingAgentFilter = $validated['marketing_agent_filter'] ?? null;

        // Public access: do not enforce authentication; show all agents unless filtered
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';

        // Check if we're viewing a specific agent's commission file
        $isPayrollView = !empty($agentSearch);
        
        // Build query with filters
        $query = RentalCode::with(['client', 'client.agent', 'rentalAgent', 'marketingAgentUser']);

        // Current cycle rentals OR any unpaid rentals (carry over)
        $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('rental_date', [$startDate, $endDate])
              ->orWhere('paid', false);
        });
        
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

        $rentalCodes = $query->get()->filter(function($code) use ($startDate, $endDate) {
            $date = $code->rental_date ?? ($code->created_at ?? now());
            $dateStr = $date instanceof \Carbon\Carbon ? $date->toDateString() : (string)$date;
            $inCycle = $dateStr >= $startDate && $dateStr <= $endDate;
            return $inCycle || !($code->paid ?? false);
        });

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
            if (in_array($paymentMethod, ['Transfer', 'Card machine'])) {
                // Subtract 20% VAT for transfer payments
                $baseCommission = $totalFee * 0.8;
            }

            // Calculate commission split: Agency 45%, Agent 55%
            $agencyCut = $baseCommission * 0.45;
            $agentCut = $baseCommission * 0.55;
            
            // Check if marketing agent is different from rental agent (use IDs when available)
            $marketingDeduction = 0;
            $clientCount = $code->client_count ?? 1;
            
            $hasDifferentMarketingAgent = false;
            // Resolve marketing agent name from relation or fallback field
            $marketingAgentName = $code->marketingAgentUser->name ?? ($code->marketing_agent_name ?? null);
            if ($marketingAgentName === 'N/A') { $marketingAgentName = null; }

            if (!empty($code->marketing_agent_id) && !empty($code->rental_agent_id)) {
                $hasDifferentMarketingAgent = (int) $code->marketing_agent_id !== (int) $code->rental_agent_id;
            } else {
                $rentAgentName = $code->rent_by_agent_name;
                if (!empty($marketingAgentName) && !empty($rentAgentName)) {
                    $hasDifferentMarketingAgent = trim($marketingAgentName) !== trim($rentAgentName);
                }
            }
            
            if ($hasDifferentMarketingAgent) {
                // £30 for single client, £40 for multiple clients
                $marketingDeduction = $clientCount > 1 ? 40.0 : 30.0;
                $agentCut -= $marketingDeduction; // Deduct from rental agent
                
                // Ensure marketing agent name is available for summaries
                if (empty($marketingAgentName)) {
                    $marketingAgentName = $code->marketingAgentUser->name ?? ($code->marketing_agent_name ?? null);
                    if ($marketingAgentName === 'N/A') { $marketingAgentName = null; }
                }
            }
            
            // Determine the agent strictly by rental_agent_id (ID-based only)
            $agentName = null;
            $rentalAgentId = $code->rental_agent_id ?? null;
            if (!empty($rentalAgentId) && in_array((int)$rentalAgentId, $agentUserIds)) {
                $agentName = $agentUsers[(int)$rentalAgentId] ?? null;
            } else {
                // Skip records that do not have a valid registered rental agent id
                continue;
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
                if (in_array($paymentMethod, ['Transfer', 'Card machine'])) {
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
                    'vat_amount' => in_array($paymentMethod, ['Transfer', 'Card machine']) ? ($totalFee - $baseCommission) : 0,
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
            
            // Handle marketing agent earnings (only if different from rental agent) - ID-based only
            if ($marketingDeduction > 0) {
                $marketingAgentName = null;
                if (!empty($code->marketing_agent_id) && in_array((int)$code->marketing_agent_id, $agentUserIds) && (int)$code->marketing_agent_id !== (int)$rentalAgentId) {
                    $marketingAgentName = $agentUsers[(int)$code->marketing_agent_id] ?? null;
                }
                if ($marketingAgentName && $marketingAgentName !== $agentName) {
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
        }

        // Calculate averages and apply agent search filter
        $filteredAgents = [];
        foreach ($byAgent as $agentName => $agentData) {
            // If searching for a specific agent, only show that agent (exact match)
            if ($agentSearch && $agentName !== $agentSearch) {
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

        // Calculate cycle totals (11th -> 10th) - only for registered agent users with proper commission structure (ID-based only)
        $monthlyTotals = []; // reuse key name for view compatibility
        foreach ($rentalCodes as $code) {
            $totalFee = (float) ($code->consultation_fee ?? 0);
            $rentalDate = $code->rental_date ?? ($code->created_at ?? now());
            // Determine cycle end for this record
            $cycleEnd = $rentalDate->copy();
            if ($cycleEnd->day <= 10) {
                $cycleEnd = $cycleEnd->copy()->day(10);
            } else {
                $cycleEnd = $cycleEnd->copy()->addMonthNoOverflow()->day(10);
            }
            $monthKey = $cycleEnd->format('Y-m-10');
            $paymentMethod = $code->payment_method ?? 'Cash';
            
            // Skip if no consultation fee
            if ($totalFee <= 0) {
                continue;
            }
            
            // Calculate base commission after VAT (for Transfer and Card machine)
            $baseCommission = $totalFee;
            if (in_array($paymentMethod, ['Transfer', 'Card machine'])) {
                $baseCommission = $totalFee * 0.8;
            }
            
            // Determine the agent strictly by rental_agent_id (registered users only)
            $agentName = null;
            $rentalAgentId = $code->rental_agent_id ?? null;
            if (!empty($rentalAgentId) && in_array((int)$rentalAgentId, $agentUserIds)) {
                $agentName = $agentUsers[(int)$rentalAgentId] ?? null;
            } else {
                continue;
            }
            
            // Add to cycle totals
            if ($agentName) {
                if (!isset($monthlyTotals[$monthKey])) {
                    $monthlyTotals[$monthKey] = 0;
                }
                $monthlyTotals[$monthKey] += $baseCommission;
            }
        }
        ksort($monthlyTotals);
        $chartData['monthly_totals'] = $monthlyTotals;

        // Default behavior: show only agents with rental earnings (exclude marketing-only) unless explicitly requested
        if (empty($marketingAgentFilter)) {
            $filteredAgents = array_filter($filteredAgents, function ($agent) {
                // Keep agents that have any rental/bonus earnings attributed to them
                return ($agent['agent_earnings'] ?? 0) > 0;
            });
        }

        // Final debug logging
        \Log::info('Agent earnings summary', [
            'total_rental_codes' => $rentalCodes->count(),
            'agents_found' => count($filteredAgents),
            'agent_names' => array_keys($filteredAgents),
            'monthly_totals' => $monthlyTotals
        ]);

        // Add landlord bonuses to agent earnings
        // Landlord bonuses in current cycle OR any unpaid bonuses
        $landlordBonuses = \App\Models\LandlordBonus::with(['agent.user'])
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere('status', '!=', 'paid');
            })
            ->get();

        foreach ($landlordBonuses as $bonus) {
            $agentName = $bonus->agent->user->name;
            
            if (!isset($filteredAgents[$agentName])) {
                $filteredAgents[$agentName] = [
                    'name' => $agentName,
                    'total_earnings' => 0,
                    'paid_amount' => 0,
                    'entitled_amount' => 0,
                    'outstanding_amount' => 0,
                    'transaction_count' => 0,
                    'paid_transactions' => 0,
                    'unpaid_transactions' => 0,
                    'marketing_deductions' => 0,
                    'agent_earnings' => 0,
                    'agency_earnings' => 0,
                    'avg_transaction_value' => 0,
                    'last_transaction_date' => null,
                    'transactions' => [],
                    'landlord_bonuses' => []
                ];
            }
            
            $bonusAmount = $bonus->agent_commission; // Use agent's portion, not full commission
            $filteredAgents[$agentName]['total_earnings'] += $bonusAmount;
            $filteredAgents[$agentName]['agent_earnings'] = ($filteredAgents[$agentName]['agent_earnings'] ?? 0) + $bonusAmount;
            $filteredAgents[$agentName]['transaction_count'] += 1;
            
            // Ensure landlord_bonuses array exists
            if (!isset($filteredAgents[$agentName]['landlord_bonuses'])) {
                $filteredAgents[$agentName]['landlord_bonuses'] = [];
            }
            
            if ($bonus->status === 'paid') {
                $filteredAgents[$agentName]['paid_amount'] += $bonusAmount;
                $filteredAgents[$agentName]['paid_transactions'] = ($filteredAgents[$agentName]['paid_transactions'] ?? 0) + 1;
            } else {
                $filteredAgents[$agentName]['entitled_amount'] += $bonusAmount;
                $filteredAgents[$agentName]['unpaid_transactions'] = ($filteredAgents[$agentName]['unpaid_transactions'] ?? 0) + 1;
            }
            
            $filteredAgents[$agentName]['outstanding_amount'] = max(0, 
                $filteredAgents[$agentName]['entitled_amount'] - $filteredAgents[$agentName]['paid_amount']
            );
            
            $filteredAgents[$agentName]['landlord_bonuses'][] = [
                'id' => $bonus->id,
                'bonus_code' => $bonus->bonus_code,
                'date' => $bonus->date,
                'landlord' => $bonus->landlord,
                'property' => $bonus->property,
                'client' => $bonus->client,
                'commission' => $bonus->commission,
                'agent_commission' => $bonusAmount,
                'bonus_split' => $bonus->bonus_split,
                'status' => $bonus->status,
                'notes' => $bonus->notes,
                'type' => 'landlord_bonus'
            ];
        }

        // Recalculate summary statistics after adding landlord bonuses
        $summary = [
            'total_agents' => count($filteredAgents),
            'total_rental_codes' => $rentalCodes->count(),
            'total_earnings' => array_sum(array_map(fn($x) => $x['total_earnings'], $filteredAgents)),
            'total_agency_earnings' => array_sum(array_map(fn($x) => $x['agency_earnings'] ?? 0, $filteredAgents)),
            'total_agent_earnings' => array_sum(array_map(fn($x) => $x['agent_earnings'] ?? 0, $filteredAgents)),
            'total_marketing_earnings' => array_sum(array_map(fn($x) => $x['marketing_agent_earnings'] ?? 0, $filteredAgents)),
            'total_marketing_deductions' => array_sum(array_map(fn($x) => $x['marketing_deductions'] ?? 0, $filteredAgents)),
            'total_paid_amount' => array_sum(array_map(fn($x) => $x['paid_amount'], $filteredAgents)),
            'total_entitled_amount' => array_sum(array_map(fn($x) => $x['entitled_amount'], $filteredAgents)),
            'total_outstanding_amount' => array_sum(array_map(fn($x) => $x['outstanding_amount'], $filteredAgents)),
            'total_transactions' => array_sum(array_map(fn($x) => $x['transaction_count'], $filteredAgents)),
            'total_vat_deductions' => array_sum(array_map(fn($x) => $x['vat_deductions'] ?? 0, $filteredAgents)),
            'avg_earnings_per_agent' => count($filteredAgents) > 0 
                ? array_sum(array_map(fn($x) => $x['total_earnings'], $filteredAgents)) / count($filteredAgents) 
                : 0,
            'top_earner' => count($filteredAgents) > 0 ? array_values($filteredAgents)[0] : null,
        ];

        // Attach agent IDs to each entry for linking
        foreach ($filteredAgents as $name => &$agent) {
            $foundId = array_search($name, $agentUsers, true);
            if ($foundId !== false) {
                $agent['id'] = $foundId;
            }
        }
        unset($agent);

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
     * Show individual agent payroll details
     */
    public function agentPayroll($requestedAgentName)
    {
        // Get all agent earnings data
        // Commission cycle defaults: 11th -> 10th
        $today = now();
        if ($today->day <= 10) {
            $cycleStart = $today->copy()->subMonthNoOverflow()->day(11);
            $cycleEnd = $today->copy()->day(10);
        } else {
            $cycleStart = $today->copy()->day(11);
            $cycleEnd = $today->copy()->addMonthNoOverflow()->day(10);
        }
        $cycleStartDate = $cycleStart->toDateString();
        $cycleEndDate = min($today->toDateString(), $cycleEnd->toDateString());

        $startDate = request('start_date') ?? $cycleStartDate;
        $endDate = request('end_date') ?? $cycleEndDate;
        $normalizedRequestedName = trim($requestedAgentName);
        $endDateTime = \Carbon\Carbon::parse($endDate)->endOfDay();
        $startDateTime = $startDate ? \Carbon\Carbon::parse($startDate)->startOfDay() : null;
        $status = request('status');
        $paymentMethod = request('payment_method');
        $marketingAgentFilter = request('marketing_agent_filter');

        // Get rental codes
        $query = RentalCode::with(['client', 'client.agent'])
            ->where('rental_date', '<=', $endDate)
            // Payroll should only include approved rentals
            ->where('status', 'approved');

        if ($startDate) {
            $query->where('rental_date', '>=', $startDate);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $rentalCodes = $query->get()->filter(function($code) use ($startDate, $endDate) {
            $date = $code->rental_date ?? ($code->created_at ?? now());
            $dateStr = $date instanceof \Carbon\Carbon ? $date->toDateString() : (string)$date;
            $inCycle = $dateStr >= $startDate && $dateStr <= $endDate;
            return $inCycle || !($code->paid ?? false);
        });

        // Get agent users
        $agentUsers = User::where('role', 'agent')->pluck('name', 'id')->toArray();
        $agentUserIds = User::where('role', 'agent')->pluck('id')->toArray();
        $agentUserNames = array_values($agentUsers);

        // Process rental codes
        $byAgent = [];
        foreach ($rentalCodes as $code) {
            // Safety: ensure only approved rentals are processed
            if (($code->status ?? null) !== 'approved') {
                continue;
            }
            $totalFee = (float) ($code->consultation_fee ?? 0);
            $rentalDate = $code->rental_date ?? now();
            $paymentMethod = $code->payment_method ?? 'Cash';

            if ($totalFee <= 0) continue;

            // Calculate base commission after VAT
            $baseCommission = $totalFee;
            if (in_array($paymentMethod, ['Transfer', 'Card machine'])) {
                $baseCommission = $totalFee * 0.8;
            }

            // Determine agent
            $agentId = $code->rent_by_agent;
            $agentName = null;

            if (!empty($agentId) && is_numeric($agentId) && in_array((int)$agentId, $agentUserIds)) {
                $agentName = $agentUsers[(int)$agentId] ?? null;
            } else {
                $clientAgentName = $code->client && $code->client->agent ? $code->client->agent->company_name : null;
                $rentAgentName = $code->rent_by_agent_name;

                if (!empty($clientAgentName) && in_array($clientAgentName, $agentUserNames)) {
                    $agentName = $clientAgentName;
                } elseif (!empty($rentAgentName) && in_array($rentAgentName, $agentUserNames)) {
                    $agentName = $rentAgentName;
                } else {
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

            if (empty($agentName)) {
                if (!empty($code->rent_by_agent_name)) {
                    $agentName = $code->rent_by_agent_name;
                } elseif (!empty($agentId)) {
                    $agentName = is_string($agentId) ? trim($agentId) : "Agent-{$agentId}";
                } else {
                    $agentName = "Unknown Agent";
                }
            }

            // Only process if this is the requested agent
            if ($agentName !== $requestedAgentName) continue;

            // Initialize agent data
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

            // Calculate splits
            $agencyCut = $baseCommission * 0.45;
            $agentCut = $baseCommission * 0.55;

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

            // Track paid amounts
            $isPaid = $code->paid ?? false;
            $agentEarnings = $agentCut;

            if ($isPaid) {
                $byAgent[$agentName]['paid_amount'] += $agentEarnings;
            } else {
                $byAgent[$agentName]['entitled_amount'] += $agentEarnings;
            }

            $byAgent[$agentName]['outstanding_amount'] = max(0, $byAgent[$agentName]['entitled_amount'] - $byAgent[$agentName]['paid_amount']);

            $byAgent[$agentName]['transactions'][] = [
                'total_fee' => $totalFee,
                'base_commission' => $baseCommission,
                'agency_cut' => $agencyCut,
                'agent_cut' => $agentCut,
                'vat_amount' => in_array($paymentMethod, ['Transfer', 'Card machine']) ? ($totalFee - $baseCommission) : 0,
                'marketing_deduction' => 0,
                'marketing_agent' => null,
                'client_count' => 1,
                'paid' => $isPaid,
                'paid_at' => $code->paid_at,
                'date' => $rentalDate,
                'code' => $code->rental_code ?? 'N/A',
                'status' => $code->status ?? 'Unknown',
                'payment_method' => $paymentMethod,
            ];

            // Update last transaction date
            if (!$byAgent[$agentName]['last_transaction_date'] || $rentalDate > $byAgent[$agentName]['last_transaction_date']) {
                $byAgent[$agentName]['last_transaction_date'] = $rentalDate;
            }
        }

        // Get landlord bonuses for this agent
        $landlordBonuses = \App\Models\LandlordBonus::with(['agent.user'])
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere('status', '!=', 'paid');
            })
            ->get()
            ->filter(function($bonus) use ($requestedAgentName) {
                return $bonus->agent->user->name === $requestedAgentName;
            });

        // Add landlord bonuses to agent data
        if (isset($byAgent[$agentName])) {
            $byAgent[$agentName]['landlord_bonuses'] = [];
            foreach ($landlordBonuses as $bonus) {
                $bonusAmount = $bonus->agent_commission;
                $byAgent[$agentName]['total_earnings'] += $bonusAmount;
                $byAgent[$agentName]['agent_earnings'] += $bonusAmount;
                $byAgent[$agentName]['transaction_count'] += 1;

                if ($bonus->status === 'paid') {
                    $byAgent[$agentName]['paid_amount'] += $bonusAmount;
                } else {
                    $byAgent[$agentName]['entitled_amount'] += $bonusAmount;
                }

                $byAgent[$agentName]['outstanding_amount'] = max(0, 
                    $byAgent[$agentName]['entitled_amount'] - $byAgent[$agentName]['paid_amount']
                );

                $byAgent[$agentName]['landlord_bonuses'][] = [
                    'id' => $bonus->id,
                    'bonus_code' => $bonus->bonus_code,
                    'date' => $bonus->date,
                    'landlord' => $bonus->landlord,
                    'property' => $bonus->property,
                    'client' => $bonus->client,
                    'commission' => $bonus->commission,
                    'agent_commission' => $bonusAmount,
                    'bonus_split' => $bonus->bonus_split,
                    'status' => $bonus->status,
                    'notes' => $bonus->notes,
                    'type' => 'landlord_bonus'
                ];
            }

            // Section totals: rentals (exclude marketing entries) and bonuses
            $rentalCount = 0;
            $rentalPaid = 0.0;
            $rentalEntitled = 0.0;
            $rentalAgentCut = 0.0;
            foreach ($byAgent[$agentName]['transactions'] as $t) {
                if (!($t['is_marketing_earnings'] ?? false)) {
                    $rentalCount += 1;
                    $rentalAgentCut += (float) ($t['agent_cut'] ?? 0);
                    if (!empty($t['paid'])) {
                        $rentalPaid += (float) ($t['agent_cut'] ?? 0);
                    } else {
                        $rentalEntitled += (float) ($t['agent_cut'] ?? 0);
                    }
                }
            }
            $byAgent[$agentName]['rental_totals'] = [
                'count' => $rentalCount,
                'agent_cut' => $rentalAgentCut,
                'paid' => $rentalPaid,
                'entitled' => $rentalEntitled,
                'outstanding' => max(0, $rentalEntitled),
            ];

            $bonusCount = count($byAgent[$agentName]['landlord_bonuses']);
            $bonusPaid = 0.0;
            $bonusEntitled = 0.0;
            $bonusCommission = 0.0;
            foreach ($byAgent[$agentName]['landlord_bonuses'] as $b) {
                $bonusCommission += (float) ($b['agent_commission'] ?? 0);
                if (($b['status'] ?? '') === 'paid') {
                    $bonusPaid += (float) ($b['agent_commission'] ?? 0);
                } else {
                    $bonusEntitled += (float) ($b['agent_commission'] ?? 0);
                }
            }
            $byAgent[$agentName]['bonus_totals'] = [
                'count' => $bonusCount,
                'agent_commission' => $bonusCommission,
                'paid' => $bonusPaid,
                'entitled' => $bonusEntitled,
                'outstanding' => max(0, $bonusEntitled),
            ];
        }

        $agentData = $byAgent[$requestedAgentName] ?? null;
        if (!$agentData) {
            abort(404, 'Agent not found');
        }

        // If no data, render the view with an empty state instead of 404
        if ($agentData['transaction_count'] === 0 && count($agentData['landlord_bonuses']) === 0) {
            \Log::info('Agent payroll accessed with no data', ['agent' => $requestedAgentName]);
        }

        return view('admin.rental-codes.agent-payroll', [
            'agent' => $agentData,
            'agentName' => $requestedAgentName,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Show individual agent commission file - NEW VERSION
     */
    public function agentPayrollNew($requestedAgentName)
    {
        // Require authentication for payroll view; admins can view any agent, others only their own
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to view payroll.');
        }
        $isAdmin = $user->role === 'admin';
        if (!$isAdmin && $user->name !== $requestedAgentName) {
            return redirect()->route('rental-codes.agent-earnings')->with('error', 'You can only view your own payroll.');
        }

        // Treat marketing agents as marketing-only for this view
        $requestedIsMarketing = \App\Models\User::where('name', $requestedAgentName)
            ->where(function($q) {
                $q->where('role', 'marketing_agent')
                  ->orWhereJsonContains('roles', 'marketing_agent');
            })
            ->exists();
        
        // Commission cycle defaults: 11th -> 10th
        $today = now();
        if ($today->day <= 10) {
            $cycleStart = $today->copy()->subMonthNoOverflow()->day(11);
            $cycleEnd = $today->copy()->day(10);
        } else {
            $cycleStart = $today->copy()->day(11);
            $cycleEnd = $today->copy()->addMonthNoOverflow()->day(10);
        }
        $cycleStartDate = $cycleStart->toDateString();
        $cycleEndDate = min($today->toDateString(), $cycleEnd->toDateString());

        $startDate = request('start_date') ?? $cycleStartDate;
        $endDate = request('end_date') ?? $cycleEndDate;
        $status = request('status');
        $paymentMethod = request('payment_method');
        $normalizedRequestedName = trim($requestedAgentName);
        $endDateTime = \Carbon\Carbon::parse($endDate)->endOfDay();
        $startDateTime = $startDate ? \Carbon\Carbon::parse($startDate)->startOfDay() : null;

        // Get rental codes for this agent (restrict to approved)
        $query = RentalCode::with(['client', 'client.agent', 'rentalAgent', 'marketingAgentUser'])
            ->where(function($q) use ($endDateTime) {
                $q->where(function($q2) use ($endDateTime) {
                    $q2->whereNotNull('rental_date')
                       ->where('rental_date', '<=', $endDateTime->toDateString());
                })->orWhere(function($q3) use ($endDateTime) {
                    $q3->whereNull('rental_date')
                       ->where('created_at', '<=', $endDateTime);
                });
            })
            ->where('status', 'approved');

        if ($startDateTime) {
            $query->where(function($q) use ($startDateTime) {
                $q->where(function($q2) use ($startDateTime) {
                    $q2->whereNotNull('rental_date')
                       ->where('rental_date', '>=', $startDateTime->toDateString());
                })->orWhere(function($q3) use ($startDateTime) {
                    $q3->whereNull('rental_date')
                       ->where('created_at', '>=', $startDateTime);
                });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $rentalCodes = $query->get();

        // Get users for matching (include all users so IDs referenced in records still match)
        $agentUsers = User::pluck('name', 'id')->toArray();
        $agentUserIds = User::pluck('id')->toArray();

        // Initialize agent data
        $agentData = [
            'name' => $requestedAgentName,
            'agency_earnings' => 0.0,
            'agent_earnings' => 0.0,
            'total_earnings' => 0.0,
            'transaction_count' => 0,
            'transactions' => [],
            'vat_deductions' => 0.0,
            'marketing_deductions' => 0.0,
            'marketing_agent_earnings' => 0.0,
            'paid_amount' => 0.0,
            'entitled_amount' => 0.0,
            'outstanding_amount' => 0.0,
            'landlord_bonuses' => []
        ];

        // Process rental codes for this agent
        foreach ($rentalCodes as $code) {
            $totalFee = (float) ($code->consultation_fee ?? 0);
            $rentalDate = $code->rental_date ?? ($code->created_at ?? now());
            $paymentMethod = $code->payment_method ?? 'Cash';

            if ($totalFee <= 0) continue;

            // Calculate base commission after VAT
            $baseCommission = $totalFee;
            if (in_array($paymentMethod, ['Transfer', 'Card machine'])) {
                $baseCommission = $totalFee * 0.8;
            }

            // Determine if this rental code belongs to the requested agent
            $agentName = null;
            $isMarketingEarnings = false;
            $marketingAgent = null;
            $clientCount = $code->client_count ?? 1; // Get actual client count or default to 1

            // Check rental agent
            if ($code->rentalAgent && strcasecmp(trim($code->rentalAgent->name), $normalizedRequestedName) === 0) {
                $agentName = $normalizedRequestedName;
            }
            // Check marketing agent
            elseif ($code->marketingAgentUser && strcasecmp(trim($code->marketingAgentUser->name), $normalizedRequestedName) === 0) {
                $agentName = $normalizedRequestedName;
                $isMarketingEarnings = true;
                $marketingAgent = $normalizedRequestedName;
            }
            // Check by ID
            elseif (!empty($code->rent_by_agent) && is_numeric($code->rent_by_agent) && in_array((int)$code->rent_by_agent, $agentUserIds)) {
                $agentName = $agentUsers[(int)$code->rent_by_agent] ?? null;
                if ($agentName !== $requestedAgentName) continue;
            }
            // Check by name
            elseif (!empty($code->rent_by_agent_name) && strcasecmp(trim($code->rent_by_agent_name), $normalizedRequestedName) === 0) {
                $agentName = $normalizedRequestedName;
            }
            // Check client agent
            elseif ($code->client && $code->client->agent && strcasecmp(trim($code->client->agent->company_name), $normalizedRequestedName) === 0) {
                $agentName = $normalizedRequestedName;
            }

            // Skip if this rental code doesn't belong to the requested agent
            if (!($agentName !== null && strcasecmp($agentName, $normalizedRequestedName) === 0)) continue;

            // Show both marketing and rental earnings together (no marketing-only gating)

            // Calculate splits based on agent type
            if ($isMarketingEarnings) {
                // Marketing agent gets fixed commission: £30 for 1 client, £40 for 2+ clients
                $marketingCommission = $clientCount >= 2 ? 40 : 30;
                
                $agencyCut = $baseCommission - $marketingCommission;
                $agentCut = $marketingCommission;
                
                // Track marketing earnings
                $agentData['marketing_agent_earnings'] += $marketingCommission;
            } else {
                // Regular rental agent gets 55% of base commission
                $agencyCut = $baseCommission * 0.45;
                $agentCut = $baseCommission * 0.55;
                
                // If there's a separate marketing agent, deduct marketing commission from rental agent
                $hasDifferentMarketingAgent = false;
                if (!empty($code->marketing_agent_id) && !empty($code->rental_agent_id)) {
                    $hasDifferentMarketingAgent = (int) $code->marketing_agent_id !== (int) $code->rental_agent_id;
                } else {
                    $rentAgentNameCheck = $code->rent_by_agent_name;
                    $marketingAgentNameCheck = $code->marketing_agent_name;
                    if ($marketingAgentNameCheck === 'N/A') { $marketingAgentNameCheck = null; }
                    if (!empty($marketingAgentNameCheck) && !empty($rentAgentNameCheck)) {
                        $hasDifferentMarketingAgent = trim($marketingAgentNameCheck) !== trim($rentAgentNameCheck);
                    }
                }

                if ($hasDifferentMarketingAgent) {
                    $marketingDeduction = $clientCount >= 2 ? 40 : 30;
                    $agentCut -= $marketingDeduction;
                    $agentData['marketing_deductions'] += $marketingDeduction;
                    
                    // The marketing agent gets this amount (tracked separately)
                    $agentData['marketing_agent_earnings'] += $marketingDeduction;
                }
            }

            // Add to agent totals
            $agentData['agency_earnings'] += $agencyCut;
            $agentData['agent_earnings'] += $agentCut;
            $agentData['total_earnings'] += $baseCommission;
            $agentData['transaction_count'] += 1;

            // Track VAT deductions
            if (in_array($paymentMethod, ['Transfer', 'Card machine'])) {
                $vatAmount = $totalFee - $baseCommission;
                $agentData['vat_deductions'] += $vatAmount;
            }

            // Track paid amounts
            $isPaid = $code->paid ?? false;
            $agentEarnings = $agentCut;

            if ($isPaid) {
                $agentData['paid_amount'] += $agentEarnings;
            } else {
                $agentData['entitled_amount'] += $agentEarnings;
            }

            $agentData['outstanding_amount'] = max(0, $agentData['entitled_amount'] - $agentData['paid_amount']);

            // Add transaction details
            $agentData['transactions'][] = [
                'id' => $code->id,
                'total_fee' => $totalFee,
                'base_commission' => $baseCommission,
                'agency_cut' => $agencyCut,
                'agent_cut' => $agentCut,
                'vat_amount' => in_array($paymentMethod, ['Transfer', 'Card machine']) ? ($totalFee - $baseCommission) : 0,
                'marketing_deduction' => $marketingDeduction ?? 0,
                'marketing_agent' => $code->marketing_agent_name,
                'client_count' => $clientCount,
                'paid' => $isPaid,
                'paid_at' => $code->paid_at,
                'date' => $rentalDate,
                'code' => $code->rental_code ?? 'N/A',
                'status' => $code->status ?? 'Unknown',
                'payment_method' => $paymentMethod,
                'is_marketing_earnings' => $isMarketingEarnings,
            ];
        }

        // Get landlord bonuses for this agent
        $landlordBonuses = \App\Models\LandlordBonus::with(['agent.user'])
            ->whereBetween('date', [$startDate ?? '1900-01-01', $endDate])
            ->get()
            ->filter(function($bonus) use ($requestedAgentName) {
                return $bonus->agent->user->name === $requestedAgentName;
            });

        // Add landlord bonuses to agent data
        foreach ($landlordBonuses as $bonus) {
            $bonusAmount = $bonus->agent_commission;
            $agentData['total_earnings'] += $bonusAmount;
            $agentData['agent_earnings'] += $bonusAmount;
            $agentData['transaction_count'] += 1;

            if ($bonus->status === 'paid') {
                $agentData['paid_amount'] += $bonusAmount;
            } else {
                $agentData['entitled_amount'] += $bonusAmount;
            }

            $agentData['outstanding_amount'] = max(0, 
                $agentData['entitled_amount'] - $agentData['paid_amount']
            );

            $agentData['landlord_bonuses'][] = [
                'id' => $bonus->id,
                'bonus_code' => $bonus->bonus_code,
                'date' => $bonus->date,
                'landlord' => $bonus->landlord,
                'property' => $bonus->property,
                'client' => $bonus->client,
                'commission' => $bonus->commission,
                'agent_commission' => $bonusAmount,
                'bonus_split' => $bonus->bonus_split,
                'status' => $bonus->status,
                'notes' => $bonus->notes,
                'type' => 'landlord_bonus'
            ];
        }

        // Section totals (rentals and bonuses)
        $rentalCount = 0;
        $rentalPaid = 0.0;
        $rentalEntitled = 0.0;
        $rentalAgentCut = 0.0;
        foreach ($agentData['transactions'] as $t) {
            if (!($t['is_marketing_earnings'] ?? false)) {
                $rentalCount += 1;
                $rentalAgentCut += (float) ($t['agent_cut'] ?? 0);
                if (!empty($t['paid'])) {
                    $rentalPaid += (float) ($t['agent_cut'] ?? 0);
                } else {
                    $rentalEntitled += (float) ($t['agent_cut'] ?? 0);
                }
            }
        }
        $agentData['rental_totals'] = [
            'count' => $rentalCount,
            'agent_cut' => $rentalAgentCut,
            'paid' => $rentalPaid,
            'entitled' => $rentalEntitled,
            'outstanding' => max(0, $rentalEntitled),
        ];

        $bonusCount = count($agentData['landlord_bonuses']);
        $bonusPaid = 0.0;
        $bonusEntitled = 0.0;
        $bonusCommission = 0.0;
        foreach ($agentData['landlord_bonuses'] as $b) {
            $bonusCommission += (float) ($b['agent_commission'] ?? 0);
            if (($b['status'] ?? '') === 'paid') {
                $bonusPaid += (float) ($b['agent_commission'] ?? 0);
            } else {
                $bonusEntitled += (float) ($b['agent_commission'] ?? 0);
            }
        }
        $agentData['bonus_totals'] = [
            'count' => $bonusCount,
            'agent_commission' => $bonusCommission,
            'paid' => $bonusPaid,
            'entitled' => $bonusEntitled,
            'outstanding' => max(0, $bonusEntitled),
        ];

        // Check if agent has any data; render empty state instead of 404
        if ($agentData['transaction_count'] === 0 && count($agentData['landlord_bonuses']) === 0) {
            \Log::info('Agent payroll accessed with no data', ['agent' => $requestedAgentName]);
        }

        return view('admin.rental-codes.agent-payroll', [
            'agent' => $agentData,
            'agentName' => $requestedAgentName,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Show individual agent commission file by agent ID (preferred)
     */
    public function agentPayrollById($agentId)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to view payroll.');
        }
        $isAdmin = $user->role === 'admin';
        if (!$isAdmin && (int)$user->id !== (int)$agentId) {
            return redirect()->route('rental-codes.agent-earnings')->with('error', 'You can only view your own payroll.');
        }

        $agentUser = User::find($agentId);
        if (!$agentUser) {
            abort(404, 'Agent not found');
        }

        $startDate = request('start_date');
        $endDate = request('end_date') ?? now()->format('Y-m-d');
        $status = request('status');
        $paymentMethod = request('payment_method');

        $endDateTime = \Carbon\Carbon::parse($endDate)->endOfDay();
        $startDateTime = $startDate ? \Carbon\Carbon::parse($startDate)->startOfDay() : null;

        // Rentals for this agent by foreign key; include date fallbacks
        $query = RentalCode::with(['client', 'client.agent', 'rentalAgent', 'marketingAgentUser'])
            ->where('rental_agent_id', (int)$agentId)
            ->where(function($q) use ($endDateTime) {
                $q->where(function($q2) use ($endDateTime) {
                    $q2->whereNotNull('rental_date')
                       ->where('rental_date', '<=', $endDateTime->toDateString());
                })->orWhere(function($q3) use ($endDateTime) {
                    $q3->whereNull('rental_date')
                       ->where('created_at', '<=', $endDateTime);
                });
            })
            ->where('status', 'approved');

        if ($startDateTime) {
            $query->where(function($q) use ($startDateTime) {
                $q->where(function($q2) use ($startDateTime) {
                    $q2->whereNotNull('rental_date')
                       ->where('rental_date', '>=', $startDateTime->toDateString());
                })->orWhere(function($q3) use ($startDateTime) {
                    $q3->whereNull('rental_date')
                       ->where('created_at', '>=', $startDateTime);
                });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $rentalCodes = $query->get();

        $agentData = [
            'name' => $agentUser->name,
            'agency_earnings' => 0.0,
            'agent_earnings' => 0.0,
            'total_earnings' => 0.0,
            'transaction_count' => 0,
            'transactions' => [],
            'vat_deductions' => 0.0,
            'marketing_deductions' => 0.0,
            'marketing_agent_earnings' => 0.0,
            'paid_amount' => 0.0,
            'entitled_amount' => 0.0,
            'outstanding_amount' => 0.0,
            'landlord_bonuses' => []
        ];

        foreach ($rentalCodes as $code) {
            $totalFee = (float) ($code->consultation_fee ?? 0);
            if ($totalFee <= 0) continue;

            $rentalDate = $code->rental_date ?? ($code->created_at ?? now());
            $paymentMethod = $code->payment_method ?? 'Cash';
            $clientCount = $code->client_count ?? 1;

            $baseCommission = $totalFee;
            if (in_array($paymentMethod, ['Transfer', 'Card machine'])) {
                $baseCommission = $totalFee * 0.8;
            }

            // Regular rental agent gets 55%; adjust if different marketing agent exists
            $agencyCut = $baseCommission * 0.45;
            $agentCut = $baseCommission * 0.55;

            $hasDifferentMarketingAgent = false;
            if (!empty($code->marketing_agent_id) && !empty($code->rental_agent_id)) {
                $hasDifferentMarketingAgent = (int) $code->marketing_agent_id !== (int) $code->rental_agent_id;
            } else {
                $rentAgentNameCheck = $code->rent_by_agent_name;
                $marketingAgentNameCheck = $code->marketing_agent_name;
                if ($marketingAgentNameCheck === 'N/A') { $marketingAgentNameCheck = null; }
                if (!empty($marketingAgentNameCheck) && !empty($rentAgentNameCheck)) {
                    $hasDifferentMarketingAgent = trim($marketingAgentNameCheck) !== trim($rentAgentNameCheck);
                }
            }

            if ($hasDifferentMarketingAgent) {
                $marketingDeduction = $clientCount >= 2 ? 40 : 30;
                $agentCut -= $marketingDeduction;
                $agentData['marketing_deductions'] += $marketingDeduction;
                $agentData['marketing_agent_earnings'] += $marketingDeduction;
            }

            $agentData['agency_earnings'] += $agencyCut;
            $agentData['agent_earnings'] += $agentCut;
            $agentData['total_earnings'] += $baseCommission;
            $agentData['transaction_count'] += 1;

            if (in_array($paymentMethod, ['Transfer', 'Card machine'])) {
                $vatAmount = $totalFee - $baseCommission;
                $agentData['vat_deductions'] += $vatAmount;
            }

            $isPaid = $code->paid ?? false;
            if ($isPaid) {
                $agentData['paid_amount'] += $agentCut;
            } else {
                $agentData['entitled_amount'] += $agentCut;
            }
            $agentData['outstanding_amount'] = max(0, $agentData['entitled_amount'] - $agentData['paid_amount']);

            $agentData['transactions'][] = [
                'id' => $code->id,
                'total_fee' => $totalFee,
                'base_commission' => $baseCommission,
                'agency_cut' => $agencyCut,
                'agent_cut' => $agentCut,
                'vat_amount' => in_array($paymentMethod, ['Transfer', 'Card machine']) ? ($totalFee - $baseCommission) : 0,
                'marketing_deduction' => $marketingDeduction ?? 0,
                'marketing_agent' => $code->marketing_agent_name,
                'client_count' => $clientCount,
                'paid' => $isPaid,
                'paid_at' => $code->paid_at,
                'date' => $rentalDate,
                'code' => $code->rental_code ?? 'N/A',
                'status' => $code->status ?? 'Unknown',
                'payment_method' => $paymentMethod,
                'is_marketing_earnings' => false,
            ];
        }

        // Also include rentals where this agent acted as the marketing agent (not the rental agent)
        $marketingQuery = RentalCode::with(['client', 'client.agent', 'rentalAgent', 'marketingAgentUser'])
            ->where('marketing_agent_id', (int)$agentId)
            ->where(function($q) use ($endDateTime) {
                $q->where(function($q2) use ($endDateTime) {
                    $q2->whereNotNull('rental_date')
                       ->where('rental_date', '<=', $endDateTime->toDateString());
                })->orWhere(function($q3) use ($endDateTime) {
                    $q3->whereNull('rental_date')
                       ->where('created_at', '<=', $endDateTime);
                });
            })
            // ensure it's a different rental agent; no marketing commission if same person
            ->where(function($q) use ($agentId) {
                $q->whereNull('rental_agent_id')
                  ->orWhere('rental_agent_id', '!=', (int)$agentId);
            })
            ->where('status', 'approved');

        if ($startDateTime) {
            $marketingQuery->where(function($q) use ($startDateTime) {
                $q->where(function($q2) use ($startDateTime) {
                    $q2->whereNotNull('rental_date')
                       ->where('rental_date', '>=', $startDateTime->toDateString());
                })->orWhere(function($q3) use ($startDateTime) {
                    $q3->whereNull('rental_date')
                       ->where('created_at', '>=', $startDateTime);
                });
            });
        }

        if ($status) {
            $marketingQuery->where('status', $status);
        }

        if ($paymentMethod) {
            $marketingQuery->where('payment_method', $paymentMethod);
        }

        $marketingCodes = $marketingQuery->get()->filter(function($code) use ($startDate, $endDate) {
            $date = $code->rental_date ?? ($code->created_at ?? now());
            $dateStr = $date instanceof \Carbon\Carbon ? $date->toDateString() : (string)$date;
            $inCycle = $dateStr >= $startDate && $dateStr <= $endDate;
            return $inCycle || !($code->paid ?? false);
        });

        foreach ($marketingCodes as $code) {
            $totalFee = (float) ($code->consultation_fee ?? 0);
            if ($totalFee <= 0) continue;

            $rentalDate = $code->rental_date ?? ($code->created_at ?? now());
            $paymentMethod = $code->payment_method ?? 'Cash';
            $clientCount = $code->client_count ?? 1;

            $baseCommission = $totalFee;
            if (in_array($paymentMethod, ['Transfer', 'Card machine'])) {
                $baseCommission = $totalFee * 0.8;
            }

            // Marketing agent gets fixed commission: £30 (1 client) or £40 (2+ clients)
            $marketingCommission = $clientCount >= 2 ? 40 : 30;
            $agencyCut = max(0, $baseCommission - $marketingCommission);
            $agentCut = $marketingCommission;

            // Totals
            $agentData['agency_earnings'] += $agencyCut;
            $agentData['agent_earnings'] += $agentCut;
            $agentData['total_earnings'] += $baseCommission;
            $agentData['transaction_count'] += 1;
            $agentData['marketing_agent_earnings'] += $marketingCommission;

            if (in_array($paymentMethod, ['Transfer', 'Card machine'])) {
                $vatAmount = $totalFee - $baseCommission;
                $agentData['vat_deductions'] += $vatAmount;
            }

            $isPaid = $code->paid ?? false;
            if ($isPaid) {
                $agentData['paid_amount'] += $agentCut;
            } else {
                $agentData['entitled_amount'] += $agentCut;
            }
            $agentData['outstanding_amount'] = max(0, $agentData['entitled_amount'] - $agentData['paid_amount']);

            $agentData['transactions'][] = [
                'id' => $code->id,
                'total_fee' => $totalFee,
                'base_commission' => $baseCommission,
                'agency_cut' => $agencyCut,
                'agent_cut' => $agentCut,
                'vat_amount' => in_array($paymentMethod, ['Transfer', 'Card machine']) ? ($totalFee - $baseCommission) : 0,
                'marketing_deduction' => 0,
                'marketing_agent' => $agentUser->name,
                'client_count' => $clientCount,
                'paid' => $isPaid,
                'paid_at' => $code->paid_at,
                'date' => $rentalDate,
                'code' => $code->rental_code ?? 'N/A',
                'status' => $code->status ?? 'Unknown',
                'payment_method' => $paymentMethod,
                'is_marketing_earnings' => true,
            ];
        }

        // Landlord bonuses by agent user id
        $landlordBonuses = \App\Models\LandlordBonus::with(['agent.user'])
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere('status', '!=', 'paid');
            })
            ->get()
            ->filter(function($bonus) use ($agentId) {
                return $bonus->agent && $bonus->agent->user && (int)$bonus->agent->user->id === (int)$agentId;
            });

        foreach ($landlordBonuses as $bonus) {
            $bonusAmount = $bonus->agent_commission;
            $agentData['total_earnings'] += $bonusAmount;
            $agentData['agent_earnings'] += $bonusAmount;
            $agentData['transaction_count'] += 1;
            if ($bonus->status === 'paid') {
                $agentData['paid_amount'] += $bonusAmount;
            } else {
                $agentData['entitled_amount'] += $bonusAmount;
            }
            $agentData['outstanding_amount'] = max(0, $agentData['entitled_amount'] - $agentData['paid_amount']);
            $agentData['landlord_bonuses'][] = [
                'bonus_code' => $bonus->bonus_code,
                'date' => $bonus->date,
                'landlord' => $bonus->landlord,
                'property' => $bonus->property,
                'client' => $bonus->client,
                'commission' => $bonus->commission,
                'agent_commission' => $bonusAmount,
                'bonus_split' => $bonus->bonus_split,
                'status' => $bonus->status,
                'notes' => $bonus->notes,
                'type' => 'landlord_bonus'
            ];
        }

        // Section totals (rentals and bonuses)
        $rentalCount = 0;
        $rentalPaid = 0.0;
        $rentalEntitled = 0.0;
        $rentalAgentCut = 0.0;
        foreach ($agentData['transactions'] as $t) {
            if (!($t['is_marketing_earnings'] ?? false)) {
                $rentalCount += 1;
                $rentalAgentCut += (float) ($t['agent_cut'] ?? 0);
                if (!empty($t['paid'])) {
                    $rentalPaid += (float) ($t['agent_cut'] ?? 0);
                } else {
                    $rentalEntitled += (float) ($t['agent_cut'] ?? 0);
                }
            }
        }
        $agentData['rental_totals'] = [
            'count' => $rentalCount,
            'agent_cut' => $rentalAgentCut,
            'paid' => $rentalPaid,
            'entitled' => $rentalEntitled,
            'outstanding' => max(0, $rentalEntitled),
        ];

        $bonusCount = count($agentData['landlord_bonuses']);
        $bonusPaid = 0.0;
        $bonusEntitled = 0.0;
        $bonusCommission = 0.0;
        foreach ($agentData['landlord_bonuses'] as $b) {
            $bonusCommission += (float) ($b['agent_commission'] ?? 0);
            if (($b['status'] ?? '') === 'paid') {
                $bonusPaid += (float) ($b['agent_commission'] ?? 0);
            } else {
                $bonusEntitled += (float) ($b['agent_commission'] ?? 0);
            }
        }
        $agentData['bonus_totals'] = [
            'count' => $bonusCount,
            'agent_commission' => $bonusCommission,
            'paid' => $bonusPaid,
            'entitled' => $bonusEntitled,
            'outstanding' => max(0, $bonusEntitled),
        ];
        return view('admin.rental-codes.agent-payroll', [
            'agent' => $agentData,
            'agentName' => $agentUser->name,
            'startDate' => $startDate,
            'endDate' => $endDate,
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
     * Bulk mark rentals as paid (admin only)
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
            'rental_code_ids' => 'required|array',
            'rental_code_ids.*' => 'integer|exists:rental_codes,id',
        ]);

        $ids = $validated['rental_code_ids'];

        try {
            \Log::info('Bulk mark as paid', ['count' => count($ids)]);
            \App\Models\RentalCode::whereIn('id', $ids)->update([
                'paid' => true,
                'paid_at' => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Selected rentals marked as paid.',
                ]);
            }
            return redirect()->back()->with('success', 'Selected rentals marked as paid.');
        } catch (\Exception $e) {
            \Log::error('Bulk mark as paid failed', ['error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark selected rentals as paid: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to mark selected rentals as paid.');
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
            'payment_method' => ['nullable', 'in:Cash,Transfer,Card machine'],
        ]);

        // Commission cycle defaults: 11th -> 10th; restrict to current cycle by default
        $today = now();
        if ($today->day <= 10) {
            $cycleStart = $today->copy()->subMonthNoOverflow()->day(11);
            $cycleEnd = $today->copy()->day(10);
        } else {
            $cycleStart = $today->copy()->day(11);
            $cycleEnd = $today->copy()->addMonthNoOverflow()->day(10);
        }
        $cycleStartDate = $cycleStart->toDateString();
        $cycleEndDate = min($today->toDateString(), $cycleEnd->toDateString());

        $startDate = $validated['start_date'] ?? $cycleStartDate;
        $endDate = $validated['end_date'] ?? $cycleEndDate;
        $status = $validated['status'] ?? null;
        $paymentMethod = $validated['payment_method'] ?? null;

        // Build query with filters
        $query = RentalCode::with('client');
        
        // Current cycle rentals OR any unpaid rentals (carry over)
        $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('rental_date', [$startDate, $endDate])
              ->orWhere('paid', false);
        });
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
            if (in_array($paymentMethod, ['Transfer', 'Card machine'])) {
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
            if (in_array($paymentMethod, ['Transfer', 'Card machine'])) {
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
        
        // Load the relationships
        $rentalCode->load(['rentalAgent', 'marketingAgentUser', 'client']);
        
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
            'rental_agent_name' => $rentalCode->rentalAgent->name ?? 'N/A',
            'marketing_agent_name' => $rentalCode->marketingAgentUser->name ?? 'N/A',
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
        try {
            $whatsappService = new WhatsAppService();
            
            // Send admin notification for new rental code
            $result = $whatsappService->sendRentalCodeAdminNotification($rentalCode, $client);
            
            if ($result['success']) {
                \Log::info('WhatsApp admin notification sent successfully', [
                    'rental_code' => $rentalCode->rental_code,
                    'sid' => $result['sid'] ?? null
                ]);
            } else {
                \Log::warning('WhatsApp admin notification failed', [
                    'rental_code' => $rentalCode->rental_code,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp notifications', [
                'rental_code' => $rentalCode->rental_code,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send WhatsApp update notification for rental code
     */
    private function sendWhatsAppUpdateNotification(RentalCode $rentalCode, $client)
    {
        try {
            $whatsappService = new WhatsAppService();
            
            // Send admin notification for rental code update
            $result = $whatsappService->sendRentalCodeUpdateNotification($rentalCode, $client, 'updated');
            
            if ($result['success']) {
                \Log::info('WhatsApp update notification sent successfully', [
                    'rental_code' => $rentalCode->rental_code,
                    'sid' => $result['sid'] ?? null
                ]);
            } else {
                \Log::warning('WhatsApp update notification failed', [
                    'rental_code' => $rentalCode->rental_code,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp update notification', [
                'rental_code' => $rentalCode->rental_code,
                'error' => $e->getMessage()
            ]);
        }
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
     * Approve all pending rental codes (admin only)
     */
    public function approveAllPending(Request $request)
    {
        // Only admin users can approve all pending rentals
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators can approve all pending rentals.',
            ], 403);
        }

        try {
            // Get all pending rental codes
            $pendingRentalCodes = RentalCode::where('status', 'pending')->pluck('id')->toArray();
            
            if (empty($pendingRentalCodes)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No pending rental codes found.',
                    'updated_count' => 0
                ]);
            }

            // Update all pending rental codes to approved
            $updatedCount = RentalCode::whereIn('id', $pendingRentalCodes)
                ->update([
                    'status' => 'approved',
                    'updated_at' => now()
                ]);

            \Log::info('Approved all pending rental codes', [
                'count' => $updatedCount,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully approved {$updatedCount} pending rental code(s).",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to approve all pending rental codes', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve pending rental codes. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle document removal for rental codes
     */
    private function handleDocumentRemoval(Request $request, RentalCode $rentalCode)
    {
        if ($request->has('removed_client_contracts') && !empty($request->input('removed_client_contracts'))) {
            $removedIndices = json_decode($request->input('removed_client_contracts'), true);
            
            if (is_array($removedIndices) && count($removedIndices) > 0) {
                // Get current client contracts
                $currentContracts = null;
                if ($rentalCode->client_contract) {
                    if (is_string($rentalCode->client_contract)) {
                        $decoded = json_decode($rentalCode->client_contract, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $currentContracts = $decoded;
                        } else {
                            $currentContracts = [$rentalCode->client_contract];
                        }
                    } elseif (is_array($rentalCode->client_contract)) {
                        $currentContracts = $rentalCode->client_contract;
                    } else {
                        $currentContracts = [$rentalCode->client_contract];
                    }
                }
                
                if ($currentContracts && is_array($currentContracts)) {
                    // Remove files at specified indices (sort descending to maintain indices)
                    rsort($removedIndices);
                    foreach ($removedIndices as $index) {
                        if (isset($currentContracts[$index])) {
                            // Delete the file from storage
                            $filePath = $currentContracts[$index];
                            $fullPath = storage_path('app/public/' . $filePath);
                            if (file_exists($fullPath)) {
                                try {
                                    unlink($fullPath);
                                    \Log::info('Deleted client contract file', [
                                        'rental_code_id' => $rentalCode->id,
                                        'file_path' => $filePath
                                    ]);
                                } catch (\Exception $e) {
                                    \Log::error('Failed to delete client contract file', [
                                        'rental_code_id' => $rentalCode->id,
                                        'file_path' => $filePath,
                                        'error' => $e->getMessage()
                                    ]);
                                }
                            }
                            // Remove from array
                            unset($currentContracts[$index]);
                        }
                    }
                    
                    // Re-index array and update
                    $currentContracts = array_values($currentContracts);
                    
                    if (count($currentContracts) > 0) {
                        $rentalCode->client_contract = json_encode($currentContracts);
                    } else {
                        $rentalCode->client_contract = null;
                    }
                    
                    $rentalCode->save();
                    
                    \Log::info('Document removal completed', [
                        'rental_code_id' => $rentalCode->id,
                        'removed_indices' => $removedIndices,
                        'remaining_count' => count($currentContracts)
                    ]);
                }
            }
        }
    }

    /**
     * Handle file uploads for rental codes - Simplified and reliable
     */
    private function handleFileUploads(Request $request, RentalCode $rentalCode)
    {
        \Log::info('Starting file upload processing', [
            'rental_code_id' => $rentalCode->id,
            'has_client_contract' => $request->hasFile('client_contract'),
            'has_payment_proof' => $request->hasFile('payment_proof'),
            'has_client_id_document' => $request->hasFile('client_id_document'),
            'client_contract_count' => $request->hasFile('client_contract') ? count($request->file('client_contract')) : 0,
            'payment_proof_count' => $request->hasFile('payment_proof') ? count($request->file('payment_proof')) : 0,
            'client_id_document_count' => $request->hasFile('client_id_document') ? count($request->file('client_id_document')) : 0
        ]);
        
        try {
            // Process client contracts
            $this->processFileUploads($request, 'client_contract', 'rental-codes/documents', $rentalCode);
            
            // Process payment proofs
            $this->processFileUploads($request, 'payment_proof', 'rental-codes/documents', $rentalCode);
            
            // Process client ID documents
            $this->processFileUploads($request, 'client_id_document', 'rental-codes/documents', $rentalCode);
            
            // Log final state
            $rentalCode->refresh();
            \Log::info('File upload processing completed', [
                'rental_code_id' => $rentalCode->id,
                'client_contract' => $rentalCode->client_contract,
                'payment_proof' => $rentalCode->payment_proof,
                'client_id_document' => $rentalCode->client_id_document
            ]);
        } catch (\Exception $e) {
            \Log::error('File upload processing failed', [
                'rental_code_id' => $rentalCode->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Process file uploads for a specific field
     */
    private function processFileUploads(Request $request, $fieldName, $storagePath, RentalCode $rentalCode)
    {
        if (!$request->hasFile($fieldName)) {
            \Log::info("No files found for field: {$fieldName}");
            return;
        }
        
        // Ensure storage directory exists
        $fullStoragePath = storage_path('app/public/' . $storagePath);
        if (!file_exists($fullStoragePath)) {
            \Log::info("Creating storage directory: {$fullStoragePath}");
            \File::makeDirectory($fullStoragePath, 0755, true);
        }
        
        $files = $request->file($fieldName);
        \Log::info("Processing {$fieldName} uploads", [
            'count' => count($files),
            'field' => $fieldName,
            'storage_path' => $storagePath,
            'full_storage_path' => $fullStoragePath
        ]);
        
        // Get existing files for this field (to append new ones)
        $existingFiles = [];
        $currentValue = $rentalCode->$fieldName;
        if ($currentValue) {
            if (is_string($currentValue)) {
                $decoded = json_decode($currentValue, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $existingFiles = $decoded;
                } else {
                    // Old format - single string, convert to array
                    $existingFiles = [$currentValue];
                }
            } elseif (is_array($currentValue)) {
                $existingFiles = $currentValue;
            } else {
                $existingFiles = [$currentValue];
            }
        }
        
        // Store all valid files
        $storedPaths = $existingFiles; // Start with existing files
        
        foreach ($files as $index => $file) {
            if ($file && $file->isValid() && $file->getSize() > 0) {
                try {
                    $path = $file->store($storagePath, 'public');
                    $storedPaths[] = $path; // Add to array
                    \Log::info("Stored file for {$fieldName}", [
                        'index' => $index,
                        'path' => $path,
                        'size' => $file->getSize(),
                        'name' => $file->getClientOriginalName()
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Failed to store file for {$fieldName}", [
                        'index' => $index,
                        'error' => $e->getMessage(),
                        'file' => $file->getClientOriginalName()
                    ]);
                }
            } else {
                \Log::warning("Invalid file skipped for {$fieldName}", [
                    'index' => $index,
                    'valid' => $file ? $file->isValid() : false,
                    'size' => $file ? $file->getSize() : 0,
                    'error' => $file ? $file->getError() : 'No file'
                ]);
            }
        }
        
        // Update the field with JSON array (or single string for backward compatibility if only one file)
        try {
            if (count($storedPaths) > 0) {
                if (count($storedPaths) === 1) {
                    // For backward compatibility, store as single string if only one file
                    $rentalCode->update([$fieldName => $storedPaths[0]]);
                } else {
                    // Store as JSON array for multiple files
                    $rentalCode->update([$fieldName => json_encode($storedPaths)]);
                }
            \Log::info("Updated {$fieldName} field", [
                    'total_files' => count($storedPaths),
                    'success' => true
            ]);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to update {$fieldName} field in database", [
                'error' => $e->getMessage(),
                'rental_code_id' => $rentalCode->id
            ]);
        }
    }


    /**
     * Download a file from storage
     */
    public function downloadFile(RentalCode $rentalCode, $field, $index = null)
    {
        try {
            // Normalize index - if null or empty string, default to 0
            if ($index === null || $index === '') {
                $index = 0;
            } else {
                $index = (int) $index;
            }
            
            \Log::info('Download file request', [
                'rental_code_id' => $rentalCode->id,
                'field' => $field,
                'index' => $index,
                'raw_index' => request()->route('index')
            ]);
            
            // Get the file path for the specified field
            $filePath = null;
            $fieldValue = null;
            
            switch ($field) {
                case 'client_contract':
                    $fieldValue = $rentalCode->client_contract;
                    break;
                case 'payment_proof':
                    $fieldValue = $rentalCode->payment_proof;
                    break;
                case 'client_id_document':
                    $fieldValue = $rentalCode->client_id_document;
                    break;
                case 'client_id_image':
                    $fieldValue = $rentalCode->client_id_image;
                    break;
                case 'cash_receipt_image':
                    $fieldValue = $rentalCode->cash_receipt_image;
                    break;
                case 'contact_images':
                    $fieldValue = $rentalCode->contact_images;
                    break;
                default:
                    \Log::warning('Invalid field type requested for download', [
                        'rental_code_id' => $rentalCode->id,
                        'field' => $field
                    ]);
                    abort(404, 'File type not found');
            }

            if (!$fieldValue || empty($fieldValue)) {
                \Log::warning('No file value found for field (download)', [
                    'rental_code_id' => $rentalCode->id,
                    'field' => $field
                ]);
                abort(404, 'No file found for this field');
            }

            // Handle array fields (JSON string or array) - with backward compatibility for old single-string records
            if (is_string($fieldValue)) {
                $decoded = json_decode($fieldValue, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
                    // It's a JSON array (new format with multiple files)
                    if (isset($decoded[$index])) {
                        $filePath = $decoded[$index];
                    } else {
                        \Log::warning('File index not found in array (download)', [
                            'rental_code_id' => $rentalCode->id,
                            'field' => $field,
                            'requested_index' => $index,
                            'available_indices' => array_keys($decoded)
                        ]);
                        abort(404, 'File index not found');
                    }
                } else {
                    // It's a single string path (old format - backward compatibility)
                    // For old records, ignore index and use the single string
                    $filePath = $fieldValue;
                }
            } elseif (is_array($fieldValue)) {
                // Already an array (new format)
                if (isset($fieldValue[$index])) {
                    $filePath = $fieldValue[$index];
                } else {
                    \Log::warning('File index not found in array (download)', [
                        'rental_code_id' => $rentalCode->id,
                        'field' => $field,
                        'requested_index' => $index,
                        'available_indices' => array_keys($fieldValue)
                    ]);
                    abort(404, 'File index not found');
                }
            } else {
                // Fallback for other types (backward compatibility)
                $filePath = $fieldValue;
            }

            if (!$filePath || empty($filePath)) {
                \Log::warning('File path is empty after processing (download)', [
                    'rental_code_id' => $rentalCode->id,
                    'field' => $field,
                    'index' => $index
                ]);
                abort(404, 'No file found for this field');
            }
            
            $fullPath = storage_path('app/public/' . $filePath);
            
            \Log::info('Attempting to download file', [
                'rental_code_id' => $rentalCode->id,
                'field' => $field,
                'file_path' => $filePath,
                'full_path' => $fullPath,
                'file_exists' => file_exists($fullPath)
            ]);

            if (!file_exists($fullPath)) {
                \Log::error('File does not exist on server (download)', [
                    'rental_code_id' => $rentalCode->id,
                    'field' => $field,
                    'file_path' => $filePath,
                    'full_path' => $fullPath
                ]);
                abort(404, 'File does not exist on server: ' . $filePath);
            }

            $originalName = basename($filePath);
            $mimeType = mime_content_type($fullPath);
            
            \Log::info('File found, preparing download', [
                'rental_code_id' => $rentalCode->id,
                'field' => $field,
                'file_path' => $filePath,
                'original_name' => $originalName,
                'mime_type' => $mimeType
            ]);

            return response()->download($fullPath, $originalName, [
                'Content-Type' => $mimeType,
            ]);

        } catch (\Exception $e) {
            \Log::error('File download failed', [
                'rental_code_id' => $rentalCode->id,
                'field' => $field,
                'index' => $index,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Failed to download file: ' . $e->getMessage());
        }
    }

    /**
     * View a file in browser
     */
    public function viewFile(RentalCode $rentalCode, $field, $index = null)
    {
        try {
            // Normalize index - if null or empty string, default to 0
            if ($index === null || $index === '') {
                $index = 0;
            } else {
                $index = (int) $index;
            }
            
            \Log::info('View file request', [
                'rental_code_id' => $rentalCode->id,
                'field' => $field,
                'index' => $index,
                'raw_index' => request()->route('index')
            ]);
            
            // Get the file path for the specified field
            $filePath = null;
            $fieldValue = null;
            
            switch ($field) {
                case 'client_contract':
                    $fieldValue = $rentalCode->client_contract;
                    break;
                case 'payment_proof':
                    $fieldValue = $rentalCode->payment_proof;
                    break;
                case 'client_id_document':
                    $fieldValue = $rentalCode->client_id_document;
                    break;
                case 'client_id_image':
                    $fieldValue = $rentalCode->client_id_image;
                    break;
                case 'cash_receipt_image':
                    $fieldValue = $rentalCode->cash_receipt_image;
                    break;
                case 'contact_images':
                    $fieldValue = $rentalCode->contact_images;
                    break;
                default:
                    \Log::warning('Invalid field type requested', [
                        'rental_code_id' => $rentalCode->id,
                        'field' => $field
                    ]);
                    abort(404, 'File type not found');
            }

            if (!$fieldValue || empty($fieldValue)) {
                \Log::warning('No file value found for field', [
                    'rental_code_id' => $rentalCode->id,
                    'field' => $field,
                    'field_value' => $fieldValue
                ]);
                abort(404, 'No file found for this field');
            }

            \Log::info('Field value retrieved', [
                'rental_code_id' => $rentalCode->id,
                'field' => $field,
                'field_value_type' => gettype($fieldValue),
                'field_value_preview' => is_string($fieldValue) ? substr($fieldValue, 0, 100) : 'array/object'
            ]);

            // Handle array fields (JSON string or array) - with backward compatibility for old single-string records
            if (is_string($fieldValue)) {
                $decoded = json_decode($fieldValue, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
                    // It's a JSON array (new format with multiple files)
                    \Log::info('Field value is JSON array', [
                        'rental_code_id' => $rentalCode->id,
                        'field' => $field,
                        'array_count' => count($decoded),
                        'requested_index' => $index
                    ]);
                    if (isset($decoded[$index])) {
                        $filePath = $decoded[$index];
                    } else {
                        \Log::warning('File index not found in array', [
                            'rental_code_id' => $rentalCode->id,
                            'field' => $field,
                            'requested_index' => $index,
                            'available_indices' => array_keys($decoded)
                        ]);
                        abort(404, 'File index not found');
                    }
                } else {
                    // It's a single string path (old format - backward compatibility)
                    // For old records, ignore index and use the single string
                    \Log::info('Field value is single string', [
                        'rental_code_id' => $rentalCode->id,
                        'field' => $field,
                        'file_path' => $fieldValue
                    ]);
                    $filePath = $fieldValue;
                }
            } elseif (is_array($fieldValue)) {
                // Already an array (new format)
                \Log::info('Field value is array', [
                    'rental_code_id' => $rentalCode->id,
                    'field' => $field,
                    'array_count' => count($fieldValue),
                    'requested_index' => $index
                ]);
                if (isset($fieldValue[$index])) {
                    $filePath = $fieldValue[$index];
                } else {
                    \Log::warning('File index not found in array', [
                        'rental_code_id' => $rentalCode->id,
                        'field' => $field,
                        'requested_index' => $index,
                        'available_indices' => array_keys($fieldValue)
                    ]);
                    abort(404, 'File index not found');
                }
            } else {
                // Fallback for other types (backward compatibility)
                \Log::info('Field value is other type, using as-is', [
                    'rental_code_id' => $rentalCode->id,
                    'field' => $field,
                    'type' => gettype($fieldValue)
                ]);
                $filePath = $fieldValue;
            }

            if (!$filePath || empty($filePath)) {
                \Log::warning('File path is empty after processing', [
                    'rental_code_id' => $rentalCode->id,
                    'field' => $field,
                    'index' => $index
                ]);
                abort(404, 'No file found for this field');
            }
            
            $fullPath = storage_path('app/public/' . $filePath);
            
            \Log::info('Attempting to access file', [
                'rental_code_id' => $rentalCode->id,
                'field' => $field,
                'file_path' => $filePath,
                'full_path' => $fullPath,
                'file_exists' => file_exists($fullPath)
            ]);

            if (!file_exists($fullPath)) {
                \Log::error('File does not exist on server', [
                    'rental_code_id' => $rentalCode->id,
                    'field' => $field,
                    'file_path' => $filePath,
                    'full_path' => $fullPath,
                    'storage_base' => storage_path('app/public'),
                    'directory_exists' => is_dir(storage_path('app/public'))
                ]);
                abort(404, 'File does not exist on server: ' . $filePath);
            }

            $mimeType = mime_content_type($fullPath);
            $fileSize = filesize($fullPath);
            
            \Log::info('File found, serving to browser', [
                'rental_code_id' => $rentalCode->id,
                'field' => $field,
                'file_path' => $filePath,
                'mime_type' => $mimeType,
                'file_size' => $fileSize
            ]);

            return response()->file($fullPath, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
            ]);

        } catch (\Exception $e) {
            \Log::error('File view failed', [
                'rental_code_id' => $rentalCode->id,
                'field' => $field,
                'index' => $index,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Failed to view file: ' . $e->getMessage());
        }
    }

    /**
     * Export rental codes to CSV
     */
    public function export(Request $request)
    {
        $query = RentalCode::with(['client', 'rentalAgent', 'marketingAgentUser']);

        // Apply same filters as index method
        $search = trim((string)$request->input('q', ''));
        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('rental_code', 'like', '%' . $search . '%')
                  ->orWhere('property', 'like', '%' . $search . '%')
                  ->orWhere('licensor', 'like', '%' . $search . '%')
                  ->orWhere('notes', 'like', '%' . $search . '%')
                  ->orWhere('payment_method', 'like', '%' . $search . '%')
                  ->orWhereHas('client', function($cq) use ($search) {
                      $cq->where('full_name', 'like', '%' . $search . '%')
                         ->orWhere('email', 'like', '%' . $search . '%')
                         ->orWhere('phone_number', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by payment method
        $paymentMethod = $request->input('payment_method');
        if (!empty($paymentMethod)) {
            $query->where('payment_method', $paymentMethod);
        }

        // Filter by agent (either rental or marketing agent)
        $agentId = $request->input('agent_id');
        if (!empty($agentId)) {
            $query->where(function($q) use ($agentId) {
                $q->where('rental_agent_id', (int)$agentId)
                  ->orWhere('marketing_agent_id', (int)$agentId);
            });
        }

        // Get all results (no pagination)
        $rentalCodes = $query->orderBy('created_at', 'desc')->get();

        $filename = 'rental_codes_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($rentalCodes) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Rental Code',
                'Rental Date',
                'Client Name',
                'Client Email',
                'Client Phone',
                'Property',
                'Licensor',
                'Consultation Fee',
                'Payment Method',
                'Status',
                'Rental Agent',
                'Marketing Agent',
                'Client Count',
                'Notes',
                'Created At',
                'Updated At'
            ]);
            
            // CSV data
            foreach ($rentalCodes as $rentalCode) {
                fputcsv($file, [
                    $rentalCode->rental_code ?? 'N/A',
                    $rentalCode->rental_date ? $rentalCode->rental_date->format('Y-m-d') : 'N/A',
                    $rentalCode->client->full_name ?? 'N/A',
                    $rentalCode->client->email ?? 'N/A',
                    $rentalCode->client->phone_number ?? 'N/A',
                    $rentalCode->property ?? 'N/A',
                    $rentalCode->licensor ?? 'N/A',
                    number_format($rentalCode->consultation_fee ?? 0, 2),
                    $rentalCode->payment_method ?? 'N/A',
                    ucfirst($rentalCode->status ?? 'N/A'),
                    $rentalCode->rentalAgent->name ?? 'N/A',
                    $rentalCode->marketingAgentUser->name ?? 'N/A',
                    $rentalCode->client_count ?? 1,
                    $rentalCode->notes ?? '',
                    $rentalCode->created_at ? $rentalCode->created_at->format('Y-m-d H:i:s') : 'N/A',
                    $rentalCode->updated_at ? $rentalCode->updated_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
