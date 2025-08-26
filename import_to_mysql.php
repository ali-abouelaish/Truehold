<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔄 Importing data to MySQL...\n\n";

try {
    // Test database connection
    echo "1. Testing MySQL connection...\n";
    DB::connection()->getPdo();
    echo "✅ MySQL connection successful\n\n";
    
    // Check if properties table exists
    echo "2. Checking properties table...\n";
    $tableExists = DB::getSchemaBuilder()->hasTable('properties');
    echo "   Table exists: " . ($tableExists ? 'Yes' : 'No') . "\n";
    
    if (!$tableExists) {
        echo "❌ Properties table does not exist. Please run migrations first:\n";
        echo "   php artisan migrate:fresh\n";
        exit(1);
    }
    
    // Read CSV file
    echo "3. Reading CSV file...\n";
    $csvFile = 'database_export_2025-08-19_10-03-22.csv';
    
    if (!file_exists($csvFile)) {
        echo "❌ CSV file not found: {$csvFile}\n";
        exit(1);
    }
    
    $file = fopen($csvFile, 'r');
    if (!$file) {
        throw new Exception("Could not open CSV file: {$csvFile}");
    }
    
    // Read headers
    $headers = fgetcsv($file);
    if (!$headers) {
        throw new Exception("Could not read CSV headers");
    }
    
    echo "   CSV headers: " . implode(', ', array_slice($headers, 0, 5)) . "...\n";
    
    // Clear existing data
    echo "4. Clearing existing data...\n";
    DB::table('properties')->truncate();
    echo "✅ Table cleared\n\n";
    
    // Import data
    echo "5. Importing data...\n";
    $rowCount = 0;
    $imported = 0;
    $errors = 0;
    
    while (($row = fgetcsv($file)) !== false) {
        $rowCount++;
        
        // Skip header row
        if ($rowCount === 1) {
            continue;
        }
        
        try {
            // Create data array
            $data = array_combine($headers, $row);
            
            // Clean and validate data
            $cleanData = [];
            foreach ($headers as $header) {
                $value = $data[$header] ?? null;
                
                // Handle empty strings
                if ($value === '') {
                    $value = null;
                }
                
                // Handle specific fields
                switch ($header) {
                    case 'latitude':
                    case 'longitude':
                        if ($value && !is_numeric($value)) {
                            $value = null;
                        }
                        break;
                    case 'price':
                        if ($value && $value !== 'N/A') {
                            // Remove currency symbols and convert to number
                            $value = preg_replace('/[^0-9.]/', '', $value);
                            if (!is_numeric($value)) {
                                $value = null;
                            }
                        }
                        break;
                    case 'photo_count':
                        if ($value && !is_numeric($value)) {
                            $value = 0;
                        }
                        break;
                }
                
                $cleanData[$header] = $value;
            }
            
            // Insert into database
            DB::table('properties')->insert($cleanData);
            $imported++;
            
            if ($imported % 10 == 0) {
                echo "   Imported {$imported} properties...\n";
            }
            
        } catch (Exception $e) {
            $errors++;
            echo "   ❌ Error importing row {$rowCount}: " . $e->getMessage() . "\n";
            
            if ($errors > 5) {
                echo "   Too many errors, stopping import\n";
                break;
            }
        }
    }
    
    fclose($file);
    
    echo "\n📊 Import Summary:\n";
    echo "   Total rows processed: {$rowCount}\n";
    echo "   Successfully imported: {$imported}\n";
    echo "   Errors: {$errors}\n";
    
    if ($errors === 0) {
        echo "\n✅ Import completed successfully!\n";
        
        // Verify import
        $count = DB::table('properties')->count();
        echo "   Total properties in database: {$count}\n";
    } else {
        echo "\n⚠️  Import completed with {$errors} errors\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
