<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Sheets as GoogleSheets;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PropertyGoogleSheetsService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;
    protected $sheetName;
    protected $cacheTimeout;

    public function __construct()
    {
        $this->spreadsheetId = config('services.google.properties.spreadsheet_id');
        $this->sheetName = config('services.google.properties.sheet_name', 'Properties');
        $this->cacheTimeout = config('services.google.properties.cache_timeout', 300); // 5 minutes default
        
        // Initialize client only if spreadsheet ID is configured
        if ($this->spreadsheetId) {
            try {
                $this->initializeClient();
            } catch (\Exception $e) {
                Log::warning('Google Sheets client initialization failed for properties', [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    protected function initializeClient()
    {
        try {
            $this->client = new GoogleClient();
            $this->client->setApplicationName('Property Scraper App');
            // Allow both read and write operations
            $this->client->setScopes([
                GoogleSheets::SPREADSHEETS_READONLY,
                GoogleSheets::SPREADSHEETS
            ]);
            $this->client->setAccessType('offline');

            // Use service account credentials if available
            $credentialsPath = config('services.google.properties.credentials_path') 
                ?? config('services.google.sheets.credentials_path');
            
            $credentialsLoaded = false;
            
            if ($credentialsPath) {
                // Handle both relative and absolute paths
                $pathsToTry = [];
                
                // If it's already an absolute path
                if (str_starts_with($credentialsPath, '/')) {
                    $pathsToTry[] = $credentialsPath;
                } else {
                    // Try as-is first (in case it's already a full path from storage_path)
                    $pathsToTry[] = $credentialsPath;
                    
                    // Remove 'storage/app/' prefix if present, then add it properly
                    $cleanPath = preg_replace('#^storage/app/#', '', $credentialsPath);
                    $cleanPath = preg_replace('#^storage\\\\app\\\\#', '', $cleanPath); // Windows
                    
                    // Try storage/app path
                    $pathsToTry[] = storage_path('app/' . ltrim($cleanPath, '/'));
                    
                    // Try base path
                    $pathsToTry[] = base_path($credentialsPath);
                    
                    // Try base path with storage/app
                    $pathsToTry[] = base_path('storage/app/' . ltrim($cleanPath, '/'));
                }
                
                $foundPath = null;
                foreach ($pathsToTry as $path) {
                    if (file_exists($path) && is_readable($path)) {
                        $foundPath = $path;
                        break;
                    }
                }
                
                if ($foundPath) {
                    Log::info('Loading Google Sheets credentials from file', [
                        'original_path' => $credentialsPath,
                        'resolved_path' => $foundPath,
                        'readable' => is_readable($foundPath),
                        'file_size' => filesize($foundPath)
                    ]);
                    $this->client->setAuthConfig($foundPath);
                    $credentialsLoaded = true;
                } else {
                    Log::error('Google Sheets credentials file not found', [
                        'original_path' => $credentialsPath,
                        'paths_tried' => $pathsToTry,
                        'storage_path' => storage_path('app'),
                        'base_path' => base_path()
                    ]);
                }
            } else {
                Log::warning('No credentials path configured', [
                    'properties_credentials_path' => config('services.google.properties.credentials_path'),
                    'sheets_credentials_path' => config('services.google.sheets.credentials_path')
                ]);
            }
            
            if (!$credentialsLoaded) {
                // Fallback to JSON credentials from config
                $credentialsJson = config('services.google.properties.credentials_json')
                    ?? config('services.google.sheets.credentials_json');
                
                if ($credentialsJson) {
                    $credentials = json_decode($credentialsJson, true);
                    if ($credentials && isset($credentials['type']) && $credentials['type'] === 'service_account') {
                        Log::info('Loading Google Sheets credentials from config JSON');
                        $this->client->setAuthConfig($credentials);
                        $credentialsLoaded = true;
                    } else {
                        Log::warning('Google Sheets credentials JSON is invalid or not a service account');
                    }
                }
            }
            
            if (!$credentialsLoaded) {
                throw new \Exception('No valid Google Sheets credentials found. Please configure GOOGLE_SHEETS_CREDENTIALS_PATH or GOOGLE_SHEETS_CREDENTIALS_JSON');
            }

            $this->service = new GoogleSheets($this->client);
            
            Log::info('Google Sheets client initialized successfully for properties');
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google Sheets client for properties', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get all properties from Google Sheets
     * 
     * @return Collection
     */
    public function getAllProperties(): Collection
    {
        $cacheKey = 'properties_google_sheets_all';
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () {
            return $this->fetchPropertiesFromSheets();
        });
    }

    /**
     * Fetch properties directly from Google Sheets
     * 
     * @return Collection
     */
    protected function fetchPropertiesFromSheets(): Collection
    {
        if (!$this->spreadsheetId) {
            Log::warning('Properties Google Sheets spreadsheet ID not configured');
            return collect([]);
        }

        if (!$this->service) {
            Log::warning('Google Sheets service not initialized for properties');
            return collect([]);
        }

        try {
            // Try different range formats if the sheet name doesn't work
            $rangesToTry = [
                $this->sheetName . '!A:ZZ',  // Full sheet with name
                'A:ZZ',  // Full sheet without name (default sheet)
                $this->sheetName . '!A1:Z1000',  // Limited range with name
            ];
            
            $values = null;
            $lastError = null;
            
            foreach ($rangesToTry as $range) {
                try {
                    Log::info('Attempting to read from Google Sheet', [
                        'spreadsheet_id' => $this->spreadsheetId,
                        'sheet_name' => $this->sheetName,
                        'range' => $range
                    ]);
                    
                    $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
                    $values = $response->getValues();
                    
                    if (!empty($values)) {
                        Log::info('Successfully read data from range', ['range' => $range]);
                        break;
                    }
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();
                    Log::warning('Failed to read from range, trying next', [
                        'range' => $range,
                        'error' => $lastError
                    ]);
                    continue;
                }
            }
            
            if (empty($values) && $lastError) {
                throw new \Exception('Failed to read from any range: ' . $lastError);
            }

            Log::info('Google Sheets response received', [
                'values_count' => $values ? count($values) : 0,
                'has_values' => !empty($values)
            ]);

            if (empty($values)) {
                Log::warning('No data returned from Google Sheet', [
                    'spreadsheet_id' => $this->spreadsheetId,
                    'sheet_name' => $this->sheetName,
                    'values_count' => 0
                ]);
                return collect([]);
            }
            
            if (count($values) < 2) {
                Log::warning('Google Sheet has headers but no data rows', [
                    'spreadsheet_id' => $this->spreadsheetId,
                    'sheet_name' => $this->sheetName,
                    'values_count' => count($values),
                    'has_headers' => count($values) >= 1,
                    'headers' => count($values) >= 1 ? $values[0] : null
                ]);
                return collect([]);
            }

            // First row is headers - normalize them
            $rawHeaders = $values[0];
            $headers = array_map(function($header) {
                return strtolower(trim($header));
            }, $rawHeaders);
            
            Log::info('Headers extracted from Google Sheet', [
                'raw_headers' => array_slice($rawHeaders, 0, 10),
                'normalized_headers' => array_slice($headers, 0, 10),
                'header_count' => count($headers),
                'all_headers' => $headers
            ]);
            
            // Map headers to property fields (case-insensitive matching)
            $headerMap = $this->getHeaderMap();
            
            // Process data rows
            $properties = collect();
            $skippedRows = 0;
            $processedRows = 0;
            
            Log::info('Processing Google Sheets data rows', [
                'total_rows' => count($values),
                'data_rows' => count($values) - 1
            ]);
            
            for ($i = 1; $i < count($values); $i++) {
                $row = $values[$i];
                $processedRows++;
                
                // Skip empty rows (check if row has any non-empty values)
                $rowHasData = false;
                foreach ($row as $cell) {
                    $trimmed = is_string($cell) ? trim($cell) : (string)$cell;
                    if (!empty($trimmed)) {
                        $rowHasData = true;
                        break;
                    }
                }
                
                if (!$rowHasData) {
                    $skippedRows++;
                    if ($skippedRows <= 3) {
                        Log::debug('Skipping empty row', [
                            'row_index' => $i,
                            'row_data' => $row,
                            'row_length' => count($row)
                        ]);
                    }
                    continue;
                }
                
                // Ensure row has same length as headers (pad with empty strings if needed)
                while (count($row) < count($headers)) {
                    $row[] = '';
                }
                
                $property = $this->mapRowToProperty($row, $headers, $headerMap);
                
                // Accept property if it has an ID (which is always generated)
                // Don't require title or location to be present
                if ($property && !empty($property['id'])) {
                    $properties->push($property);
                } else {
                    $skippedRows++;
                    // Log first few skipped rows for debugging
                    if ($skippedRows <= 5) {
                        Log::warning('Skipped property row - no ID generated', [
                            'row_index' => $i,
                            'row_preview' => array_slice($row, 0, 10),
                            'row_count' => count($row),
                            'header_count' => count($headers),
                            'mapped_property' => $property ? array_keys($property) : null,
                            'has_id' => $property && !empty($property['id']),
                            'property_data' => $property
                        ]);
                    }
                }
            }

            Log::info('Properties fetched from Google Sheets', [
                'count' => $properties->count(),
                'processed_rows' => $processedRows,
                'skipped_rows' => $skippedRows,
                'spreadsheet_id' => $this->spreadsheetId,
                'sheet_name' => $this->sheetName,
                'sample_property_ids' => $properties->take(5)->pluck('id')->toArray(),
                'total_rows_in_sheet' => count($values),
                'headers_found' => count($headers),
                'sample_headers' => array_slice($headers, 0, 10)
            ]);
            
            // If no properties found, log more details for debugging
            if ($properties->count() === 0) {
                Log::error('No properties extracted from Google Sheet', [
                    'total_rows' => count($values),
                    'data_rows' => count($values) - 1,
                    'headers' => $headers,
                    'first_data_row' => isset($values[1]) ? $values[1] : null,
                    'first_data_row_count' => isset($values[1]) ? count($values[1]) : 0,
                    'header_count' => count($headers)
                ]);
            }

            return $properties;
        } catch (\Google\Service\Exception $e) {
            Log::error('Google Sheets API error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'errors' => $e->getErrors() ?? []
            ]);
            return collect([]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch properties from Google Sheet', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }

    /**
     * Map Google Sheets row to property array
     * 
     * @param array $row
     * @param array $headers
     * @param array $headerMap
     * @return array|null
     */
    protected function mapRowToProperty(array $row, array $headers, array $headerMap): ?array
    {
        $property = [];
        
        // Log first row for debugging
        static $firstRowLogged = false;
        if (!$firstRowLogged && !empty($row)) {
            Log::info('Mapping first data row', [
                'row_length' => count($row),
                'header_count' => count($headers),
                'row_preview' => array_slice($row, 0, 10),
                'headers_preview' => array_slice($headers, 0, 10)
            ]);
            $firstRowLogged = true;
        }
        
        foreach ($headerMap as $sheetHeader => $propertyField) {
            // Find the header index (headers are already normalized to lowercase)
            $headerIndex = array_search(strtolower(trim($sheetHeader)), $headers);
            
            if ($headerIndex !== false && isset($row[$headerIndex])) {
                $rawValue = $row[$headerIndex];
                // Only process non-empty values
                if ($rawValue === '' || $rawValue === null) {
                    continue;
                }
                $value = is_string($rawValue) ? trim($rawValue) : $rawValue;
                
                // Handle special field types
                if ($propertyField === 'id') {
                    // Use row number as ID if no ID column exists
                    $property[$propertyField] = $value ?: null;
                } elseif ($propertyField === 'latitude' || $propertyField === 'longitude') {
                    // Handle coordinates - can be string or number
                    if (empty($value) || $value === '' || $value === 'N/A') {
                        $property[$propertyField] = null;
                    } else {
                        // Try to convert to float, handle comma as decimal separator
                        $cleanValue = str_replace(',', '.', trim($value));
                        $property[$propertyField] = is_numeric($cleanValue) ? (float) $cleanValue : null;
                    }
                } elseif ($propertyField === 'photo_count') {
                    $property[$propertyField] = is_numeric($value) ? (int) $value : 0;
                } elseif ($propertyField === 'photos') {
                    // Handle photos as JSON array or comma-separated
                    if (empty($value)) {
                        $property[$propertyField] = null;
                    } elseif (strpos($value, '[') === 0) {
                        // JSON array
                        $decoded = json_decode($value, true);
                        $property[$propertyField] = is_array($decoded) ? json_encode($decoded) : null;
                    } else {
                        // Comma-separated
                        $photos = array_filter(array_map('trim', explode(',', $value)));
                        $property[$propertyField] = !empty($photos) ? json_encode($photos) : null;
                    }
                } elseif ($propertyField === 'updatable') {
                    $property[$propertyField] = in_array(strtolower($value), ['true', '1', 'yes']);
                } else {
                    $property[$propertyField] = $value ?: null;
                }
            }
        }
        
        // Ensure ID exists - use url/link if available, otherwise generate
        if (empty($property['id'])) {
            // Try to use url/link first (most reliable unique identifier)
            if (!empty($property['link'])) {
                $property['id'] = 'sheet_' . md5($property['link']);
            } elseif (!empty($property['title']) && !empty($property['location'])) {
                // Use title + location as ID source
                $property['id'] = 'sheet_' . md5($property['title'] . '|' . $property['location']);
            } elseif (!empty($property['title'])) {
                $property['id'] = 'sheet_' . md5($property['title']);
            } else {
                // Last resort: use first non-empty cell
                $idSource = null;
                foreach ($row as $cell) {
                    $trimmed = is_string($cell) ? trim($cell) : (string)$cell;
                    if (!empty($trimmed)) {
                        $idSource = $trimmed;
                        break;
                    }
                }
                $property['id'] = $idSource ? 'sheet_' . md5($idSource) : 'sheet_row_' . md5(serialize($row));
            }
        }
        
        // Ensure link field is set from url if url was provided
        if (empty($property['link']) && !empty($property['url'])) {
            $property['link'] = $property['url'];
        }
        
        // Set default values for required fields
        $property['status'] = $property['status'] ?? 'available';
        $property['updatable'] = $property['updatable'] ?? false;
        
        // Log if property has no essential fields (title or location)
        if (empty($property['title']) && empty($property['location'])) {
            Log::warning('Property row missing essential fields', [
                'row_data' => array_slice($row, 0, 5), // Log first 5 columns
                'mapped_property' => array_intersect_key($property, ['id', 'title', 'location'])
            ]);
            // Still return the property, but it might not be useful
        }
        
        return $property;
    }

    /**
     * Get header mapping from Google Sheets columns to property fields
     * 
     * @return array
     */
    protected function getHeaderMap(): array
    {
        return [
            'id' => 'id',
            'link' => 'link',
            'url' => 'link',  // Map url to link field
            'title' => 'title',
            'agent_name' => 'agent_name',
            'location' => 'location',
            'latitude' => 'latitude',
            'lat' => 'latitude',
            'longitude' => 'longitude',
            'lng' => 'longitude',
            'lon' => 'longitude',
            'status' => 'status',
            'price' => 'price',
            'description' => 'description',
            'property_type' => 'property_type',
            'type' => 'property_type',
            'available_date' => 'available_date',
            'availability' => 'available_date',
            'min_term' => 'min_term',
            'minimum_term' => 'min_term',
            'max_term' => 'max_term',
            'maximum_term' => 'max_term',
            'deposit' => 'deposit',
            'bills_included' => 'bills_included',
            'furnishings' => 'furnishings',
            'parking' => 'parking',
            'garden' => 'garden',
            'garden_patio' => 'garden',
            'broadband' => 'broadband',
            'broadband_included' => 'broadband',
            'housemates' => 'housemates',
            'total_rooms' => 'total_rooms',
            'smoker' => 'smoker',
            'smoking_ok' => 'smoking_ok',
            'pets' => 'pets',
            'pets_ok' => 'pets_ok',
            'any_pets' => 'pets',
            'occupation' => 'occupation',
            'gender' => 'gender',
            'couples_ok' => 'couples_ok',
            'couples_allowed' => 'couples_allowed',
            'pref_occupation' => 'pref_occupation',
            'references' => 'references',
            'min_age' => 'min_age',
            'max_age' => 'max_age',
            'age' => 'min_age',
            'ages' => 'min_age',
            'photo_count' => 'photo_count',
            'first_photo_url' => 'first_photo_url',
            'all_photos' => 'all_photos',
            'photos' => 'photos',
            'contact_info' => 'contact_info',
            'management_company' => 'management_company',
            'agent_id' => 'agent_id',
            'paying' => 'paying',
            'amenities' => 'amenities',
            'balcony_roof_terrace' => 'balcony_roof_terrace',
            'disabled_access' => 'disabled_access',
            'living_room' => 'living_room',
            'updatable' => 'updatable',
        ];
    }

    /**
     * Clear the properties cache
     */
    public function clearCache(): void
    {
        $cacheKey = 'properties_google_sheets_all';
        Cache::forget($cacheKey);
        Log::info('Properties Google Sheets cache cleared', ['cache_key' => $cacheKey]);
    }

    /**
     * Get a single property by ID
     * 
     * @param string|int $id
     * @return array|null
     */
    public function getPropertyById($id): ?array
    {
        return $this->getAllProperties()->firstWhere('id', $id);
    }

    /**
     * Filter properties by criteria
     * 
     * @param array $filters
     * @return Collection
     */
    public function filterProperties(array $filters): Collection
    {
        $properties = $this->getAllProperties();
        
        // Apply filters
        if (isset($filters['location'])) {
            $properties = $properties->filter(function ($property) use ($filters) {
                return stripos($property['location'] ?? '', $filters['location']) !== false;
            });
        }
        
        if (isset($filters['property_type'])) {
            $properties = $properties->filter(function ($property) use ($filters) {
                return stripos($property['property_type'] ?? '', $filters['property_type']) !== false;
            });
        }
        
        if (isset($filters['available_date'])) {
            $properties = $properties->filter(function ($property) use ($filters) {
                return stripos($property['available_date'] ?? '', $filters['available_date']) !== false;
            });
        }
        
        if (isset($filters['management_company'])) {
            $properties = $properties->filter(function ($property) use ($filters) {
                return stripos($property['management_company'] ?? '', $filters['management_company']) !== false;
            });
        }
        
        if (isset($filters['agent_name']) && auth()->check()) {
            $properties = $properties->filter(function ($property) use ($filters) {
                return stripos($property['agent_name'] ?? '', $filters['agent_name']) !== false;
            });
        }
        
        if (isset($filters['couples_allowed'])) {
            $properties = $properties->filter(function ($property) use ($filters) {
                $couplesOk = strtolower($property['couples_ok'] ?? '');
                $couplesAllowed = strtolower($property['couples_allowed'] ?? '');
                
                if ($filters['couples_allowed'] === 'yes') {
                    return stripos($couplesOk, 'yes') !== false 
                        || stripos($couplesOk, 'couples') !== false
                        || stripos($couplesAllowed, 'yes') !== false
                        || stripos($couplesAllowed, 'couples') !== false;
                } else {
                    return stripos($couplesOk, 'no') !== false 
                        || stripos($couplesAllowed, 'no') !== false;
                }
            });
        }
        
        // Price filtering
        if (isset($filters['min_price'])) {
            $properties = $properties->filter(function ($property) use ($filters) {
                $price = $this->extractNumericPrice($property['price'] ?? '');
                return $price >= (float) $filters['min_price'];
            });
        }
        
        if (isset($filters['max_price'])) {
            $properties = $properties->filter(function ($property) use ($filters) {
                $price = $this->extractNumericPrice($property['price'] ?? '');
                return $price <= (float) $filters['max_price'];
            });
        }
        
        return $properties;
    }

    /**
     * Extract numeric price from price string
     * 
     * @param string $priceString
     * @return float
     */
    protected function extractNumericPrice(string $priceString): float
    {
        // Remove currency symbols and text
        $cleaned = preg_replace('/[£,\s]/', '', $priceString);
        $cleaned = preg_replace('/[^0-9.]/', '', $cleaned);
        
        return (float) $cleaned;
    }

    /**
     * Get unique values for filter dropdowns
     * 
     * @return array
     */
    public function getFilterValues(): array
    {
        try {
            $properties = $this->getAllProperties();
            
            return [
                'locations' => $properties->pluck('location')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values(),
                'propertyTypes' => $properties->pluck('property_type')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values(),
                'available_dates' => $properties->pluck('available_date')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values(),
                'agent_names' => auth()->check() 
                    ? $properties->pluck('agent_name')
                        ->filter()
                        ->unique()
                        ->sort()
                        ->values()
                    : collect(),
                'agents_with_paying' => auth()->check()
                    ? $properties->where('paying', 'yes')
                        ->pluck('agent_name')
                        ->filter()
                        ->unique()
                        ->mapWithKeys(function ($agent) {
                            return [$agent => true];
                        })
                    : collect(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get filter values from Google Sheets', [
                'error' => $e->getMessage()
            ]);
            
            // Return empty collections on error
            return [
                'locations' => collect(),
                'propertyTypes' => collect(),
                'available_dates' => collect(),
                'agent_names' => collect(),
                'agents_with_paying' => collect(),
            ];
        }
    }

    /**
     * Append a property to Google Sheets
     * 
     * @param array $propertyData
     * @return bool
     */
    /**
     * Update a property in Google Sheets by its ID
     */
    public function updateProperty(string $id, array $updates): bool
    {
        if (!$this->spreadsheetId || !$this->service) {
            Log::warning('Google Sheets not configured for property update');
            return false;
        }

        try {
            // Get all properties to find the row number
            $range = $this->sheetName . '!A:ZZ';
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
            $rows = $response->getValues();
            
            if (empty($rows)) {
                return false;
            }
            
            // First row is headers
            $headers = array_map('strtolower', array_map('trim', $rows[0]));
            
            // Find the ID column index
            $idColumnIndex = array_search('id', $headers);
            if ($idColumnIndex === false) {
                return false;
            }
            
            // Find the row with matching ID (skip header row)
            $targetRowIndex = null;
            for ($i = 1; $i < count($rows); $i++) {
                if (isset($rows[$i][$idColumnIndex]) && $rows[$i][$idColumnIndex] == $id) {
                    $targetRowIndex = $i;
                    break;
                }
            }
            
            if ($targetRowIndex === null) {
                return false;
            }
            
            // Prepare updates for each column
            $updateRequests = [];
            foreach ($updates as $field => $value) {
                $normalizedField = strtolower(trim($field));
                $columnIndex = array_search($normalizedField, $headers);
                
                if ($columnIndex !== false) {
                    // Convert row and column indices to A1 notation
                    // Row index needs +1 because sheets are 1-indexed
                    $cellRange = $this->columnIndexToLetter($columnIndex) . ($targetRowIndex + 1);
                    $fullRange = $this->sheetName . '!' . $cellRange;
                    
                    $valueRange = new \Google\Service\Sheets\ValueRange();
                    $valueRange->setValues([[(string)$value]]);
                    
                    $updateRequests[] = [
                        'range' => $fullRange,
                        'values' => $valueRange
                    ];
                }
            }
            
            // Execute batch update
            if (!empty($updateRequests)) {
                $batchUpdateRequest = new \Google\Service\Sheets\BatchUpdateValuesRequest();
                $batchUpdateRequest->setValueInputOption('USER_ENTERED');
                $batchUpdateRequest->setData(array_map(function($req) {
                    return $req['values']->setRange($req['range']);
                }, $updateRequests));
                
                $this->service->spreadsheets_values->batchUpdate(
                    $this->spreadsheetId,
                    $batchUpdateRequest
                );
                
                // Clear cache after update
                $this->clearCache();
                
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to update property in Google Sheet', [
                'property_id' => $id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Convert column index to letter (0 = A, 1 = B, etc.)
     */
    private function columnIndexToLetter(int $index): string
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr($index % 26 + 65) . $letter;
            $index = intval($index / 26) - 1;
        }
        return $letter;
    }
    
    public function appendProperty(array $propertyData): bool
    {
        if (!$this->spreadsheetId) {
            Log::warning('Properties Google Sheets spreadsheet ID not configured');
            return false;
        }

        if (!$this->service) {
            Log::warning('Google Sheets service not initialized for properties');
            return false;
        }

        try {
            // Get headers from the sheet to determine column order
            $headers = $this->getSheetHeaders();
            
            // If no headers exist, create them based on the standard property structure
            if (empty($headers)) {
                $headers = $this->createDefaultHeaders();
            }

            // Map property data to match header order
            $rowData = [];
            foreach ($headers as $header) {
                $normalizedHeader = strtolower(trim($header));
                $value = '';
                
                // Map common header variations to property fields
                $fieldMap = $this->getHeaderMap();
                $propertyField = $fieldMap[$normalizedHeader] ?? $normalizedHeader;
                
                // Get value from property data
                if (isset($propertyData[$propertyField])) {
                    $value = $propertyData[$propertyField];
                } elseif (isset($propertyData[$normalizedHeader])) {
                    $value = $propertyData[$normalizedHeader];
                }
                
                // Handle null/empty values
                if ($value === null || $value === 'N/A' || $value === '') {
                    $value = '';
                }
                
                // Convert arrays to JSON strings
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                
                $rowData[] = (string) $value;
            }

            // Append the row to the sheet
            $range = $this->sheetName . '!A:ZZ';
            $valueRange = new \Google\Service\Sheets\ValueRange();
            $valueRange->setValues([$rowData]);
            $valueRange->setMajorDimension('ROWS');

            $options = ['valueInputOption' => 'USER_ENTERED'];
            $this->service->spreadsheets_values->append(
                $this->spreadsheetId,
                $range,
                $valueRange,
                $options
            );

            Log::info('Property appended to Google Sheet', [
                'property_title' => $propertyData['title'] ?? 'N/A',
                'spreadsheet_id' => $this->spreadsheetId
            ]);

            // Clear cache after adding new property
            $this->clearCache();

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to append property to Google Sheet', [
                'property_title' => $propertyData['title'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get headers from the Google Sheet
     * 
     * @return array
     */
    protected function getSheetHeaders(): array
    {
        try {
            $range = $this->sheetName . '!A1:ZZ1';
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (!empty($values) && !empty($values[0])) {
                return $values[0];
            }
        } catch (\Exception $e) {
            Log::warning('Could not read headers from Google Sheet', [
                'error' => $e->getMessage()
            ]);
        }
        
        return [];
    }

    /**
     * Create default headers based on standard property structure
     * 
     * @return array
     */
    protected function createDefaultHeaders(): array
    {
        return [
            'url', 'title', 'agent_name', 'location', 'latitude', 'longitude',
            'status', 'price', 'description', 'property_type', 'available_date',
            'min_term', 'max_term', 'deposit', 'bills_included', 'furnishings',
            'parking', 'garden', 'broadband', 'housemates', 'total_rooms',
            'smoker', 'pets', 'occupation', 'gender', 'couples_ok', 'smoking_ok',
            'pets_ok', 'pref_occupation', 'references', 'min_age', 'max_age',
            'photo_count', 'first_photo_url', 'all_photos', 'photos', 'paying'
        ];
    }
}
