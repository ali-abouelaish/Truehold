<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Check if Google API Client is available
if (!class_exists('Google\Client')) {
    echo "❌ ERROR: Google API Client classes not found!\n";
    echo "Please run on the server:\n";
    echo "  composer install\n";
    echo "  composer dump-autoload\n\n";
    exit(1);
}

// Check configuration
$spreadsheetId = config('services.google.sheets.spreadsheet_id');
if (empty($spreadsheetId)) {
    echo "❌ ERROR: GOOGLE_SHEETS_SPREADSHEET_ID is not configured in .env file!\n";
    exit(1);
}

$credentialsPath = config('services.google.sheets.credentials_path');
if (empty($credentialsPath) || !file_exists($credentialsPath)) {
    echo "❌ ERROR: Credentials file not found at: {$credentialsPath}\n";
    exit(1);
}

echo "✓ Configuration verified\n\n";

// Get range from command line or use default
$startId = isset($argv[1]) ? (int)$argv[1] : 60;
$endId = isset($argv[2]) ? (int)$argv[2] : 84;

echo "Syncing rental codes from ID {$startId} to {$endId}...\n\n";

$service = new App\Services\GoogleSheetsService();
$successCount = 0;
$failedCount = 0;
$notFoundCount = 0;

for ($id = $startId; $id <= $endId; $id++) {
    $rentalCode = App\Models\RentalCode::find($id);
    
    if (!$rentalCode) {
        echo "[{$id}] ❌ Not found\n";
        $notFoundCount++;
        continue;
    }
    
    $clientName = $rentalCode->client ? $rentalCode->client->full_name : 'N/A';
    echo "[{$id}] {$rentalCode->rental_code} - {$clientName}... ";
    
    try {
        $result = $service->appendRentalCode($rentalCode);
        
        if ($result) {
            echo "✅ Synced\n";
            $successCount++;
        } else {
            echo "❌ Failed\n";
            $failedCount++;
        }
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        $failedCount++;
    }
    
    // Small delay to avoid rate limiting
    usleep(100000); // 0.1 second
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Summary:\n";
echo "  ✅ Successfully synced: {$successCount}\n";
echo "  ❌ Failed: {$failedCount}\n";
echo "  ⚠️  Not found: {$notFoundCount}\n";
echo "  📊 Total processed: " . ($successCount + $failedCount + $notFoundCount) . "\n";
echo str_repeat("=", 50) . "\n";

