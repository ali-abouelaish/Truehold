<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportNewScrapeProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:import-newscrape {--truncate : Truncate existing properties before import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import properties from newscrape.csv file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $csvFile = 'newscrape.csv';
        
        if (!file_exists($csvFile)) {
            $this->error("CSV file not found: $csvFile");
            return 1;
        }

        if ($this->option('truncate')) {
            if ($this->confirm('This will delete ALL existing properties. Are you sure?')) {
                $this->info('Truncating properties table...');
                Property::truncate();
                $this->info('Properties table truncated successfully.');
            } else {
                $this->info('Import cancelled.');
                return 0;
            }
        }

        $this->info('Starting import from newscrape.csv...');
        
        // Read the entire CSV content and parse it properly
        $csvContent = file_get_contents($csvFile);
        if ($csvContent === false) {
            $this->error('Could not read CSV file');
            return 1;
        }

        // Parse CSV with proper multi-line support
        $csvData = $this->parseMultiLineCsv($csvContent);
        
        if (empty($csvData)) {
            $this->error('Could not parse CSV data');
            return 1;
        }

        $headers = array_keys($csvData[0]);
        $this->info('CSV Headers: ' . implode(', ', $headers));
        $this->info('Total columns: ' . count($headers));
        $this->info('Total rows found: ' . count($csvData));

        $rowCount = 0;
        $importedCount = 0;
        $updatedCount = 0;
        $errorCount = 0;
        $batchSize = 100;
        $batch = [];

        // Process each row
        foreach ($csvData as $rowData) {
            $rowCount++;
            
            // Skip rows with incorrect column count
            if (count($rowData) !== count($headers)) {
                $this->warn("Row $rowCount: Column count mismatch. Expected " . count($headers) . ", got " . count($rowData));
                $errorCount++;
                continue;
            }

            // Create data array from headers and row
            $data = $rowData;
            
            // Clean and validate data
            $cleanedData = $this->cleanData($data);
            
            // Ensure we have a 'link' field (map 'url' to 'link' if needed)
            if (isset($cleanedData['url']) && !isset($cleanedData['link'])) {
                $cleanedData['link'] = $cleanedData['url'];
            }
            
            // Remove 'url' field if it exists since database only has 'link'
            unset($cleanedData['url']);
            
            // Handle both 'url' and 'link' field names
            $linkField = $cleanedData['link'] ?? $cleanedData['url'] ?? null;
            
            // Skip if essential data is missing
            if (empty($linkField) || empty($cleanedData['title'])) {
                $this->warn("Row $rowCount: Missing essential data (url/link or title)");
                $errorCount++;
                continue;
            }
            
            // Check if property already exists (by link)
            $existingProperty = null;
            if (!empty($linkField)) {
                $existingProperty = Property::where('link', $linkField)->first();
            }

            if ($existingProperty) {
                // Update existing property
                try {
                    $existingProperty->update($cleanedData);
                    $updatedCount++;
                    $this->line("Updated: {$cleanedData['title']}");
                } catch (\Exception $e) {
                    $this->error("Error updating property: " . $e->getMessage());
                    $errorCount++;
                }
            } else {
                // Add to batch for creation
                $batch[] = $cleanedData;
                
                if (count($batch) >= $batchSize) {
                    $this->processBatch($batch, $importedCount);
                    $batch = [];
                }
            }

            if ($rowCount % 100 === 0) {
                $this->info("Processed $rowCount rows...");
            }
        }

        // Process remaining batch
        if (!empty($batch)) {
            $this->processBatch($batch, $importedCount);
        }

        $this->info("\nImport completed!");
        $this->info("Total rows processed: $rowCount");
        $this->info("New properties imported: $importedCount");
        $this->info("Existing properties updated: $updatedCount");
        $this->info("Errors: $errorCount");

        return 0;
    }
    
    /**
     * Parse CSV content with proper multi-line field support
     */
    private function parseMultiLineCsv(string $csvContent): array
    {
        $lines = explode("\n", $csvContent);
        $headers = [];
        $data = [];
        $currentRow = [];
        $inQuotedField = false;
        $currentField = '';
        $fieldCount = 0;
        
        foreach ($lines as $lineNum => $line) {
            $line = rtrim($line, "\r");
            
            // First line contains headers
            if ($lineNum === 0) {
                $headers = $this->parseCsvLine($line);
                $fieldCount = count($headers);
                continue;
            }
            
            // Skip empty lines
            if (trim($line) === '') {
                continue;
            }
            
            // Parse the line character by character to handle quoted fields with line breaks
            $chars = str_split($line);
            $i = 0;
            
            while ($i < count($chars)) {
                $char = $chars[$i];
                
                if ($char === '"') {
                    if ($inQuotedField) {
                        // Check if this is an escaped quote
                        if ($i + 1 < count($chars) && $chars[$i + 1] === '"') {
                            $currentField .= '"';
                            $i += 2; // Skip both quotes
                            continue;
                        } else {
                            // End of quoted field
                            $inQuotedField = false;
                        }
                    } else {
                        // Start of quoted field
                        $inQuotedField = true;
                    }
                } elseif ($char === ',' && !$inQuotedField) {
                    // Field separator
                    $currentRow[] = $currentField;
                    $currentField = '';
                } else {
                    $currentField .= $char;
                }
                
                $i++;
            }
            
            // If we're not in a quoted field, this line is complete
            if (!$inQuotedField) {
                // Add the last field
                $currentRow[] = $currentField;
                $currentField = '';
                
                // If we have the right number of fields, add the row
                if (count($currentRow) === $fieldCount) {
                    $data[] = array_combine($headers, $currentRow);
                    $currentRow = [];
                }
            } else {
                // We're in a quoted field that spans multiple lines, add a newline
                $currentField .= "\n";
            }
        }
        
        // Handle the last row if it doesn't end with a newline
        if (!empty($currentRow) || !empty($currentField)) {
            if (!empty($currentField)) {
                $currentRow[] = $currentField;
            }
            if (count($currentRow) === $fieldCount) {
                $data[] = array_combine($headers, $currentRow);
            }
        }
        
        return $data;
    }
    
    /**
     * Parse a simple CSV line (for headers)
     */
    private function parseCsvLine(string $line): array
    {
        $fields = [];
        $current = '';
        $inQuotes = false;
        
        for ($i = 0; $i < strlen($line); $i++) {
            $char = $line[$i];
            
            if ($char === '"') {
                if ($inQuotes && $i + 1 < strlen($line) && $line[$i + 1] === '"') {
                    $current .= '"';
                    $i++; // Skip next quote
                } else {
                    $inQuotes = !$inQuotes;
                }
            } elseif ($char === ',' && !$inQuotes) {
                $fields[] = $current;
                $current = '';
            } else {
                $current .= $char;
            }
        }
        
        $fields[] = $current;
        return $fields;
    }
    
    /**
     * Clean and validate data from CSV
     */
    private function cleanData(array $data): array
    {
        $cleaned = [];
        
        foreach ($data as $key => $value) {
            $value = trim($value);
            
            // Skip empty values
            if (empty($value) || $value === 'N/A' || $value === 'null' || $value === 'undefined') {
                continue;
            }

            // Handle specific field types
            switch ($key) {
                case 'latitude':
                case 'longitude':
                    if (is_numeric($value)) {
                        $cleaned[$key] = (float) $value;
                    }
                    break;
                    
                case 'price':
                    // Remove currency symbols and convert to numeric
                    $price = preg_replace('/[^0-9.]/', '', $value);
                    if (is_numeric($price)) {
                        $cleaned[$key] = (float) $price;
                    } else {
                        $cleaned[$key] = $value; // Keep original if can't parse
                    }
                    break;
                    
                case 'photos':
                    // Handle photos array - parse JSON or comma-separated URLs
                    $cleaned[$key] = $this->parsePhotos($value);
                    break;
                    
                case 'all_photos':
                    // Handle all_photos - parse comma-separated URLs
                    $cleaned[$key] = $this->parseAllPhotos($value);
                    break;
                    
                case 'description':
                    // Preserve description formatting but clean problematic characters
                    $cleaned[$key] = $this->cleanDescription($value);
                    break;
                    
                case 'min_age':
                case 'max_age':
                case 'photo_count':
                case 'total_rooms':
                    if (is_numeric($value)) {
                        $cleaned[$key] = (int) $value;
                    } else {
                        $cleaned[$key] = $value;
                    }
                    break;
                    
                default:
                    // Clean special characters and encoding issues
                    $cleaned[$key] = $this->cleanString($value);
                    break;
            }
        }

        return $cleaned;
    }
    
    /**
     * Clean string data to remove problematic characters
     */
    private function cleanString(string $value): string
    {
        // Convert to UTF-8 and remove BOM if present
        $value = str_replace("\xEF\xBB\xBF", '', $value);
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        
        // Remove or replace problematic Unicode characters
        $value = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $value); // Emojis
        $value = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $value); // Misc symbols
        $value = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $value); // Transport symbols
        $value = preg_replace('/[\x{1F1E0}-\x{1F1FF}]/u', '', $value); // Flag symbols
        $value = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $value);   // Misc symbols
        $value = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $value);   // Dingbats
        $value = preg_replace('/[\x{1F000}-\x{1F02F}]/u', '', $value); // Mahjong tiles
        $value = preg_replace('/[\x{1F0A0}-\x{1F0FF}]/u', '', $value); // Playing cards
        $value = preg_replace('/[\x{1F200}-\x{1F2FF}]/u', '', $value); // Enclosed characters
        $value = preg_replace('/[\x{1F700}-\x{1F77F}]/u', '', $value); // Alchemical symbols
        $value = preg_replace('/[\x{1F780}-\x{1F7FF}]/u', '', $value); // Geometric shapes extended
        $value = preg_replace('/[\x{1F800}-\x{1F8FF}]/u', '', $value); // Supplemental arrows-C
        $value = preg_replace('/[\x{1F900}-\x{1F9FF}]/u', '', $value); // Supplemental symbols and pictographs
        $value = preg_replace('/[\x{1FA00}-\x{1FA6F}]/u', '', $value); // Chess symbols
        $value = preg_replace('/[\x{1FA70}-\x{1FAFF}]/u', '', $value); // Symbols and pictographs extended-A
        
        // Remove additional problematic characters that cause MySQL errors
        $value = preg_replace('/[\x{2000}-\x{206F}]/u', ' ', $value); // General punctuation
        $value = preg_replace('/[\x{2070}-\x{209F}]/u', '', $value);  // Superscripts and subscripts
        $value = preg_replace('/[\x{20A0}-\x{20CF}]/u', '', $value);  // Currency symbols
        $value = preg_replace('/[\x{2100}-\x{214F}]/u', '', $value);  // Letterlike symbols
        $value = preg_replace('/[\x{2190}-\x{21FF}]/u', '', $value);  // Arrows
        $value = preg_replace('/[\x{2200}-\x{22FF}]/u', '', $value);  // Mathematical operators
        $value = preg_replace('/[\x{2300}-\x{23FF}]/u', '', $value);  // Miscellaneous technical
        $value = preg_replace('/[\x{2400}-\x{243F}]/u', '', $value);  // Control pictures
        $value = preg_replace('/[\x{2440}-\x{245F}]/u', '', $value);  // Optical character recognition
        $value = preg_replace('/[\x{2460}-\x{24FF}]/u', '', $value);  // Enclosed alphanumerics
        $value = preg_replace('/[\x{25A0}-\x{25FF}]/u', '', $value);  // Geometric shapes
        $value = preg_replace('/[\x{2B00}-\x{2BFF}]/u', '', $value);  // Miscellaneous symbols and arrows
        
        // Remove additional problematic Unicode ranges
        $value = preg_replace('/[\x{FE00}-\x{FE0F}]/u', '', $value);  // Variation selectors
        $value = preg_replace('/[\x{FE20}-\x{FE2F}]/u', '', $value);  // Combining half marks
        $value = preg_replace('/[\x{FEFF}]/u', '', $value);           // Zero width no-break space
        $value = preg_replace('/[\x{FF00}-\x{FFEF}]/u', '', $value);  // Halfwidth and fullwidth forms
        $value = preg_replace('/[\x{FFF0}-\x{FFFF}]/u', '', $value);  // Specials
        
        // Remove any remaining 4-byte UTF-8 characters that cause MySQL issues
        $value = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $value);
        
        // Remove specific problematic 4-byte UTF-8 sequences that appear in the data
        $value = preg_replace('/\xF0[\x90-\xBF][\x80-\xBF][\x80-\xBF]/', '', $value);
        $value = preg_replace('/\xF1[\x80-\xBF][\x80-\xBF][\x80-\xBF]/', '', $value);
        $value = preg_replace('/\xF2[\x80-\xBF][\x80-\xBF][\x80-\xBF]/', '', $value);
        $value = preg_replace('/\xF3[\x80-\xBF][\x80-\xBF][\x80-\xBF]/', '', $value);
        $value = preg_replace('/\xF4[\x80-\x8F][\x80-\xBF][\x80-\xBF]/', '', $value);
        
        // Remove any remaining invalid UTF-8 sequences
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        // Ensure the string is valid UTF-8
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        
        // Remove any remaining invalid UTF-8 sequences after conversion
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        // Remove specific problematic characters that appear in the error messages
        $value = str_replace([
            "\xE2\x80\xA6", // Horizontal ellipsis
            "\xE2\x80\xA2", // Bullet
            "\xE2\x80\xA3", // Triangular bullet
            "\xE2\x80\xB9", // Single left-pointing angle quotation mark
            "\xE2\x80\xBA", // Single right-pointing angle quotation mark
            "\xE2\x80\xBC", // Double exclamation mark
            "\xE2\x80\xBD", // Interrobang
            "\xE2\x80\xBE", // Overline
            "\xE2\x81\x80", // Caret insertion point
            "\xE2\x81\x81", // Asterism
            "\xE2\x81\x82", // Hyphen bullet
            "\xE2\x81\x83", // Fraction slash
            "\xE2\x81\x84", // Left square bracket with quill
            "\xE2\x81\x85", // Right square bracket with quill
            "\xE2\x81\x86", // Double question mark
            "\xE2\x81\x87", // Question exclamation mark
            "\xE2\x81\x88", // Latin capital letter T with stroke
            "\xE2\x81\x89", // Latin small letter T with stroke
            "\xE2\x81\x8A", // Latin capital letter I with stroke
            "\xE2\x81\x8B", // Latin small letter I with stroke
            "\xE2\x81\x8C", // Latin capital letter O with stroke
            "\xE2\x81\x8D", // Latin small letter O with stroke
            "\xE2\x81\x8E", // Latin capital letter O with tilde and acute
            "\xE2\x81\x8F", // Latin small letter O with tilde and acute
            "\xE2\x81\x90", // Latin capital letter O with tilde and diaeresis
            "\xE2\x81\x91", // Latin small letter O with tilde and diaeresis
            "\xE2\x81\x92", // Latin capital letter O with tilde and macron
            "\xE2\x81\x93", // Latin small letter O with tilde and macron
            "\xE2\x81\x94", // Latin capital letter O with tilde and grave
            "\xE2\x81\x95", // Latin small letter O with tilde and grave
            "\xE2\x81\x96", // Latin capital letter O with tilde and circumflex
            "\xE2\x81\x97", // Latin small letter O with tilde and circumflex
            "\xE2\x81\x98", // Latin capital letter O with tilde and caron
            "\xE2\x81\x99", // Latin small letter O with tilde and caron
            "\xE2\x81\x9A", // Latin capital letter O with tilde and breve
            "\xE2\x81\x9B", // Latin small letter O with tilde and breve
            "\xE2\x81\x9C", // Latin capital letter O with tilde and ring above
            "\xE2\x81\x9D", // Latin small letter O with tilde and ring above
            "\xE2\x81\x9E", // Latin capital letter O with tilde and double acute
            "\xE2\x81\x9F", // Latin small letter O with tilde and double acute
        ], '', $value);
        
        // Remove other problematic characters that cause database errors
        $value = str_replace([
            "\x80", "\x93", "\x94", "\x96", "\x97", "\x99", "\x9C", "\x9D", "\xAD",
            "\xE2\x80\x93", "\xE2\x80\x94", "\xE2\x80\x96", "\xE2\x80\x97", "\xE2\x80\x99", "\xE2\x80\x9C", "\xE2\x80\x9D"
        ], '', $value);
        
        // Remove any remaining control characters
        $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value);
        
        // Clean up multiple spaces and newlines
        $value = preg_replace('/\s+/', ' ', $value);
        $value = str_replace(["\r", "\n", "\t"], ' ', $value);
        
        // Ensure the string is not too long for database fields
        if (strlen($value) > 65535) {
            $value = substr($value, 0, 65535);
        }
        
        return trim($value);
    }

    /**
     * Parse photos field from CSV
     */
    private function parsePhotos($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // If it's already a JSON string, try to decode it
        if (strpos($value, '[') === 0) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        
        // If it's comma-separated URLs, split them
        if (strpos($value, ',') !== false) {
            $urls = array_map('trim', explode(',', $value));
            $urls = array_filter($urls, function($url) {
                return filter_var($url, FILTER_VALIDATE_URL);
            });
            return $urls;
        }
        
        // Single URL
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return [$value];
        }
        
        return null;
    }
    
    /**
     * Parse all_photos field from CSV
     */
    private function parseAllPhotos($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // If it's comma-separated URLs, split them
        if (strpos($value, ',') !== false) {
            $urls = array_map('trim', explode(',', $value));
            $urls = array_filter($urls, function($url) {
                return filter_var($url, FILTER_VALIDATE_URL);
            });
            return implode(', ', $urls);
        }
        
        // Single URL
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        return $value;
    }
    
    /**
     * Clean description while preserving formatting
     */
    private function cleanDescription($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // Use aggressive Unicode cleaning for descriptions
        $value = $this->aggressiveCleanUnicode($value);
        
        // Remove BOM and convert to UTF-8
        $value = str_replace("\xEF\xBB\xBF", '', $value);
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        
        // Remove problematic characters that cause database errors
        $value = str_replace([
            "\x80", "\x93", "\x94", "\x96", "\x97", "\x99", "\x9C", "\x9D", "\xAD",
            "\xE2\x80\x93", "\xE2\x80\x94", "\xE2\x80\x96", "\xE2\x80\x97", "\xE2\x80\x99", "\xE2\x80\x9C", "\xE2\x80\x9D"
        ], '', $value);
        
        // Remove control characters but preserve newlines and tabs
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        // Clean up multiple spaces but preserve line breaks
        $value = preg_replace('/[ \t]+/', ' ', $value);
        
        // Ensure the string is not too long for database fields
        if (strlen($value) > 65535) {
            $value = substr($value, 0, 65535);
        }
        
        return trim($value);
    }
    
    /**
     * Aggressively clean problematic Unicode characters
     */
    private function aggressiveCleanUnicode(string $value): string
    {
        // Remove all 4-byte UTF-8 characters (emojis, etc.)
        $value = preg_replace('/[\x{1F000}-\x{1F9FF}]/u', '', $value);
        $value = preg_replace('/[\x{1FA00}-\x{1FAFF}]/u', '', $value);
        
        // Remove specific problematic sequences
        $value = str_replace([
            "\xF0\x9F\x98\x82", // 😂
            "\xF0\x9F\x98\x83", // 😃
            "\xF0\x9F\x98\x84", // 😄
            "\xF0\x9F\x98\x85", // 😅
            "\xF0\x9F\x98\x86", // 😆
            "\xF0\x9F\x98\x87", // 😇
            "\xF0\x9F\x98\x88", // 😈
            "\xF0\x9F\x98\x89", // 😉
            "\xF0\x9F\x98\x8A", // 😊
            "\xF0\x9F\x98\x8B", // 😋
            "\xF0\x9F\x98\x8C", // 😌
            "\xF0\x9F\x98\x8D", // 😍
            "\xF0\x9F\x98\x8E", // 😎
            "\xF0\x9F\x98\x8F", // 😏
            "\xF0\x9F\x98\x90", // 😐
            "\xF0\x9F\x98\x91", // 😑
            "\xF0\x9F\x98\x92", // 😒
            "\xF0\x9F\x98\x93", // 😓
            "\xF0\x9F\x98\x94", // 😔
            "\xF0\x9F\x98\x95", // 😕
            "\xF0\x9F\x98\x96", // 😖
            "\xF0\x9F\x98\x97", // 😗
            "\xF0\x9F\x98\x98", // 😘
            "\xF0\x9F\x98\x99", // 😙
            "\xF0\x9F\x98\x9A", // 😚
            "\xF0\x9F\x98\x9B", // 😛
            "\xF0\x9F\x98\x9C", // 😜
            "\xF0\x9F\x98\x9D", // 😝
            "\xF0\x9F\x98\x9E", // 😞
            "\xF0\x9F\x98\x9F", // 😟
            "\xF0\x9F\x98\xA0", // 😠
            "\xF0\x9F\x98\xA1", // 😡
            "\xF0\x9F\x98\xA2", // 😢
            "\xF0\x9F\x98\xA3", // 😣
            "\xF0\x9F\x98\xA4", // 😤
            "\xF0\x9F\x98\xA5", // 😥
            "\xF0\x9F\x98\xA6", // 😦
            "\xF0\x9F\x98\xA7", // 😧
            "\xF0\x9F\x98\xA8", // 😨
            "\xF0\x9F\x98\xA9", // 😩
            "\xF0\x9F\x98\xAA", // 😪
            "\xF0\x9F\x98\xAB", // 😫
            "\xF0\x9F\x98\xAC", // 😬
            "\xF0\x9F\x98\xAD", // 😭
            "\xF0\x9F\x98\xAE", // 😮
            "\xF0\x9F\x98\xAF", // 😯
            "\xF0\x9F\x98\xB0", // 😰
            "\xF0\x9F\x98\xB1", // 😱
            "\xF0\x9F\x98\xB2", // 😲
            "\xF0\x9F\x98\xB3", // 😳
            "\xF0\x9F\x98\xB4", // 😴
            "\xF0\x9F\x98\xB5", // 😵
            "\xF0\x9F\x98\xB6", // 😶
            "\xF0\x9F\x98\xB7", // 😷
            "\xF0\x9F\x98\xB8", // 😸
            "\xF0\x9F\x98\xB9", // 😹
            "\xF0\x9F\x98\xBA", // 😺
            "\xF0\x9F\x98\xBB", // 😻
            "\xF0\x9F\x98\xBC", // 😼
            "\xF0\x9F\x98\xBD", // 😽
            "\xF0\x9F\x98\xBE", // 😾
            "\xF0\x9F\x98\xBF", // 😿
            "\xF0\x9F\x99\x80", // 🙀
            "\xF0\x9F\x99\x81", // 🙁
            "\xF0\x9F\x99\x82", // 🙂
            "\xF0\x9F\x99\x83", // 🙃
            "\xF0\x9F\x99\x84", // 🙄
            "\xF0\x9F\x99\x85", // 🙅
            "\xF0\x9F\x99\x86", // 🙆
            "\xF0\x9F\x99\x87", // 🙇
            "\xF0\x9F\x99\x88", // 🙈
            "\xF0\x9F\x99\x89", // 🙉
            "\xF0\x9F\x99\x8A", // 🙊
            "\xF0\x9F\x99\x8B", // 🙋
            "\xF0\x9F\x99\x8C", // 🙌
            "\xF0\x9F\x99\x8D", // 🙍
            "\xF0\x9F\x99\x8E", // 🙎
            "\xF0\x9F\x99\x8F", // 🙏
        ], '', $value);
        
        // Remove any remaining 4-byte UTF-8 sequences
        $value = preg_replace('/\xF0[\x90-\xBF][\x80-\xBF][\x80-\xBF]/', '', $value);
        $value = preg_replace('/\xF1[\x80-\xBF][\x80-\xBF][\x80-\xBF]/', '', $value);
        $value = preg_replace('/\xF2[\x80-\xBF][\x80-\xBF][\x80-\xBF]/', '', $value);
        $value = preg_replace('/\xF3[\x80-\xBF][\x80-\xBF][\x80-\xBF]/', '', $value);
        $value = preg_replace('/\xF4[\x80-\x8F][\x80-\xBF][\x80-\xBF]/', '', $value);
        
        // Remove any remaining 3-byte UTF-8 sequences that might be problematic
        $value = preg_replace('/\xE2[\x80-\xBF][\x80-\xBF]/', '', $value);
        $value = preg_replace('/\xE3[\x80-\xBF][\x80-\xBF]/', '', $value);
        $value = preg_replace('/\xEF[\x80-\xBF][\x80-\xBF]/', '', $value);
        
        // Remove any remaining invalid UTF-8 sequences
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        // Final safety check - remove any remaining non-ASCII characters that might cause issues
        $value = preg_replace('/[^\x20-\x7E\x0A\x0D]/', '', $value);
        
        return $value;
    }

    /**
     * Process a batch of properties
     */
    private function processBatch(array $batch, int &$importedCount): void
    {
        try {
            Property::insert($batch);
            $importedCount += count($batch);
            $this->line("Imported batch of " . count($batch) . " properties");
        } catch (\Exception $e) {
            $this->error("Error importing batch: " . $e->getMessage());
            
            // Try importing one by one to identify problematic records
            foreach ($batch as $propertyData) {
                try {
                    Property::create($propertyData);
                    $importedCount++;
                } catch (\Exception $e) {
                    $this->error("Error importing property: " . ($propertyData['title'] ?? 'Unknown') . " - " . $e->getMessage());
                }
            }
        }
    }
}
