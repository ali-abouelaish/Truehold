<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WasenderWebhookController;
use App\Http\Controllers\RentalWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// WasenderAPI Webhook Routes
Route::post('/wasender/webhook', [WasenderWebhookController::class, 'handle'])->name('wasender.webhook');

// Test endpoint (optional - can be removed in production or protected with middleware)
Route::get('/wasender/test', [WasenderWebhookController::class, 'test'])->name('wasender.test');

// Rental Code Webhook - sends new rental codes to WhatsApp group
Route::post('/rental-codes/notify-group', [RentalWebhookController::class, 'notifyGroup'])->name('rental.notify-group');
Route::get('/rental-codes/test-notify', [RentalWebhookController::class, 'test'])->name('rental.test-notify');

