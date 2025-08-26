<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Property;

echo "Checking Character Encoding\n";
echo "==========================\n\n";

// Get a property with the encoding issue
$property = Property::where('title', 'like', '%Manor House%')->first();

if ($property) {
    echo "Property: {$property->title}\n";
    echo "Price: '{$property->price}'\n";
    echo "Price bytes: " . bin2hex($property->price) . "\n";
    
    // Check each character
    echo "Character analysis:\n";
    for ($i = 0; $i < strlen($property->price); $i++) {
        $char = $property->price[$i];
        $byte = ord($char);
        $hex = bin2hex($char);
        echo "  Position {$i}: '{$char}' (byte: {$byte}, hex: {$hex})\n";
    }
} else {
    echo "Property not found\n";
}
