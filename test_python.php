<?php
// Test Python installation
use Illuminate\Support\Facades\Process;

echo "<h2>Python Installation Test</h2>";

$pythonCommands = ['python3', 'python', 'py', 'python.exe'];
$workingCommand = null;

foreach ($pythonCommands as $cmd) {
    echo "<p>Trying: <code>$cmd</code> - ";
    
    try {
        $result = Process::run("$cmd --version");
        if ($result->successful()) {
            echo "<span style='color: green;'>✅ Working!</span> Version: " . trim($result->output()) . "</p>";
            $workingCommand = $cmd;
            break;
        } else {
            echo "<span style='color: red;'>❌ Failed</span> - " . $result->errorOutput() . "</p>";
        }
    } catch (Exception $e) {
        echo "<span style='color: red;'>❌ Error</span> - " . $e->getMessage() . "</p>";
    }
}

if ($workingCommand) {
    echo "<h3 style='color: green;'>✅ Python is working with command: $workingCommand</h3>";
    
    // Test required packages
    echo "<h3>Testing Required Packages:</h3>";
    $packages = ['requests', 'beautifulsoup4', 'pandas', 'lxml'];
    
    foreach ($packages as $package) {
        echo "<p>Testing $package - ";
        try {
            $result = Process::run("$workingCommand -c \"import $package; print('OK')\"");
            if ($result->successful()) {
                echo "<span style='color: green;'>✅ Installed</span></p>";
            } else {
                echo "<span style='color: red;'>❌ Not installed</span> - " . $result->errorOutput() . "</p>";
            }
        } catch (Exception $e) {
            echo "<span style='color: red;'>❌ Error</span> - " . $e->getMessage() . "</p>";
        }
    }
} else {
    echo "<h3 style='color: red;'>❌ Python is not installed or not in PATH</h3>";
    echo "<p>Please install Python and add it to your system PATH.</p>";
    echo "<p>See PYTHON_SETUP_GUIDE.md for detailed instructions.</p>";
}
?>
