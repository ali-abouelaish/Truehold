<?php
// Simple diagnostic script to check server configuration
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .warning { background-color: #fff3cd; border-color: #ffeaa7; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
        pre { background-color: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Server Diagnostic Report</h1>
    
    <div class="section info">
        <h2>Server Information</h2>
        <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
        <p><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
        <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></p>
        <p><strong>Current Directory:</strong> <?php echo getcwd(); ?></p>
    </div>
    
    <div class="section info">
        <h2>Laravel Information</h2>
        <?php if (file_exists('../artisan')): ?>
            <p class="success">✓ Laravel application detected</p>
            <p><strong>Laravel Version:</strong> 
                <?php 
                $composer = json_decode(file_get_contents('../composer.json'), true);
                echo $composer['require']['laravel/framework'] ?? 'Unknown';
                ?>
            </p>
        <?php else: ?>
            <p class="error">✗ Laravel application not found</p>
        <?php endif; ?>
    </div>
    
    <div class="section info">
        <h2>File Permissions</h2>
        <p><strong>Public Directory:</strong> 
            <?php echo is_writable('.') ? '<span class="success">✓ Writable</span>' : '<span class="error">✗ Not writable</span>'; ?>
        </p>
        <p><strong>CSS Directory:</strong> 
            <?php echo is_dir('css') ? '<span class="success">✓ Exists</span>' : '<span class="error">✗ Does not exist</span>'; ?>
        </p>
        <p><strong>JS Directory:</strong> 
            <?php echo is_dir('js') ? '<span class="success">✓ Exists</span>' : '<span class="error">✗ Does not exist</span>'; ?>
        </p>
    </div>
    
    <div class="section info">
        <h2>Network Connectivity Test</h2>
        <p><strong>CDN Test (Cloudflare):</strong> 
            <?php 
            $context = stream_context_create(['http' => ['timeout' => 5]]);
            $test = @file_get_contents('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', false, $context);
            echo $test ? '<span class="success">✓ Accessible</span>' : '<span class="error">✗ Not accessible</span>';
            ?>
        </p>
        <p><strong>CDN Test (jsDelivr):</strong> 
            <?php 
            $test = @file_get_contents('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', false, $context);
            echo $test ? '<span class="success">✓ Accessible</span>' : '<span class="error">✗ Not accessible</span>';
            ?>
        </p>
    </div>
    
    <div class="section info">
        <h2>PHP Extensions</h2>
        <p><strong>cURL:</strong> <?php echo extension_loaded('curl') ? '<span class="success">✓ Available</span>' : '<span class="error">✗ Not available</span>'; ?></p>
        <p><strong>OpenSSL:</strong> <?php echo extension_loaded('openssl') ? '<span class="success">✓ Available</span>' : '<span class="error">✗ Not available</span>'; ?></p>
        <p><strong>JSON:</strong> <?php echo extension_loaded('json') ? '<span class="success">✓ Available</span>' : '<span class="error">✗ Not available</span>'; ?></p>
    </div>
    
    <div class="section info">
        <h2>Environment Variables</h2>
        <pre><?php 
        $envVars = ['APP_ENV', 'APP_DEBUG', 'APP_URL', 'DB_CONNECTION', 'DB_HOST'];
        foreach ($envVars as $var) {
            echo $var . ': ' . (getenv($var) ?: 'Not set') . "\n";
        }
        ?></pre>
    </div>
    
    <div class="section warning">
        <h2>Recommendations</h2>
        <ul>
            <li>If CDN tests fail, consider using local copies of libraries</li>
            <li>Check firewall settings if external resources are blocked</li>
            <li>Ensure proper file permissions for the public directory</li>
            <li>Consider using a CDN proxy or local mirror</li>
        </ul>
    </div>
    
    <div class="section info">
        <h2>Quick Fixes</h2>
        <p><strong>Test Libraries:</strong> <a href="test-libraries.html" target="_blank">Open Library Test Page</a></p>
        <p><strong>Check Laravel:</strong> <a href="../" target="_blank">Go to Laravel App</a></p>
    </div>
</body>
</html>
