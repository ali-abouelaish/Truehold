<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Sheets as GoogleSheets;
use Google\Service\Sheets\ValueRange as GoogleSheetsValueRange;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;
    protected $sheetName;

    public function __construct()
    {
        $this->spreadsheetId = config('services.google.sheets.spreadsheet_id');
        $this->sheetName = config('services.google.sheets.sheet_name', 'Sheet1');
        
        // Initialize client only if spreadsheet ID is configured
        if ($this->spreadsheetId) {
            try {
                $this->initializeClient();
            } catch (\Exception $e) {
                Log::warning('Google Sheets client initialization failed, but continuing', [
                    'error' => $e->getMessage()
                ]);
                // Don't throw - allow the service to be created but mark as unavailable
            }
        }
    }

    protected function initializeClient()
    {
        try {
            $this->client = new GoogleClient();
            $this->client->setApplicationName('Property Scraper App');
            $this->client->setScopes([GoogleSheets::SPREADSHEETS]);
            $this->client->setAccessType('offline');
            $this->client->setPrompt('select_account consent');

            // Use service account credentials if available
            $credentialsPath = config('services.google.sheets.credentials_path');
            if ($credentialsPath && file_exists($credentialsPath)) {
                $this->client->setAuthConfig($credentialsPath);
            } else {
                // Fallback to JSON credentials from config
                $credentialsJson = config('services.google.sheets.credentials_json');
                if ($credentialsJson) {
                    $credentials = json_decode($credentialsJson, true);
                    if ($credentials) {
                        $this->client->setAuthConfig($credentials);
                    }
                }
            }

            $this->service = new GoogleSheets($this->client);
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google Sheets client', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Append a rental code row to the Google Sheet
     */
    public function appendRentalCode($rentalCode)
    {
        if (!$this->spreadsheetId) {
            Log::warning('Google Sheets spreadsheet ID not configured');
            return false;
        }

        if (!$this->service) {
            Log::warning('Google Sheets service not initialized');
            return false;
        }

        try {
            // Load relationships
            $rentalCode->load(['client', 'rentalAgent', 'marketingAgentUser']);

            // Calculate commission values (matching the logic from RentalCodeController)
            $totalFee = (float) ($rentalCode->consultation_fee ?? 0);
            $paymentMethod = $rentalCode->payment_method ?? '';
            
            // Calculate base commission after VAT (for Transfer/Card machine payments)
            $baseCommission = $totalFee;
            if (in_array($paymentMethod, ['Transfer', 'Card machine'])) {
                // Subtract 20% VAT for transfer/card machine payments
                $baseCommission = $totalFee * 0.8;
            }
            
            // Calculate commission split: Agency 45%, Agent 55%
            $agentCut = $baseCommission * 0.55;
            
            // Calculate marketing fee (if marketing agent is different from rental agent)
            $marketingFee = 0;
            $clientCount = $rentalCode->client_count ?? 1;
            $hasDifferentMarketingAgent = false;
            
            if (!empty($rentalCode->marketing_agent_id) && !empty($rentalCode->rental_agent_id)) {
                $hasDifferentMarketingAgent = (int) $rentalCode->marketing_agent_id !== (int) $rentalCode->rental_agent_id;
            }
            
            if ($hasDifferentMarketingAgent) {
                // £30 for single client, £40 for multiple clients
                $marketingFee = $clientCount > 1 ? 40.0 : 30.0;
                $agentCut -= $marketingFee; // Deduct from rental agent
            }
            
            // Get agent names
            $assistedBy = $rentalCode->rentalAgent->name ?? 'N/A';
            $clientOf = $hasDifferentMarketingAgent 
                ? ($rentalCode->marketingAgentUser->name ?? 'N/A')
                : 'N/A';

            // Prepare row data matching your Google Sheet columns
            $rowData = [
                $rentalCode->rental_code ?? '',                                    // Rental Code
                $rentalCode->rental_date ? $rentalCode->rental_date->format('Y-m-d') : '', // Date
                $rentalCode->client->full_name ?? 'N/A',                          // Name
                $rentalCode->property ?? 'N/A',                                    // Property
                $paymentMethod,                                                    // Payment Method
                $assistedBy,                                                       // Assisted by
                $clientOf,                                                         // Client of
                number_format($marketingFee, 2),                                   // Marketing fee
                number_format($baseCommission, 2),                                // Total Sourcing
                number_format($agentCut, 2),                                       // Agent cut
            ];

            // Check if headers exist, if not, add them
            $this->ensureHeadersExist();

            // Append the row (10 columns: A to J)
            $range = $this->sheetName . '!A:J';
            $valueRange = new GoogleSheetsValueRange();
            $valueRange->setValues([$rowData]);
            $valueRange->setMajorDimension('ROWS');

            $options = ['valueInputOption' => 'USER_ENTERED'];
            $this->service->spreadsheets_values->append(
                $this->spreadsheetId,
                $range,
                $valueRange,
                $options
            );

            Log::info('Rental code appended to Google Sheet', [
                'rental_code' => $rentalCode->rental_code,
                'spreadsheet_id' => $this->spreadsheetId
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to append rental code to Google Sheet', [
                'rental_code' => $rentalCode->rental_code ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Ensure headers exist in the sheet
     */
    protected function ensureHeadersExist()
    {
        try {
            $range = $this->sheetName . '!A1:J1';
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();

            // If no headers exist, add them
            if (empty($values) || empty($values[0])) {
                $headers = [
                    'Rental Code',
                    'Date',
                    'Name',
                    'Property',
                    'Payment Method',
                    'Assissted by',  // Note: matching user's spelling
                    'Client of',
                    'Marketing fee',
                    'Total Sourcing',
                    'Agent cut'
                ];

                $valueRange = new GoogleSheetsValueRange();
                $valueRange->setValues([$headers]);
                $valueRange->setMajorDimension('ROWS');

                $options = ['valueInputOption' => 'RAW'];
                $this->service->spreadsheets_values->update(
                    $this->spreadsheetId,
                    $range,
                    $valueRange,
                    $options
                );
            }
        } catch (\Exception $e) {
            // If sheet doesn't exist or is empty, try to create headers
            Log::warning('Could not check/update headers in Google Sheet', [
                'error' => $e->getMessage()
            ]);
        }
    }
}

