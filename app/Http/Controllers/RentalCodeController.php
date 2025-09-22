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
        $agentUsers = User::where(function($query) {
            $query->where('role', 'agent')
                  ->orWhereHas('agent');
        })->with('agent')->get();
        
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
        $agentUsers = User::where(function($query) {
            $query->where('role', 'agent')
                  ->orWhereHas('agent');
        })->with('agent')->get();
        
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
     * Show agent earnings report
     */
    public function agentEarnings(Request $request)
    {
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Get all rental codes up to the specified date
        $rentalCodes = RentalCode::where('rental_date', '<=', $endDate)
            ->where('status', '!=', 'cancelled')
            ->get();

        // Calculate earnings by agent
        $agentEarnings = [];
        
        foreach ($rentalCodes as $rentalCode) {
            // Handle rent_by_agent earnings
            if (!empty($rentalCode->rent_by_agent)) {
                $agentName = $rentalCode->rent_by_agent;
                if (!isset($agentEarnings[$agentName])) {
                    $agentEarnings[$agentName] = [
                        'name' => $agentName,
                        'rent_earnings' => 0,
                        'client_earnings' => 0,
                        'total_earnings' => 0,
                        'rent_count' => 0,
                        'client_count' => 0,
                        'total_count' => 0
                    ];
                }
                $agentEarnings[$agentName]['rent_earnings'] += $rentalCode->consultation_fee;
                $agentEarnings[$agentName]['rent_count']++;
                $agentEarnings[$agentName]['total_earnings'] += $rentalCode->consultation_fee;
                $agentEarnings[$agentName]['total_count']++;
            }
            
            // Handle client_by_agent earnings
            if (!empty($rentalCode->client_by_agent)) {
                $agentName = $rentalCode->client_by_agent;
                if (!isset($agentEarnings[$agentName])) {
                    $agentEarnings[$agentName] = [
                        'name' => $agentName,
                        'rent_earnings' => 0,
                        'client_earnings' => 0,
                        'total_earnings' => 0,
                        'rent_count' => 0,
                        'client_count' => 0,
                        'total_count' => 0
                    ];
                }
                $agentEarnings[$agentName]['client_earnings'] += $rentalCode->consultation_fee;
                $agentEarnings[$agentName]['client_count']++;
                $agentEarnings[$agentName]['total_earnings'] += $rentalCode->consultation_fee;
                $agentEarnings[$agentName]['total_count']++;
            }
        }

        // Sort by total earnings (descending)
        uasort($agentEarnings, function($a, $b) {
            return $b['total_earnings'] <=> $a['total_earnings'];
        });

        // Calculate totals
        $totalEarnings = array_sum(array_column($agentEarnings, 'total_earnings'));
        $totalRentalCodes = $rentalCodes->count();

        return view('admin.rental-codes.agent-earnings', compact(
            'agentEarnings', 
            'totalEarnings', 
            'totalRentalCodes', 
            'endDate'
        ));
    }
}
