<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyFromSheet;
use App\Models\Client;
use App\Services\PropertyGoogleSheetsService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

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
        
        // Force clear cache if requested (for debugging)
        if ($request->has('clear_cache')) {
            $this->sheetsService->clearCache();
            \Log::info('Properties cache manually cleared');
        }
        
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
            'data_source' => 'google_sheets',
            'spreadsheet_id' => config('services.google.properties.spreadsheet_id'),
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
        \Log::info('Property index filters applied (Database Fallback)', [
            'filters' => $request->all(),
            'total_results' => $properties->total(),
            'current_page' => $properties->currentPage(),
            'per_page' => $properties->perPage(),
            'data_source' => 'database',
            'google_sheets_configured' => !empty(config('services.google.properties.spreadsheet_id')),
        ]);

        return view('properties.index', compact('properties', 'locations', 'propertyTypes', 'availableDates', 'agentNames', 'agentsWithPaying'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Check if Google Sheets is configured
        $useGoogleSheets = !empty(config('services.google.properties.spreadsheet_id'));
        
        if ($useGoogleSheets) {
            try {
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
                    
                    $clients = collect();
                    if (auth()->check()) {
                        $clients = Client::orderBy('full_name', 'asc')->get();
                    }
                    
                    return view('properties.show', compact('property', 'clients'));
                }
            } catch (\Exception $e) {
                \Log::error('Error loading property from Google Sheets, falling back to database', [
                    'id' => $id,
                    'error' => $e->getMessage()
                ]);
                // Fall through to database fallback
            }
        }
        
        // Fallback to database if not found in sheets or Google Sheets not configured
        $property = Property::with(['interests.client', 'interestedClients'])->findOrFail($id);
        
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
                
                // Filter properties with valid coordinates
                $properties = $filteredProperties->filter(function ($property) {
                    $lat = $property['latitude'] ?? null;
                    $lng = $property['longitude'] ?? null;
                    
                    // Handle string coordinates
                    if (is_string($lat)) {
                        $lat = str_replace(',', '.', trim($lat));
                    }
                    if (is_string($lng)) {
                        $lng = str_replace(',', '.', trim($lng));
                    }
                    
                    if (empty($lat) || empty($lng) || $lat === 'N/A' || $lng === 'N/A') {
                        return false;
                    }
                    
                    $latFloat = (float) $lat;
                    $lngFloat = (float) $lng;
                    
                    // Check if valid numeric values
                    if (!is_numeric($lat) || !is_numeric($lng)) {
                        return false;
                    }
                    
                    return (-90 <= $latFloat && $latFloat <= 90) && (-180 <= $lngFloat && $lngFloat <= 180);
                });
                
                \Log::info('Properties filtered by coordinates', [
                    'before_filter' => $filteredProperties->count(),
                    'after_filter' => $properties->count(),
                    'sample_properties' => $properties->take(3)->map(function($p) {
                        return [
                            'id' => $p['id'] ?? 'NO ID',
                            'title' => $p['title'] ?? 'NO TITLE',
                            'lat' => $p['latitude'] ?? null,
                            'lng' => $p['longitude'] ?? null,
                            'lat_type' => gettype($p['latitude'] ?? null),
                            'lng_type' => gettype($p['longitude'] ?? null),
                        ];
                    })
                ]);

                // Convert to Property-like objects for view compatibility
                $propertyObjects = $properties->map(function ($propertyData) {
                    return new PropertyFromSheet($propertyData);
                })->take(400);

                // For JSON encoding in the view, convert back to arrays with proper coordinate types
                $propertiesForJson = $properties->map(function ($propertyData) {
                    // Ensure coordinates are properly formatted as numbers
                    $propertyData['latitude'] = isset($propertyData['latitude']) ? (float) $propertyData['latitude'] : null;
                    $propertyData['longitude'] = isset($propertyData['longitude']) ? (float) $propertyData['longitude'] : null;
                    return $propertyData;
                })->take(400)->values()->all();

                // Get filter values for dropdowns
                $filterValues = $this->sheetsService->getFilterValues();
                $locations = $filterValues['locations'];
                $propertyTypes = $filterValues['propertyTypes'];
                $availableDates = $filterValues['available_dates'];
                $agentNames = $filterValues['agent_names'];
                $agentsWithPaying = $filterValues['agents_with_paying'];

                // Log validation results for debugging
                \Log::info('Map query results (Google Sheets)', [
                    'total_properties' => $propertyObjects->count(),
                    'properties_with_coords' => $propertiesForJson->filter(function($p) {
                        return !empty($p['latitude']) && !empty($p['longitude']);
                    })->count(),
                    'filters_applied' => $filters,
                    'sample_coords' => $propertiesForJson->take(3)->map(function($p) {
                        return [
                            'id' => $p['id'] ?? 'NO ID',
                            'lat' => $p['latitude'] ?? null,
                            'lng' => $p['longitude'] ?? null
                        ];
                    })
                ]);

                return view('properties.map', [
                    'properties' => $propertyObjects,
                    'propertiesForJson' => $propertiesForJson,
                    'locations' => $locations,
                    'propertyTypes' => $propertyTypes,
                    'availableDates' => $availableDates,
                    'agentNames' => $agentNames,
                    'agentsWithPaying' => $agentsWithPaying
                ]);
            } catch (\Exception $e) {
                \Log::error('Error loading properties from Google Sheets for map, falling back to database', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Fall through to database fallback
            }
        }
        
        // Fallback to database if Google Sheets not configured or fails
        $query = Property::query()->withValidCoordinates();

        // Apply filters
        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }

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

        if ($request->filled('management_company')) {
            $query->byManagementCompany($request->management_company);
        }

        if ($request->filled('agent_name') && auth()->check()) {
            $query->where('agent_name', 'like', "%{$request->agent_name}%");
        }

        if ($request->filled('couples_allowed')) {
            $query->byCouplesAllowed($request->couples_allowed);
        }

        // Get properties with all necessary fields for map display
        $properties = $query->limit(400)->get();

        // Get filter values for dropdowns
        $locations = Property::distinct()->pluck('location')->filter()->sort()->values();
        $propertyTypes = Property::distinct()->pluck('property_type')->sort()->values();
        $availableDates = Property::distinct()->pluck('available_date')->sort()->values();
        
        if (auth()->check()) {
            $agentNames = Property::distinct()->pluck('agent_name')->filter()->sort()->values();
            $agentsWithPaying = Property::whereNotNull('agent_name')
                ->where('agent_name', '!=', '')
                ->select('agent_name', 'paying')
                ->get()
                ->groupBy('agent_name')
                ->map(function ($properties) {
                    return $properties->contains(function ($property) {
                        return strtolower($property->paying ?? '') === 'yes';
                    });
                });
        } else {
            $agentNames = collect();
            $agentsWithPaying = collect();
        }

        // Convert to array for JSON encoding
        $propertiesForJson = $properties->map(function ($property) {
            return [
                'id' => $property->id,
                'title' => $property->title,
                'location' => $property->location,
                'latitude' => $property->latitude ? (float) $property->latitude : null,
                'longitude' => $property->longitude ? (float) $property->longitude : null,
                'price' => $property->price,
                'property_type' => $property->property_type,
                'agent_name' => $property->agent_name,
                'management_company' => $property->management_company,
                'couples_ok' => $property->couples_ok,
                'first_photo_url' => $property->first_photo_url,
                'high_quality_photos_array' => $property->high_quality_photos_array,
            ];
        })->values()->all();

        \Log::info('Map query results (Database Fallback)', [
            'total_properties' => $properties->count(),
            'with_coords' => $properties->filter(function($p) {
                return !empty($p->latitude) && !empty($p->longitude);
            })->count(),
        ]);

        return view('properties.map', [
            'properties' => $properties,
            'propertiesForJson' => $propertiesForJson,
            'locations' => $locations,
            'propertyTypes' => $propertyTypes,
            'availableDates' => $availableDates,
            'agentNames' => $agentNames,
            'agentsWithPaying' => $agentsWithPaying
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * This is the public/create form for properties which will
     * create a local Property record and also append to Google Sheets
     * (if Google Sheets is configured).
     */
    public function create()
    {
        return view('properties.create');
    }

    /**
     * Store a newly created resource in storage and append to Google Sheets.
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
        
        // Create local Property record (existing behaviour)
        $propertyData = $request->all();
        $propertyData['updatable'] = false; // CRM-created properties should not be updated by imports
        $property = Property::create($propertyData);

        // Also append to Google Sheets "Properties" worksheet if configured
        try {
            $this->sheetsService->appendProperty($property->toArray());
        } catch (\Exception $e) {
            \Log::error('Failed to append property to Google Sheets from PropertyController@store', [
                'property_id' => $property->id ?? null,
                'error' => $e->getMessage(),
            ]);
            // Do not block the user flow if Sheets fails
        }
        
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
