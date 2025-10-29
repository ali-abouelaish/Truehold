<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class PublicClientController extends Controller
{
    // Show public client intake form (no auth required)
    public function create()
    {
        return view('public.client-intake');
    }

    // Store public client submission
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'nationality' => ['required', 'string', 'max:100'],
            'current_address' => ['required', 'string'],
            'company_university_name' => ['required', 'string', 'max:255'],
            'company_university_address' => ['required', 'string'],
            'position_role' => ['required', 'string', 'max:255'],
            'current_landlord_name' => ['required', 'string', 'max:255'],
            'current_landlord_contact_info' => ['required', 'string'],
            'privacy_consent' => ['accepted'],
        ]);

        // Create client without assigning agent; admin can assign later
        $client = Client::create([
            'full_name' => $validated['full_name'],
            'date_of_birth' => $validated['date_of_birth'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'nationality' => $validated['nationality'],
            'current_address' => $validated['current_address'],
            'company_university_name' => $validated['company_university_name'],
            'company_university_address' => $validated['company_university_address'],
            'position_role' => $validated['position_role'],
            'current_landlord_name' => $validated['current_landlord_name'],
            'current_landlord_contact_info' => $validated['current_landlord_contact_info'],
            'registration_status' => 'unregistered',
            // agent_id left null intentionally
        ]);

        return redirect()->route('public.client.create')
            ->with('success', 'Your application has been submitted successfully. We will contact you soon.');
    }
}


