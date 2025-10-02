<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\UserPermission;

try {
    // Find user with ID 2
    $user = User::find(2);
    
    if (!$user) {
        echo "❌ User with ID 2 not found!\n";
        exit(1);
    }
    
    echo "✅ Found user: {$user->email}\n";
    
    // Give admin permissions to all sections
    $sections = ['dashboard', 'properties', 'clients', 'rental-codes', 'call-logs', 'invoices', 'viewings', 'admin-permissions'];
    
    foreach ($sections as $section) {
        // Delete existing permission if it exists
        UserPermission::where('user_id', 2)
            ->where('section', $section)
            ->delete();
            
        // Create new permission
        UserPermission::create([
            'user_id' => 2,
            'section' => $section,
            'can_view' => true,
            'can_create' => true,
            'can_edit' => true,
            'can_delete' => true,
        ]);
        echo "✅ Added full permissions for '{$section}' to user ID 2\n";
    }
    
    echo "\n🎉 Successfully granted admin permissions to user ID 2!\n";
    echo "User {$user->email} can now access all admin sections and manage other users.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
