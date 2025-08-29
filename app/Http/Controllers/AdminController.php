<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalProperties = Property::count();
        $propertiesWithCoords = Property::withValidCoordinates()->count();
        $recentProperties = Property::latest()->take(5)->get();
        
        return view('admin.dashboard', compact('totalProperties', 'propertiesWithCoords', 'recentProperties'));
    }

    public function properties()
    {
        $properties = Property::latest()->paginate(20);
        return view('admin.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('admin.properties.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'property_type' => 'required|string|max:100',
            'available_date' => 'nullable|string|max:100',
            'status' => 'required|string|in:available,rented,unavailable',
            'management_company' => 'nullable|string|max:255',
            'amenities' => 'nullable|string',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Handle image uploads
        $photos = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                $path = Storage::disk('public')->putFileAs('images/properties', $image, $filename);
                $photos[] = Storage::disk('public')->url($path);
            }
        }

        // Create property data
        $propertyData = $request->except(['images']);
        $propertyData['photos'] = $photos; // Store as array (Laravel will handle JSON conversion)
        $propertyData['first_photo_url'] = $photos[0] ?? null;
        $propertyData['photo_count'] = count($photos);

        $property = Property::create($propertyData);

        return redirect()->route('admin.properties')
            ->with('success', 'Property created successfully with ' . count($photos) . ' images!');
    }

    // User Management Methods
    public function users()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'Agent user created successfully!');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users')
            ->with('success', 'Agent user updated successfully!');
    }

    public function destroyUser(User $user)
    {
        // Prevent deleting the current user
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Agent user deleted successfully!');
    }

    public function edit(Property $property)
    {
        return view('admin.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'property_type' => 'required|string|max:100',
            'available_date' => 'nullable|string|max:100',
            'status' => 'required|string|in:available,rented,unavailable',
            'management_company' => 'nullable|string|max:255',
            'amenities' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'removed_images' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Get current photos
        $currentPhotos = [];
        if ($property->photos) {
            $currentPhotos = is_array($property->photos) ? $property->photos : [];
        }

        // Handle removed images
        if ($request->filled('removed_images')) {
            $removedIndices = json_decode($request->input('removed_images'), true) ?: [];
            // Remove images from the end to avoid index shifting issues
            rsort($removedIndices);
            foreach ($removedIndices as $index) {
                if (isset($currentPhotos[$index])) {
                    unset($currentPhotos[$index]);
                }
            }
            // Re-index array
            $currentPhotos = array_values($currentPhotos);
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                $path = Storage::disk('public')->putFileAs('images/properties', $image, $filename);
                $currentPhotos[] = Storage::disk('public')->url($path);
            }
        }

        // Handle first photo selection
        if ($request->has('first_photo') && !empty($currentPhotos)) {
            $firstPhotoIndex = (int) $request->input('first_photo');
            if (isset($currentPhotos[$firstPhotoIndex])) {
                // Move selected photo to first position
                $selectedPhoto = $currentPhotos[$firstPhotoIndex];
                unset($currentPhotos[$firstPhotoIndex]);
                array_unshift($currentPhotos, $selectedPhoto);
            }
        }

        // Update property data
        $propertyData = $request->except(['images', 'first_photo']);
        $propertyData['photos'] = $currentPhotos; // Store as array (Laravel will handle JSON conversion)
        $propertyData['first_photo_url'] = $currentPhotos[0] ?? null;
        $propertyData['photo_count'] = count($currentPhotos);

        $property->update($propertyData);

        return redirect()->route('admin.properties')
            ->with('success', 'Property updated successfully!');
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return redirect()->route('admin.properties')
            ->with('success', 'Property deleted successfully!');
    }

    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            
            // Store in public/images/properties
            $path = Storage::disk('public')->putFileAs('images/properties', $file, $filename);
            
            $url = Storage::disk('public')->url($path);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'filename' => $filename
            ]);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }

    // Client Management Methods
    public function clients()
    {
        $clients = \App\Models\Client::with('agent')->latest()->paginate(20);
        $agents = \App\Models\Agent::all();
        $nationalities = \App\Models\Client::distinct()->pluck('nationality')->filter()->sort()->values();
        
        return view('admin.clients.index', compact('clients', 'agents', 'nationalities'));
    }

    public function createClient()
    {
        $agents = \App\Models\Agent::all();
        return view('admin.clients.create', compact('agents'));
    }

    public function storeClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'nationality' => 'nullable|string|max:100',
            'current_address' => 'nullable|string',
            'company_university_name' => 'nullable|string|max:255',
            'company_university_address' => 'nullable|string',
            'position_role' => 'nullable|string|max:255',
            'agent_id' => 'nullable|exists:agents,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        \App\Models\Client::create($request->all());

        return redirect()->route('admin.clients')
            ->with('success', 'Client created successfully!');
    }

    public function editClient(\App\Models\Client $client)
    {
        $agents = \App\Models\Agent::all();
        return view('admin.clients.edit', compact('client', 'agents'));
    }

    public function updateClient(Request $request, \App\Models\Client $client)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'nationality' => 'nullable|string|max:100',
            'current_address' => 'nullable|string',
            'company_university_name' => 'nullable|string|max:255',
            'company_university_address' => 'nullable|string',
            'position_role' => 'nullable|string|max:255',
            'agent_id' => 'nullable|exists:agents,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $client->update($request->all());

        return redirect()->route('admin.clients')
            ->with('success', 'Client updated successfully!');
    }

    public function destroyClient(\App\Models\Client $client)
    {
        $client->delete();
        return redirect()->route('admin.clients')
            ->with('success', 'Client deleted successfully!');
    }
}
