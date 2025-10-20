<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RentalCodeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\GroupViewingController;
use App\Http\Controllers\CallLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\PhpScraperController;
use App\Http\Controllers\RentalCodeCashDocumentController;
use App\Http\Controllers\AgentProfileController;
use Twilio\Rest\Client;

Route::get('/', function () {
    return redirect('/properties');
});


// Public routes - no authentication required
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/map', [PropertyController::class, 'map'])->name('properties.map');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
Route::get('/rental-codes/agent-earnings', [RentalCodeController::class, 'agentEarnings'])->name('rental-codes.agent-earnings');

// Storage file serving route
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    
    if (!file_exists($filePath)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($filePath);
    $fileSize = filesize($filePath);
    
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Content-Length' => $fileSize,
    ]);
})->where('path', '.*')->name('storage.serve');
    
// Temporary public route for testing rental code generation
Route::get('/test-rental-code', function () {
    try {
        $lastRentalCode = \App\Models\RentalCode::orderBy('id', 'desc')->first();
        
        if (!$lastRentalCode) {
            // First rental code starts from CC0121
            $nextNumber = 121;
        } else {
            // Extract number from last code (e.g., "CC0121" -> 121)
            preg_match('/CC(\d+)/', $lastRentalCode->rental_code, $matches);
            if (isset($matches[1])) {
                $lastNumber = (int)$matches[1];
                // If the last number is less than 121, start from 121
                $nextNumber = $lastNumber >= 121 ? $lastNumber + 1 : 121;
            } else {
                // If no valid number found, start from 121
                $nextNumber = 121;
            }
        }
        
        // Format as CC0121, CC0122, etc.
        $newCode = 'CC' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        return response()->json(['code' => $newCode]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to generate rental code: ' . $e->getMessage()], 500);
    }
});

// WhatsApp test route
Route::get('/test-whatsapp', function () {
    $sid    = env('TWILIO_ACCOUNT_SID');
    $token  = env('TWILIO_AUTH_TOKEN');
    $testNumber = env('TEST_WHATSAPP_NUMBER');
    $whatsappNumber = env('TWILIO_WHATSAPP_NUMBER');
    
    // Check if test number is configured
    if (!$testNumber || $testNumber === 'whatsapp:+1234567890') {
        return response()->json([
            'error' => 'Test WhatsApp number not configured',
            'message' => 'Please update TEST_WHATSAPP_NUMBER in your .env file with a real WhatsApp number',
            'format' => 'Use format: whatsapp:+1234567890 (include country code)',
            'example' => 'whatsapp:+447123456789 for UK number'
        ], 400);
    }
    
    try {
        $twilio = new Client($sid, $token);

        $message = $twilio->messages
            ->create(
                $testNumber, // to
                [
                    "from" => $whatsappNumber,
                    "body" => "✅ Hello from Laravel CRM! Your Twilio WhatsApp setup is working perfectly."
                ]
            );

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp message sent successfully!',
            'sid' => $message->sid,
            'to' => $testNumber,
            'from' => $whatsappNumber
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'error' => 'Failed to send WhatsApp message',
            'message' => $e->getMessage(),
            'details' => [
                'to' => $testNumber,
                'from' => $whatsappNumber,
                'account_sid' => $sid ? 'Configured' : 'Missing'
            ]
        ], 500);
    }
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    })->name('login.post');
});

// Admin routes - require authentication
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/properties', [AdminController::class, 'properties'])->name('admin.properties');
    Route::get('/properties/create', [AdminController::class, 'create'])->name('admin.properties.create');
    Route::post('/properties', [AdminController::class, 'store'])->name('admin.properties.store');
    Route::get('/properties/{property}/edit', [AdminController::class, 'edit'])->name('admin.properties.edit');
    Route::put('/properties/{property}', [AdminController::class, 'update'])->name('admin.properties.update');
    Route::patch('/properties/{property}/toggle-updatable', [AdminController::class, 'toggleUpdatable'])->name('admin.properties.toggle-updatable');
    Route::delete('/properties/{property}', [AdminController::class, 'destroy'])->name('admin.properties.destroy');
    Route::post('/upload-image', [AdminController::class, 'uploadImage'])->name('admin.upload-image');

    // Property interested clients
    Route::post('/properties/{property}/interests', [AdminController::class, 'addInterestedClient'])->name('admin.properties.interests.add');
    Route::delete('/properties/{property}/interests/{client}', [AdminController::class, 'removeInterestedClient'])->name('admin.properties.interests.remove');

    // Group Viewings
    Route::get('/group-viewings', [GroupViewingController::class, 'index'])->name('admin.group-viewings.index');
    Route::get('/group-viewings/create', [GroupViewingController::class, 'create'])->name('admin.group-viewings.create');
    Route::post('/group-viewings', [GroupViewingController::class, 'store'])->name('admin.group-viewings.store');
    Route::get('/group-viewings/{groupViewing}/attendees', [GroupViewingController::class, 'attendees'])->name('admin.group-viewings.attendees');
    
    // User Management Routes
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    
    // Client Management Routes
    Route::get('/clients', [AdminController::class, 'clients'])->name('admin.clients');
    Route::get('/clients/create', [AdminController::class, 'createClient'])->name('admin.clients.create');
    Route::post('/clients', [AdminController::class, 'storeClient'])->name('admin.clients.store');
    Route::get('/clients/{client}/edit', [AdminController::class, 'editClient'])->name('admin.clients.edit');
    Route::put('/clients/{client}', [AdminController::class, 'updateClient'])->name('admin.clients.update');
    Route::delete('/clients/{client}', [AdminController::class, 'destroyClient'])->name('admin.clients.destroy');
    Route::post('/clients/{client}/toggle-registration', [AdminController::class, 'toggleRegistrationStatus'])->name('admin.clients.toggle-registration');
    
    // Rental Code Management Routes
    Route::resource('rental-codes', RentalCodeController::class);
    Route::get('/rental-codes/generate-code', [RentalCodeController::class, 'generateCode'])->name('rental-codes.generate-code');
    Route::post('/rental-codes/{rentalCode}/mark-paid', [RentalCodeController::class, 'markAsPaid'])->name('rental-codes.mark-paid');
    Route::post('/rental-codes/{rentalCode}/mark-unpaid', [RentalCodeController::class, 'markAsUnpaid'])->name('rental-codes.mark-unpaid');
    Route::post('/rental-codes/{rentalCode}/update-status', [RentalCodeController::class, 'updateStatus'])->name('rental-codes.update-status');
    Route::post('/rental-codes/bulk-update-status', [RentalCodeController::class, 'bulkUpdateStatus'])->name('rental-codes.bulk-update-status');
    Route::get('/rental-codes/agent/{agentName}', [RentalCodeController::class, 'agentDetails'])->name('rental-codes.agent-details');
    Route::get('/rental-codes/{rentalCode}/details', [RentalCodeController::class, 'getRentalDetails'])->name('rental-codes.details');
    
    // Marketing Agent Management Routes
    Route::get('/marketing-agents', [RentalCodeController::class, 'marketingAgents'])->name('marketing-agents.index');
    Route::post('/marketing-agents', [RentalCodeController::class, 'storeMarketingAgent'])->name('marketing-agents.store');
    Route::delete('/marketing-agents/{user}', [RentalCodeController::class, 'removeMarketingAgent'])->name('marketing-agents.remove');
    
    // Invoice Management Routes
    Route::resource('invoices', InvoiceController::class)->names([
        'index' => 'admin.invoices.index',
        'create' => 'admin.invoices.create',
        'store' => 'admin.invoices.store',
        'show' => 'admin.invoices.show',
        'edit' => 'admin.invoices.edit',
        'update' => 'admin.invoices.update',
        'destroy' => 'admin.invoices.destroy'
    ]);
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('admin.invoices.pdf');
    Route::post('/invoices/{invoice}/mark-sent', [InvoiceController::class, 'markAsSent'])->name('admin.invoices.mark-sent');
    Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('admin.invoices.mark-paid');
    Route::post('/invoices/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->name('admin.invoices.duplicate');
    
    // Call Log Management Routes - Specific routes first
    Route::get('/call-logs/stats', [CallLogController::class, 'stats'])->name('admin.call-logs.stats');
    Route::get('/call-logs/follow-ups', [CallLogController::class, 'followUps'])->name('admin.call-logs.follow-ups');
    Route::get('/call-logs/recent', [CallLogController::class, 'recent'])->name('admin.call-logs.recent');
    Route::get('/call-logs/check-phone', [CallLogController::class, 'checkPhone'])->name('admin.call-logs.check-phone');
    Route::get('/call-logs/previous-calls', [CallLogController::class, 'getPreviousCalls'])->name('admin.call-logs.previous-calls');
    Route::post('/call-logs/{call_log}/update-next-step', [CallLogController::class, 'updateNextStep'])->name('admin.call-logs.update-next-step');
    
    // Admin Permissions Management
    // New simplified permission system
    Route::get('/user-permissions', [UserPermissionController::class, 'index'])->name('admin.user-permissions.index')->middleware('admin.permission:admin_permissions,view');
    Route::get('/user-permissions/{user}/edit', [UserPermissionController::class, 'edit'])->name('admin.user-permissions.edit')->middleware('admin.permission:admin_permissions,edit');
    Route::put('/user-permissions/{user}', [UserPermissionController::class, 'update'])->name('admin.user-permissions.update')->middleware('admin.permission:admin_permissions,edit');
    Route::delete('/user-permissions/{user}/reset', [UserPermissionController::class, 'reset'])->name('admin.user-permissions.reset')->middleware('admin.permission:admin_permissions,delete');
    
    // Old complex permission system (keeping for now)
    Route::get('/permissions', [App\Http\Controllers\AdminPermissionController::class, 'index'])->name('admin.permissions.index')->middleware('admin.permission:admin_permissions,view');
    Route::get('/permissions/{user}/edit', [App\Http\Controllers\AdminPermissionController::class, 'edit'])->name('admin.permissions.edit')->middleware('admin.permission:admin_permissions,edit');
    Route::put('/permissions/{user}', [App\Http\Controllers\AdminPermissionController::class, 'update'])->name('admin.permissions.update')->middleware('admin.permission:admin_permissions,edit');
    Route::delete('/permissions/{user}/reset', [App\Http\Controllers\AdminPermissionController::class, 'reset'])->name('admin.permissions.reset')->middleware('admin.permission:admin_permissions,delete');
    
    // Resource routes after specific routes
    Route::resource('call-logs', CallLogController::class)->names([
        'index' => 'admin.call-logs.index',
        'create' => 'admin.call-logs.create',
        'store' => 'admin.call-logs.store',
        'show' => 'admin.call-logs.show',
        'edit' => 'admin.call-logs.edit',
        'update' => 'admin.call-logs.update',
        'destroy' => 'admin.call-logs.destroy'
    ]);
    
    // Cash Document Management Routes (Merged with Rental Codes)
    Route::get('/cash-documents', [RentalCodeCashDocumentController::class, 'index'])->name('rental-codes.cash-documents.index');
    Route::get('/rental-codes/{rentalCode}/cash-documents/create', [RentalCodeCashDocumentController::class, 'create'])->name('rental-codes.cash-documents.create');
    Route::post('/rental-codes/{rentalCode}/cash-documents', [RentalCodeCashDocumentController::class, 'store'])->name('rental-codes.cash-documents.store');
    Route::get('/rental-codes/{rentalCode}/cash-documents', [RentalCodeCashDocumentController::class, 'show'])->name('rental-codes.cash-documents.show');
    Route::get('/rental-codes/{rentalCode}/cash-documents/edit', [RentalCodeCashDocumentController::class, 'edit'])->name('rental-codes.cash-documents.edit');
    Route::put('/rental-codes/{rentalCode}/cash-documents', [RentalCodeCashDocumentController::class, 'update'])->name('rental-codes.cash-documents.update');
    Route::delete('/rental-codes/{rentalCode}/cash-documents', [RentalCodeCashDocumentController::class, 'destroy'])->name('rental-codes.cash-documents.destroy');
    Route::post('/rental-codes/{rentalCode}/cash-documents/approve', [RentalCodeCashDocumentController::class, 'approve'])->name('rental-codes.cash-documents.approve');
    Route::post('/rental-codes/{rentalCode}/cash-documents/reject', [RentalCodeCashDocumentController::class, 'reject'])->name('rental-codes.cash-documents.reject');
    
    // Legacy cash-documents routes (redirect to new structure)
    Route::get('/cash-documents/create', function () {
        return redirect()->route('rental-codes.cash-documents.index');
    })->name('cash-documents.create');
});

// Profile routes - require authentication
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Agent Profile Routes
    Route::prefix('agent')->name('agent.profile.')->group(function () {
        Route::get('/dashboard', [AgentProfileController::class, 'dashboard'])->name('dashboard');
        Route::get('/rental-codes', [AgentProfileController::class, 'rentalCodes'])->name('rental-codes');
        Route::get('/earnings', [AgentProfileController::class, 'earnings'])->name('earnings');
        Route::get('/deductions', [AgentProfileController::class, 'deductions'])->name('deductions');
        Route::get('/clients', [AgentProfileController::class, 'clients'])->name('clients');
    });
    
        // Scraper Routes
        Route::get('/scraper', [ScraperController::class, 'index'])->name('admin.scraper.index');
        Route::post('/scraper/add-profile', [ScraperController::class, 'addProfile'])->name('admin.scraper.add-profile');
        Route::post('/scraper/remove-profile', [ScraperController::class, 'removeProfile'])->name('admin.scraper.remove-profile');
        Route::post('/scraper/run', [ScraperController::class, 'runScraper'])->name('admin.scraper.run');
        Route::post('/scraper/run-php', [PhpScraperController::class, 'runPhpScraper'])->name('admin.scraper.run-php');
        Route::post('/scraper/import', [ScraperController::class, 'importData'])->name('admin.scraper.import');
});

// Logout route
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/properties');
})->middleware('auth')->name('logout');
