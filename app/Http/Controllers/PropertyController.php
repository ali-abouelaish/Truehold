<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Property::query();

        // Apply search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%");
            });
        }

        // Apply filters
        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }

        // Price filtering - handle min and max separately
        if ($request->filled('min_price')) {
            $query->byMinPrice($request->min_price);
        }
        
        if ($request->filled('max_price')) {
            $query->byMaxPrice($request->max_price);
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', 'like', "%{$request->property_type}%");
        }

        if ($request->filled('available_date')) {
            $query->where('available_date', 'like', "%{$request->available_date}%");
        }

        // New filters: Management Company and London Area
        if ($request->filled('management_company')) {
            $query->byManagementCompany($request->management_company);
        }

        if ($request->filled('london_area')) {
            $query->byLondonArea($request->london_area);
        }

        if ($request->filled('couples_allowed')) {
            $query->byCouplesAllowed($request->couples_allowed);
        }

        // Get unique values for filter dropdowns
        $locations = Property::distinct()->pluck('location')->filter()->sort()->values();
        $propertyTypes = Property::distinct()->pluck('property_type')->filter()->sort()->values();
        $availableDates = Property::distinct()->pluck('available_date')->filter()->sort()->values();
        $managementCompanies = Property::distinct()->pluck('management_company')->filter()->sort()->values();

        $properties = $query->latest()->paginate(20);

        // Log filter results for debugging
        \Log::info('Property index filters applied', [
            'filters' => $request->all(),
            'total_results' => $properties->total(),
            'current_page' => $properties->currentPage(),
            'per_page' => $properties->perPage(),
            'query_sql' => $query->toSql(),
            'query_bindings' => $query->getBindings(),
            'price_filter_applied' => $request->filled('min_price') || $request->filled('max_price'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'management_company_filter' => $request->input('management_company'),
            'london_area_filter' => $request->input('london_area')
        ]);

        return view('properties.index', compact('properties', 'locations', 'propertyTypes', 'availableDates', 'managementCompanies'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $property = Property::findOrFail($id);
        return view('properties.show', compact('property'));
    }

    /**
     * Display properties on an interactive map.
     */
    public function map(Request $request)
    {
        // Enhanced coordinate validation query using the scope
        $query = Property::query()->withValidCoordinates();

        // Apply search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%");
            });
        }

        // Apply filters
        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }

        // Price filtering - handle min and max separately
        if ($request->filled('min_price')) {
            $query->byMinPrice($request->min_price);
        }
        
        if ($request->filled('max_price')) {
            $query->byMaxPrice($request->max_price);
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', 'like', "%{$request->property_type}%");
        }

        if ($request->filled('available_date')) {
            $query->where('available_date', 'like', "%{$request->available_date}%");
        }

        // New filters: Management Company and London Area
        if ($request->filled('management_company')) {
            $query->byManagementCompany($request->management_company);
        }

        if ($request->filled('london_area')) {
            $query->byLondonArea($request->london_area);
        }

        if ($request->filled('couples_allowed')) {
            $query->byCouplesAllowed($request->couples_allowed);
        }

        // Get unique values for filter dropdowns
        $locations = Property::distinct()->pluck('location')->filter()->sort()->values();
        $propertyTypes = Property::distinct()->pluck('property_type')->sort()->values();
        $availableDates = Property::distinct()->pluck('available_date')->sort()->values();
        $managementCompanies = Property::distinct()->pluck('management_company')->filter()->sort()->values();

        // Limit properties to prevent performance issues
        $properties = $query->limit(100)->get();

        // Log validation results for debugging
        \Log::info('Map query results', [
            'total_properties' => $properties->count(),
            'filters_applied' => $request->all(),
            'query_sql' => $query->toSql(),
            'query_bindings' => $query->getBindings()
        ]);
        
        // Debug: Check if properties have valid coordinates
        $propertiesWithCoords = $properties->filter(function($prop) {
            return !empty($prop->latitude) && !empty($prop->longitude);
        });
        
        \Log::info('Properties with coordinates', [
            'total' => $properties->count(),
            'with_coords' => $propertiesWithCoords->count(),
            'sample_coords' => $propertiesWithCoords->take(3)->map(function($p) {
                return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'lat' => $p->latitude,
                    'lng' => $p->longitude
                ];
            })
        ]);

        return view('properties.map', compact('properties', 'locations', 'propertyTypes', 'availableDates', 'managementCompanies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('properties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'property_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'amenities' => 'nullable|string',
            'bills_included' => 'nullable|string',
            'deposit' => 'nullable|string',
            'minimum_term' => 'nullable|string',
            'furnishings' => 'nullable|string',
            'garden_patio' => 'nullable|string',
            'contact_info' => 'nullable|string',
            'management_company' => 'nullable|string|max:255',
            'available_date' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'nullable|in:available,rented,unavailable,on_hold',
        ]);
        
        $property = Property::create($request->all());
        
        return redirect()->route('properties.show', $property)
            ->with('success', 'Property created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $property = Property::findOrFail($id);
        return view('properties.edit', compact('property'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $property = Property::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'property_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'amenities' => 'nullable|string',
            'bills_included' => 'nullable|string',
            'deposit' => 'nullable|string',
            'minimum_term' => 'nullable|string',
            'furnishings' => 'nullable|string',
            'garden_patio' => 'nullable|string',
            'contact_info' => 'nullable|string',
            'management_company' => 'nullable|string|max:255',
            'available_date' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'nullable|in:available,rented,unavailable,on_hold',
        ]);
        
        $property->update($request->all());
        
        return redirect()->route('properties.show', $property)
            ->with('success', 'Property updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $property = Property::findOrFail($id);
        $property->delete();
        
        return redirect()->route('properties.index')
            ->with('success', 'Property deleted successfully!');
    }
}
