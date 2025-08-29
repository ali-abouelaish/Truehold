<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return redirect('/properties');
});

// Public routes - no authentication required
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/map', [PropertyController::class, 'map'])->name('properties.map');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');

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
});

// Logout route
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/properties');
})->middleware('auth')->name('logout');
