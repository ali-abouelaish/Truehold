<?php

namespace App\Http\Controllers;

use App\Models\ApProperty;
use Illuminate\Http\Request;

class ApPropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = ApProperty::orderByDesc('created_at')->paginate(20);
        return view('admin.ap-properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.ap-properties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'availability' => 'nullable|date',
            'property_name' => 'required|string|max:255',
            'pcm' => 'nullable|integer|min:0',
            'postcode' => 'nullable|string|max:20',
            'area' => 'nullable|string|max:255',
            'n_rooms' => 'nullable|integer|min:0',
            'n_bathrooms' => 'nullable|integer|min:0',
            'status' => 'required|string|in:booked,available_on_date,renewal,empty_available_now',
            'type' => 'required|string|in:full_flat,house_share',
            'is_room' => 'sometimes|boolean',
            'room_label' => 'nullable|string|max:100|required_if:type,house_share|required_if:is_room,1',
            'couples_allowed' => 'sometimes|boolean',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|max:5120',
        ]);

        $data = $validated;
        $data['is_room'] = $request->boolean('is_room');
        $data['couples_allowed'] = $request->boolean('couples_allowed');
        $data['images_url'] = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image) {
                    $data['images_url'][] = $image->store('ap-properties', 'public');
                }
            }
        }

        $property = ApProperty::create($data);

        return redirect()
            ->route('admin.ap-properties.edit', $property)
            ->with('success', 'AP Property created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ApProperty $ap_property)
    {
        return view('admin.ap-properties.show', ['property' => $ap_property]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApProperty $ap_property)
    {
        return view('admin.ap-properties.edit', ['property' => $ap_property]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApProperty $ap_property)
    {
        $validated = $request->validate([
            'availability' => 'nullable|date',
            'property_name' => 'required|string|max:255',
            'pcm' => 'nullable|integer|min:0',
            'postcode' => 'nullable|string|max:20',
            'area' => 'nullable|string|max:255',
            'n_rooms' => 'nullable|integer|min:0',
            'n_bathrooms' => 'nullable|integer|min:0',
            'status' => 'required|string|in:booked,available_on_date,renewal,empty_available_now',
            'type' => 'required|string|in:full_flat,house_share',
            'is_room' => 'sometimes|boolean',
            'room_label' => 'nullable|string|max:100|required_if:type,house_share|required_if:is_room,1',
            'couples_allowed' => 'sometimes|boolean',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|max:5120',
        ]);

        $data = $validated;
        $data['is_room'] = $request->boolean('is_room');
        $data['couples_allowed'] = $request->boolean('couples_allowed');

        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $image) {
                if ($image) {
                    $paths[] = $image->store('ap-properties', 'public');
                }
            }
            $data['images_url'] = $paths;
        } else {
            $data['images_url'] = $ap_property->images_url ?? [];
        }

        $ap_property->update($data);

        return redirect()
            ->route('admin.ap-properties.edit', $ap_property)
            ->with('success', 'AP Property updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApProperty $ap_property)
    {
        $ap_property->delete();

        return redirect()
            ->route('admin.ap-properties.index')
            ->with('success', 'AP Property deleted successfully.');
    }
}


