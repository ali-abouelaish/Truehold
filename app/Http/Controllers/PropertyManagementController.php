<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class PropertyManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::query();
        
        // Search functionality with error handling for NA/undefined values
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search) && $search !== 'N/A' && $search !== 'undefined' && $search !== 'null') {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%")
                      ->orWhere('management_company', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
        }
        
        // Filter by status with validation
        if ($request->filled('status')) {
            $status = $request->status;
            if (in_array($status, ['available', 'rented', 'unavailable', 'on_hold'])) {
                $query->where('status', $status);
            }
        }
        
        // Filter by price range with error handling for invalid prices
        if ($request->filled('price_range')) {
            $priceRange = $request->price_range;
            // Use a simpler approach that's more compatible with SQLite
            switch ($priceRange) {
                case '0-500':
                    $query->where(function($q) {
                        $q->whereNotNull('price')
                          ->where('price', '!=', 'N/A')
                          ->where('price', '!=', 'undefined')
                          ->where('price', '!=', 'null')
                          ->where('price', '!=', '')
                          ->whereRaw('CAST(price AS REAL) > 0')
                          ->whereRaw('CAST(price AS REAL) <= 500');
                    });
                    break;
                case '500-1000':
                    $query->where(function($q) {
                        $q->whereNotNull('price')
                          ->where('price', '!=', 'N/A')
                          ->where('price', '!=', 'undefined')
                          ->where('price', '!=', 'null')
                          ->where('price', '!=', '')
                          ->whereRaw('CAST(price AS REAL) > 500')
                          ->whereRaw('CAST(price AS REAL) <= 1000');
                    });
                    break;
                case '1000-1500':
                    $query->where(function($q) {
                        $q->whereNotNull('price')
                          ->where('price', '!=', 'N/A')
                          ->where('price', '!=', 'undefined')
                          ->where('price', '!=', 'null')
                          ->where('price', '!=', '')
                          ->whereRaw('CAST(price AS REAL) > 1000')
                          ->whereRaw('CAST(price AS REAL) <= 1500');
                    });
                    break;
                case '1500+':
                    $query->where(function($q) {
                        $q->whereNotNull('price')
                          ->where('price', '!=', 'N/A')
                          ->where('price', '!=', 'undefined')
                          ->where('price', '!=', 'null')
                          ->where('price', '!=', '')
                          ->whereRaw('CAST(price AS REAL) > 1500');
                    });
                    break;
            }
        }
        
        // Filter by management company with error handling
        if ($request->filled('company')) {
            $company = $request->company;
            if (!empty($company) && $company !== 'N/A' && $company !== 'undefined' && $company !== 'null') {
                $query->where('management_company', $company);
            }
        }
        
        // Sorting with error handling
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_low':
                $query->orderByRaw('CASE 
                    WHEN price IS NULL OR price = "N/A" OR price = "undefined" OR price = "null" OR price = 0 
                    THEN 999999 
                    ELSE CAST(price AS DECIMAL(10,2)) 
                END ASC');
                break;
            case 'price_high':
                $query->orderByRaw('CASE 
                    WHEN price IS NULL OR price = "N/A" OR price = "undefined" OR price = "null" OR price = 0 
                    THEN 0 
                    ELSE CAST(price AS DECIMAL(10,2)) 
                END DESC');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default: // latest
                $query->latest();
                break;
        }
        
        // Handle export request
        if ($request->has('export')) {
            $exportProperties = $query->get();
            return $this->exportProperties($exportProperties);
        }
        
        try {
            $properties = $query->paginate(20)->withQueryString();
            
            // Clean up properties to ensure all values are strings
            $properties->getCollection()->transform(function ($property) {
                // Ensure critical fields are strings with more robust handling
                $property->title = $this->ensureString($property->title, 'N/A');
                $property->location = $this->ensureString($property->location, 'N/A');
                $property->property_type = $this->ensureString($property->property_type, 'N/A');
                $property->status = $this->ensureString($property->status, 'available');
                $property->management_company = $this->ensureString($property->management_company, 'N/A');
                $property->price = $this->ensureString($property->price, 'N/A');
                
                return $property;
            });
        } catch (\Exception $e) {
            \Log::error('Property query error: ' . $e->getMessage());
            // Return empty results if there's an error
            $properties = collect([])->paginate(20);
        }
        
        // Get unique companies for filter dropdown with error handling
        $companies = Property::whereNotNull('management_company')
                           ->where('management_company', '!=', 'N/A')
                           ->where('management_company', '!=', 'undefined')
                           ->where('management_company', '!=', 'null')
                           ->where('management_company', '!=', '')
                           ->distinct()
                           ->pluck('management_company')
                           ->sort()
                           ->values();
        
        // Get property statistics with error handling
        $totalProperties = Property::count();
        $availableCount = Property::where('status', 'available')->count();
        $rentedCount = Property::where('status', 'rented')->count();
        $unavailableCount = Property::where('status', 'unavailable')->count();
        
        return view('properties.manage', compact('properties', 'companies', 'totalProperties', 'availableCount', 'rentedCount', 'unavailableCount'));
    }
    
    /**
     * Ensure a value is a string, handling arrays, nulls, and other types
     */
    private function ensureString($value, $default = 'N/A')
    {
        if (is_array($value)) {
            return implode(', ', array_filter($value, 'is_string'));
        }
        
        if (is_null($value) || $value === 'N/A' || $value === 'undefined' || $value === 'null') {
            return $default;
        }
        
        if (is_object($value)) {
            return method_exists($value, '__toString') ? (string) $value : $default;
        }
        
        return (string) $value;
    }
    
    /**
     * Check if a price value is valid for filtering
     */
    private function isValidPrice($price)
    {
        if (empty($price) || is_array($price) || is_object($price)) {
            return false;
        }
        
        if (is_string($price)) {
            $price = trim($price);
            if (in_array($price, ['N/A', 'undefined', 'null', ''])) {
                return false;
            }
            return is_numeric($price) && $price > 0;
        }
        
        return is_numeric($price) && $price > 0;
    }
    
    public function updateStatus(Request $request, Property $property)
    {
        $request->validate([
            'status' => 'required|in:available,rented,unavailable,on_hold'
        ]);
        
        // Validate the property exists and has a valid status
        if (!$property) {
            return back()->with('error', 'Property not found.');
        }
        
        $status = $request->status;
        if (!in_array($status, ['available', 'rented', 'unavailable', 'on_hold'])) {
            return back()->with('error', 'Invalid status provided.');
        }
        
        $property->update(['status' => $status]);
        
        return back()->with('success', "Property status updated to " . ucfirst($status));
    }
    
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'property_ids' => 'required|array',
            'property_ids.*' => 'exists:properties,id',
            'status' => 'required|in:available,rented,unavailable,on_hold'
        ]);
        
        // Additional validation
        $propertyIds = $request->property_ids;
        $status = $request->status;
        
        if (empty($propertyIds)) {
            return back()->with('error', 'No properties selected for update.');
        }
        
        if (!in_array($status, ['available', 'rented', 'unavailable', 'on_hold'])) {
            return back()->with('error', 'Invalid status provided.');
        }
        
        // Filter out any invalid IDs and update only valid properties
        $validIds = Property::whereIn('id', $propertyIds)->pluck('id')->toArray();
        
        if (empty($validIds)) {
            return back()->with('error', 'No valid properties found for update.');
        }
        
        Property::whereIn('id', $validIds)->update(['status' => $status]);
        
        return back()->with('success', count($validIds) . " properties updated to " . ucfirst($status));
    }
    
    private function exportProperties($properties)
    {
        $filename = 'properties_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($properties) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Title', 'Location', 'Price', 'Status', 'Property Type', 
                'Available Date', 'Management Company', 'Contact Info'
            ]);
            
            // CSV data with error handling for NA/undefined values
            foreach ($properties as $property) {
                fputcsv($file, [
                    $property->id ?? 'N/A',
                    $this->cleanValue($property->title),
                    $this->cleanValue($property->location),
                    $this->cleanPrice($property->price),
                    $this->cleanStatus($property->status),
                    $this->cleanValue($property->property_type),
                    $this->cleanValue($property->available_date),
                    $this->cleanValue($property->management_company),
                    $this->cleanValue($property->contact_info)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Clean and format property values for export
     */
    private function cleanValue($value)
    {
        if (empty($value) || $value === 'N/A' || $value === 'undefined' || $value === 'null') {
            return 'N/A';
        }
        return $value;
    }
    
    /**
     * Clean and format price values for export
     */
    private function cleanPrice($price)
    {
        if (empty($price) || $price === 'N/A' || $price === 'undefined' || $price === 'null' || $price == 0) {
            return 'N/A';
        }
        return is_numeric($price) ? '£' . number_format($price, 2) : $price;
    }
    
    /**
     * Clean and format status values for export
     */
    private function cleanStatus($status)
    {
        if (empty($status) || $status === 'N/A' || $status === 'undefined' || $status === 'null') {
            return 'Available';
        }
        return ucfirst($status);
    }
}
