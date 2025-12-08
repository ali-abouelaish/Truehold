<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PropertyGoogleSheetsService;
use Illuminate\Support\Facades\Log;

class PropertyDebugController extends Controller
{
    public function testSheets(PropertyGoogleSheetsService $service)
    {
        $spreadsheetId = config('services.google.properties.spreadsheet_id');
        $sheetName = config('services.google.properties.sheet_name', 'Properties');
        
        $debugInfo = [
            'configured' => !empty($spreadsheetId),
            'spreadsheet_id' => $spreadsheetId,
            'sheet_name' => $sheetName,
        ];
        
        try {
            $service->clearCache();
            $properties = $service->getAllProperties();
            $filterValues = $service->getFilterValues();
            
            $debugInfo['properties_count'] = $properties->count();
            $debugInfo['sample_properties'] = $properties->take(3)->toArray();
            $debugInfo['filter_values'] = [
                'locations_count' => $filterValues['locations']->count(),
                'property_types_count' => $filterValues['propertyTypes']->count(),
            ];
        } catch (\Exception $e) {
            $debugInfo['error'] = $e->getMessage();
        }
        
        return response()->json($debugInfo, JSON_PRETTY_PRINT);
    }
}
