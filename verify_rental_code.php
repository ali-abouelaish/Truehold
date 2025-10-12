<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the last rental code
$lastCode = \App\Models\RentalCode::orderBy('id', 'desc')->first();

if ($lastCode) {
    echo "Current last rental code: " . $lastCode->rental_code . " (ID: " . $lastCode->id . ")" . PHP_EOL;
} else {
    echo "No rental codes found" . PHP_EOL;
}
