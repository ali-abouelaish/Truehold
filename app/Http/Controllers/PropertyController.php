<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyFromSheet;
use App\Models\Client;
use App\Services\PropertyGoogleSheetsService;
use Illuminate\Pagination\LengthAwarePaginator;

class PropertyController extends Controller
{
    protected $sheetsService;

    public function __construct(PropertyGoogleSheetsService $sheetsService)
    {
        $this->sheetsService = $sheetsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if Google Sheets is configured
        $useGoogleSheets = !empty(config('services.google.properties.spreadsheet_id'));
        
        if ($useGoogleSheets) {
            try {
                // Build filters array
                $filters = [];
                
                if ($request->filled('location')) {
                    $filters['location'] = $request->location;
                }

                if ($request->filled('min_price')) {
                    $filters['min_price'] = $request->min_price;
                }
                
                if ($request->filled('max_price')) {
                    $filters['max_price'] = $request->max_price;
                }

                if ($request->filled('property_type')) {
                    $filters['property_type'] = $request->property_type;
                }

                if ($request->filled('available_date')) {
                    $filters['available_date'] = $request->available_date;
                }

                if ($request->filled('management_company')) {
                    $filters['management_company'] = $request->management_company;
                }

                if ($request->filled('agent_name') && auth()->check()) {
                    $filters['agent_name'] = $request->agent_name;
                }

                if ($request->filled('couples_allowed')) {
                    $filters['couples_allowed'] = $request->couples_allowed;
                }

                // Get filtered properties from Google Sheets
                $filteredProperties = $this->sheetsService->filterProperties($filters);
                
                // Get filter values for dropdowns
                $filterValues = $this->sheetsService->getFilterValues();
                $locations = $filterValues['locations'];
                $propertyTypes = $filterValues['propertyTypes'];
                $availableDates = $filterValues['available_dates'];
                $agentNames = $filterValues['agent_names'];
                $agentsWithPaying = $filterValues['agents_with_paying'];

                // Convert properties to Property-like objects for view compatibility
                $properties = $filteredProperties->map(function ($propertyData) {
                    return new PropertyFromSheet($propertyData);
                });

                // Sort by ID (or you can add a created_at/updated_at field to sheets)
                $properties = $properties->sortByDesc('id')->values();

                // Paginate the results
                $perPage = 20;
                $currentPage = $request->get('page', 1);
                $items = $properties->slice(($currentPage - 1) * $perPage, $perPage)->values();
                
                $properties = new LengthAwarePaginator(
                    $items,
                    $properties->count(),
                    $perPage,
                    $currentPage,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                // Log filter results for debugging
                \Log::info('Property index filters applied (Google Sheets)', [
                    'filters' => $filters,
                    'total_results' => $properties->total(),
                    'current_page' => $properties->currentPage(),
                    'per_page' => $properties->perPage(),
                ]);

                return view('properties.index', compact('properties', 'locations', 'propertyTypes', 'availableDates', 'agentNames', 'agentsWithPaying'));
            } catch (\Exception $e) {
                \Log::error('Error loading properties from Google Sheets, falling back to database', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Fall through to database fallback
            }
        }
        
        // Fallback to database if Google Sheets not configured or fails
        $query = Property::query();

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

        // New filters: Management Company, Agent Name
        if ($request->filled('management_company')) {
            $query->byManagementCompany($request->management_company);
        }

        // Agent name filtering - only for authenticated users
        if ($request->filled('agent_name') && auth()->check()) {
            $query->where('agent_name', 'like', "%{$request->agent_name}%");
        }

        if ($request->filled('couples_allowed')) {
            $query->byCouplesAllowed($request->couples_allowed);
        }

        // Get unique values for filter dropdowns
        $locations = Property::distinct()->pluck('location')->filter()->sort()->values();
        $propertyTypes = Property::distinct()->pluck('property_type')->filter()->sort()->values();
        $availableDates = Property::distinct()->pluck('available_date')->filter()->sort()->values();
        
        // Get agent names with paying status - only for authenticated users
        if (auth()->check()) {
            $agentNames = Property::distinct()->pluck('agent_name')->filter()->sort()->values();
            // Get agents with paying status
            $agentsWithPaying = Property::whereNotNull('agent_name')
                ->where('agent_name', '!=', '')
                ->select('agent_name', 'paying')
                ->get()
                ->groupBy('agent_name')
                ->map(function ($properties) {
                    // Check if any property for this agent has paying = 'yes'
                    return $properties->contains(function ($property) {
                        return strtolower($property->paying ?? '') === 'yes';
                    });
                });
        } else {
            $agentNames = collect(); // Empty collection for non-authenticated users
            $agentsWithPaying = collect();
        }

        $properties = $query->latest()->paginate(20);

        // Log filter results for debugging
        \Log::info('Property index filters applied (Database)', [
            'filters' => $request->all(),
            'total_results' => $properties->total(),
            'current_page' => $properties->currentPage(),
            'per_page' => $properties->perPage(),
        ]);

        return view('properties.index', compact('properties', 'locations', 'propertyTypes', 'availableDates', 'agentNames', 'agentsWithPaying'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Try to get from Google Sheets first
        $propertyData = $this->sheetsService->getPropertyById($id);
        
        if ($propertyData) {
            $property = new PropertyFromSheet($propertyData);
            
            // Load interests from database if they exist (hybrid approach)
            // Note: This assumes property interests are still stored in DB
            $propertyId = $property->id;
            $interests = \App\Models\PropertyInterest::where('property_id', $propertyId)
                ->with('client')
                ->get();
            
            $property->setRelation('interests', $interests);
            
            $interestedClients = $interests->map(function ($interest) {
                return $interest->client;
            })->filter();
            
            $property->setRelation('interestedClients', $interestedClients);
        } else {
            // Fallback to database if not found in sheets
            $property = Property::with(['interests.client', 'interestedClients'])->findOrFail($id);
        }
        
        $clients = collect();
        if (auth()->check()) {
            $clients = Client::orderBy('full_name', 'asc')->get();
        }
        
        return view('properties.show', compact('property', 'clients'));
    }

    /**
     * Display properties on an interactive map.
     */
    public function map(Request $request)
    {
        // Build filters array
        $filters = [];
        
        if ($request->filled('location')) {
            $filters['location'] = $request->location;
        }

        if ($request->filled('min_price')) {
            $filters['min_price'] = $request->min_price;
        }
        
        if ($request->filled('max_price')) {
            $filters['max_price'] = $request->max_price;
        }

        if ($request->filled('property_type')) {
            $filters['property_type'] = $request->property_type;
        }

        if ($request->filled('available_date')) {
            $filters['available_date'] = $request->available_date;
        }

        if ($request->filled('management_company')) {
            $filters['management_company'] = $request->management_company;
        }

        if ($request->filled('agent_name') && auth()->check()) {
            $filters['agent_name'] = $request->agent_name;
        }

        if ($request->filled('couples_allowed')) {
            $filters['couples_allowed'] = $request->couples_allowed;
        }

        // Get filtered properties from Google Sheets
        $filteredProperties = $this->sheetsService->filterProperties($filters);
        
        // Filter properties with valid coordinates
        $properties = $filteredProperties->filter(function ($property) {
            $lat = $property['latitude'] ?? null;
            $lng = $property['longitude'] ?? null;
            
            if (empty($lat) || empty($lng)) {
                return false;
            }
            
            $latFloat = (float) $lat;
            $lngFloat = (float) $lng;
            
            return (-90 <= $latFloat && $latFloat <= 90) && (-180 <= $lngFloat && $lngFloat <= 180);
        });

        // Convert to Property-like objects
        $properties = $properties->map(function ($propertyData) {
            return new PropertyFromSheet($propertyData);
        })->take(400);

        // Get filter values for dropdowns
        $filterValues = $this->sheetsService->getFilterValues();
        $locations = $filterValues['locations'];
        $propertyTypes = $filterValues['propertyTypes'];
        $availableDates = $filterValues['available_dates'];
        $agentNames = $filterValues['agent_names'];
        $agentsWithPaying = $filterValues['agents_with_paying'];

        // Log validation results for debugging
        \Log::info('Map query results (Google Sheets)', [
            'total_properties' => $properties->count(),
            'filters_applied' => $filters,
            'with_coords' => $properties->filter(function($p) {
                return !empty($p->latitude) && !empty($p->longitude);
            })->count(),
        ]);

        return view('properties.map', compact('properties', 'locations', 'propertyTypes', 'availableDates', 'agentNames', 'agentsWithPaying'));
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
        
        $propertyData = $request->all();
        $propertyData['updatable'] = false; // CRM-created properties should not be updated by imports
        
        $property = Property::create($propertyData);
        
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
