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

Route::get('/', function () {
    return redirect('/properties');
});


// Public routes - no authentication required
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/map', [PropertyController::class, 'map'])->name('properties.map');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
Route::get('/rental-codes/agent-earnings', [RentalCodeController::class, 'agentEarnings'])->name('rental-codes.agent-earnings');
    
// Temporary public route for testing rental code generation
Route::get('/test-rental-code', function () {
    try {
        $lastRentalCode = \App\Models\RentalCode::orderBy('id', 'desc')->first();
        
        if (!$lastRentalCode) {
            $nextNumber = 1;
        } else {
            preg_match('/CC(\d+)/', $lastRentalCode->rental_code, $matches);
            $nextNumber = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
        }
        
        $newCode = 'CC' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        return response()->json(['code' => $newCode]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to generate rental code: ' . $e->getMessage()], 500);
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
    
    // Call Log Management Routes
    Route::resource('call-logs', CallLogController::class)->names([
        'index' => 'admin.call-logs.index',
        'create' => 'admin.call-logs.create',
        'store' => 'admin.call-logs.store',
        'show' => 'admin.call-logs.show',
        'edit' => 'admin.call-logs.edit',
        'update' => 'admin.call-logs.update',
        'destroy' => 'admin.call-logs.destroy'
    ]);
    Route::get('/call-logs/stats', [CallLogController::class, 'stats'])->name('admin.call-logs.stats');
    Route::get('/call-logs/follow-ups', [CallLogController::class, 'followUps'])->name('admin.call-logs.follow-ups');
    Route::get('/call-logs/recent', [CallLogController::class, 'recent'])->name('admin.call-logs.recent');
});

// Profile routes - require authentication
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Logout route
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/properties');
})->middleware('auth')->name('logout');
