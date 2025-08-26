<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Map Loading Issues Diagnostic\n";
echo "================================\n\n";

// Check total properties
$totalProperties = DB::table('properties')->count();
echo "Total properties in database: {$totalProperties}\n\n";

// Check coordinate issues
echo "📍 Coordinate Analysis:\n";
echo "----------------------\n";

$nullLat = DB::table('properties')->whereNull('latitude')->count();
$nullLng = DB::table('properties')->whereNull('longitude')->count();
$emptyLat = DB::table('properties')->where('latitude', '')->count();
$emptyLng = DB::table('properties')->where('longitude', '')->count();

echo "Properties with NULL latitude: {$nullLat}\n";
echo "Properties with NULL longitude: {$nullLng}\n";
echo "Properties with empty latitude: {$emptyLat}\n";
echo "Properties with empty longitude: {$emptyLng}\n";

// Check for invalid coordinate ranges
$invalidLat = DB::table('properties')
    ->whereNotNull('latitude')
    ->where('latitude', '!=', '')
    ->whereRaw('CAST(latitude AS REAL) < -90 OR CAST(latitude AS REAL) > 90')
    ->count();

$invalidLng = DB::table('properties')
    ->whereNotNull('longitude')
    ->where('longitude', '!=', '')
    ->whereRaw('CAST(longitude AS REAL) < -180 OR CAST(longitude AS REAL) > 180')
    ->count();

echo "Properties with invalid latitude range: {$invalidLat}\n";
echo "Properties with invalid longitude range: {$invalidLng}\n\n";

// Check properties that would pass the withValidCoordinates scope
$validCoordinates = DB::table('properties')
    ->whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->where('latitude', '!=', '')
    ->where('longitude', '!=', '')
    ->whereRaw('CAST(latitude AS REAL) BETWEEN -90 AND 90')
    ->whereRaw('CAST(longitude AS REAL) BETWEEN -180 AND 180')
    ->count();

echo "Properties that would pass coordinate validation: {$validCoordinates}\n";
echo "Properties that would FAIL coordinate validation: " . ($totalProperties - $validCoordinates) . "\n\n";

// Show problematic properties
if (($totalProperties - $validCoordinates) > 0) {
    echo "🚨 Problematic Properties:\n";
    echo "-------------------------\n";
    
    $problematic = DB::table('properties')
        ->select('id', 'title', 'location', 'latitude', 'longitude')
        ->where(function($q) {
            $q->whereNull('latitude')
              ->orWhereNull('longitude')
              ->orWhere('latitude', '')
              ->orWhere('longitude', '')
              ->orWhereRaw('CAST(latitude AS REAL) < -90 OR CAST(latitude AS REAL) > 90')
              ->orWhereRaw('CAST(longitude AS REAL) < -180 OR CAST(longitude AS REAL) > 180');
        })
        ->limit(10)
        ->get();
    
    foreach ($problematic as $prop) {
        echo "ID: {$prop->id} | Title: {$prop->title}\n";
        echo "  Location: {$prop->location}\n";
        echo "  Coordinates: '{$prop->latitude}', '{$prop->longitude}'\n";
        echo "---\n";
    }
    
    if ($problematic->count() >= 10) {
        echo "... and " . (($totalProperties - $validCoordinates) - 10) . " more\n";
    }
}

echo "\n💡 Solution:\n";
echo "The map keeps loading because " . ($totalProperties - $validCoordinates) . " properties have invalid coordinates.\n";
echo "These properties are being filtered out by the withValidCoordinates() scope in your Property model.\n";

echo "\nDone!\n";


