<?php

namespace App\Http\Controllers;

use App\Models\GroupViewing;
use App\Models\GroupViewingAttendee;
use App\Models\Property;
use App\Models\Client;
use Illuminate\Http\Request;

class GroupViewingController extends Controller
{
    public function index()
    {
        $groupViewings = GroupViewing::with(['property', 'attendees.client'])->orderBy('scheduled_at', 'desc')->paginate(20);

        // Properties (rooms) with interested clients and their counts
        $propertiesWithInterests = Property::withCount('interests')
            ->with(['interests.client'])
            ->having('interests_count', '>', 0)
            ->orderByDesc('interests_count')
            ->get();

        return view('admin.group-viewings.index', compact('groupViewings', 'propertiesWithInterests'));
    }

    public function create(Request $request)
    {
        $properties = Property::orderBy('title')->get();
        $clients = Client::orderBy('full_name')->get();
        $property = null;
        if ($request->filled('property_id')) {
            $property = Property::with('interests.client')->find($request->input('property_id'));
        }
        return view('admin.group-viewings.create', compact('properties', 'clients', 'property'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'scheduled_at' => 'required|date',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'attendees' => 'nullable|array',
            'attendees.*' => 'exists:clients,id',
        ]);

        $groupViewing = GroupViewing::create([
            'property_id' => $data['property_id'],
            'scheduled_at' => $data['scheduled_at'],
            'location' => $data['location'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by_user_id' => auth()->id(),
        ]);

        if (!empty($data['attendees'])) {
            foreach ($data['attendees'] as $clientId) {
                GroupViewingAttendee::firstOrCreate([
                    'group_viewing_id' => $groupViewing->id,
                    'client_id' => $clientId,
                ]);
            }
        }

        return redirect()->route('admin.group-viewings.index')->with('success', 'Group viewing created successfully.');
    }

    public function attendees(GroupViewing $groupViewing)
    {
        $attendees = $groupViewing->attendees()->with('client')->get();
        
        return response()->json([
            'attendees' => $attendees->map(function ($attendee) {
                return [
                    'id' => $attendee->id,
                    'status' => $attendee->status,
                    'notes' => $attendee->notes,
                    'client' => [
                        'id' => $attendee->client->id,
                        'full_name' => $attendee->client->full_name,
                        'email' => $attendee->client->email,
                        'phone_number' => $attendee->client->phone_number,
                    ]
                ];
            })
        ]);
    }
}


