<?php
// Test Python path detection
use Illuminate\Support\Facades\Process;

echo "<h2>Python Path Detection Test</h2>";

$possiblePaths = [
    'C:\\Users\\Ali\\AppData\\Local\\Programs\\Python\\Python311\\python.exe',
    'C:\\Users\\Ali\\AppData\\Local\\Programs\\Python\\Python312\\python.exe',
    'C:\\Python311\\python.exe',
    'C:\\Python312\\python.exe',
    'python3',
    'python',
    'py',
    'python.exe'
];

$workingPath = null;

foreach ($possiblePaths as $path) {
    echo "<p>Trying: <code>$path</code> - ";
    
    try {
        $result = Process::run($path . ' --version');
        if ($result->successful()) {
            echo "<span style='color: green;'>✅ Working!</span> Version: " . trim($result->output()) . "</p>";
            $workingPath = $path;
            break;
        } else {
            echo "<span style='color: red;'>❌ Failed</span> - " . $result->errorOutput() . "</p>";
        }
    } catch (Exception $e) {
        echo "<span style='color: red;'>❌ Error</span> - " . $e->getMessage() . "</p>";
    }
}

if ($workingPath) {
    echo "<h3 style='color: green;'>✅ Python found at: $workingPath</h3>";
    
    // Test running a simple Python command
    echo "<h3>Testing Python execution:</h3>";
    try {
        $result = Process::run($workingPath . ' -c "print(\'Hello from Python!\')"');
        if ($result->successful()) {
            echo "<p style='color: green;'>✅ Python execution working: " . $result->output() . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Python execution failed: " . $result->errorOutput() . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Python execution error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<h3 style='color: red;'>❌ No working Python path found</h3>";
    echo "<p>Please install Python and add it to your system PATH.</p>";
}

echo "<h3>Recommendation:</h3>";
if ($workingPath) {
    echo "<p style='color: green;'>✅ Use the Python scraper - it should work now!</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Use the PHP scraper instead - it doesn't require Python.</p>";
}
?>

