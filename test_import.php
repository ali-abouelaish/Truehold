<?php
// Test the import command
use Illuminate\Support\Facades\Process;

echo "<h2>Testing Import Command</h2>";

// Test PHP path detection
$phpPaths = [
    'C:\\xampp\\php\\php.exe',
    'php',
    'C:\\Program Files\\PHP\\php.exe',
    'C:\\php\\php.exe'
];

$phpPath = null;
foreach ($phpPaths as $path) {
    echo "<p>Trying PHP path: <code>$path</code> - ";
    
    try {
        $result = Process::run($path . ' --version');
        if ($result->successful()) {
            echo "<span style='color: green;'>✅ Working!</span> Version: " . trim($result->output()) . "</p>";
            $phpPath = $path;
            break;
        } else {
            echo "<span style='color: red;'>❌ Failed</span> - " . $result->errorOutput() . "</p>";
        }
    } catch (Exception $e) {
        echo "<span style='color: red;'>❌ Error</span> - " . $e->getMessage() . "</p>";
    }
}

if ($phpPath) {
    echo "<h3 style='color: green;'>✅ PHP found at: $phpPath</h3>";
    
    // Test running the import command
    echo "<h3>Testing Import Command:</h3>";
    try {
        $result = Process::run($phpPath . ' artisan properties:import-newscrape');
        if ($result->successful()) {
            echo "<p style='color: green;'>✅ Import command working: " . $result->output() . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Import command failed: " . $result->errorOutput() . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Import command error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<h3 style='color: red;'>❌ No working PHP path found</h3>";
}

echo "<h3>Test Complete</h3>";
?>
