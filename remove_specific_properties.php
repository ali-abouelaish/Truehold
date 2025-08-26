<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Property;

echo "Removing Properties with Specific Problematic Descriptions\n";
echo "=======================================================\n\n";

// Get all properties
$properties = Property::all();
echo "Total properties: {$properties->count()}\n\n";

$propertiesToRemove = [];
$normalProperties = [];

foreach ($properties as $property) {
    $description = $property->description;
    $title = $property->title;
    $id = $property->id;
    
    $shouldRemove = false;
    $reason = '';
    
    // Check for specific problematic patterns
    if (strpos($description, '850 pcm(whole property)This ad is for a 1 bed flat') !== false) {
        $shouldRemove = true;
        $reason = 'Exact match: 850 pcm(whole property)This ad is for a 1 bed flat';
    }
    
    // Check for similar patterns
    if (preg_match('/\d+\s*pcm\(whole property\)This ad is for a \d+ bed flat/', $description)) {
        $shouldRemove = true;
        $reason = 'Pattern match: X pcm(whole property)This ad is for a X bed flat';
    }
    
    // Check for descriptions that start with price and "whole property"
    if (preg_match('/^\d+\s*pcm\(whole property\)/', $description)) {
        $shouldRemove = true;
        $reason = 'Starts with: X pcm(whole property)';
    }
    
    // Check for descriptions that contain "This ad is for a" followed by property type
    if (strpos($description, 'This ad is for a') !== false && 
        (strpos($description, 'bed flat') !== false || strpos($description, 'studio') !== false)) {
        $shouldRemove = true;
        $reason = 'Contains: "This ad is for a" with property type';
    }
    
    // Check for very short descriptions that are likely corrupted
    if (strlen($description) < 20 && strpos($description, 'pcm') !== false) {
        $shouldRemove = true;
        $reason = 'Very short description with pcm (likely corrupted)';
    }
    
    if ($shouldRemove) {
        $propertiesToRemove[] = [
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'reason' => $reason
        ];
        echo "❌ To Remove: ID {$id} | Title: " . substr($title, 0, 40) . "... | Reason: {$reason}\n";
        echo "   Description: " . substr($description, 0, 80) . "...\n";
    } else {
        $normalProperties[] = [
            'id' => $id,
            'title' => $title,
            'description' => $description
        ];
    }
}

echo "\nSUMMARY\n";
echo "=======\n";
echo "Total properties: " . count($properties) . "\n";
echo "Properties to keep: " . count($normalProperties) . "\n";
echo "Properties to remove: " . count($propertiesToRemove) . "\n";

if (count($propertiesToRemove) > 0) {
    echo "\nPROPERTIES TO BE REMOVED:\n";
    echo "=========================\n";
    foreach ($propertiesToRemove as $prop) {
        echo "ID: {$prop['id']} | Title: " . substr($prop['title'], 0, 40) . "...\n";
        echo "Description: " . substr($prop['description'], 0, 100) . "...\n";
        echo "Reason: {$prop['reason']}\n";
        echo "---\n";
    }
    
    echo "\nDo you want to remove these properties? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $response = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($response) === 'y') {
        echo "\nRemoving problematic properties...\n";
        $removed = 0;
        
        foreach ($propertiesToRemove as $prop) {
            $property = Property::find($prop['id']);
            if ($property) {
                $property->delete();
                $removed++;
                echo "✅ Removed property ID {$prop['id']}\n";
            }
        }
        
        echo "\nRemoval complete! Removed {$removed} properties.\n";
        
        // Show final count
        $finalCount = Property::count();
        echo "Final property count: {$finalCount}\n";
    } else {
        echo "\nNo properties were removed.\n";
    }
} else {
    echo "\n✅ No problematic properties found! All properties have acceptable descriptions.\n";
}

echo "\nDone!\n";

