<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalCode;
use App\Models\Client;

class QueryRentalCodesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rental-codes:query {--limit=10 : Number of records to show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Query rental codes table with various examples';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        $this->info('=== Rental Codes Query Examples ===');
        $this->newLine();
        
        // 1. Get all rental codes
        $this->info('1. All Rental Codes:');
        $allCodes = RentalCode::limit($limit)->get();
        foreach ($allCodes as $code) {
            $this->line("   ID: {$code->id} | Code: {$code->rental_code} | Date: {$code->rental_date} | Status: {$code->status}");
        }
        $this->newLine();
        
        // 2. Get rental codes with client information
        $this->info('2. Rental Codes with Client Info:');
        $codesWithClients = RentalCode::with('client')->limit($limit)->get();
        foreach ($codesWithClients as $code) {
            $clientName = $code->client ? $code->client->full_name : 'No Client';
            $this->line("   Code: {$code->rental_code} | Client: {$clientName} | Fee: £{$code->consultation_fee}");
        }
        $this->newLine();
        
        // 3. Get latest rental code
        $this->info('3. Latest Rental Code:');
        $latestCode = RentalCode::orderBy('id', 'desc')->first();
        if ($latestCode) {
            $this->line("   Latest: {$latestCode->rental_code} (ID: {$latestCode->id})");
        } else {
            $this->line("   No rental codes found");
        }
        $this->newLine();
        
        // 4. Count total rental codes
        $this->info('4. Statistics:');
        $totalCodes = RentalCode::count();
        $completedCodes = RentalCode::where('status', 'completed')->count();
        $paidCodes = RentalCode::where('paid', true)->count();
        
        $this->line("   Total Codes: {$totalCodes}");
        $this->line("   Completed: {$completedCodes}");
        $this->line("   Paid: {$paidCodes}");
        $this->newLine();
        
        // 5. Search for specific code
        $this->info('5. Search for CC0120:');
        $specificCode = RentalCode::where('rental_code', 'CC0120')->first();
        if ($specificCode) {
            $this->line("   Found: {$specificCode->rental_code} | Status: {$specificCode->status} | Fee: £{$specificCode->consultation_fee}");
        } else {
            $this->line("   CC0120 not found");
        }
        $this->newLine();
        
        // 6. Get codes by date range
        $this->info('6. Recent Codes (Last 7 days):');
        $recentCodes = RentalCode::where('created_at', '>=', now()->subDays(7))->get();
        foreach ($recentCodes as $code) {
            $this->line("   {$code->rental_code} - {$code->created_at->format('Y-m-d H:i')}");
        }
        
        $this->newLine();
        $this->info('=== Query Examples ===');
        $this->line('// Get all rental codes');
        $this->line('$codes = RentalCode::all();');
        $this->newLine();
        
        $this->line('// Get latest rental code');
        $this->line('$latest = RentalCode::orderBy("id", "desc")->first();');
        $this->newLine();
        
        $this->line('// Search by rental code');
        $this->line('$code = RentalCode::where("rental_code", "CC0120")->first();');
        $this->newLine();
        
        $this->line('// Get codes with client info');
        $this->line('$codes = RentalCode::with("client")->get();');
        $this->newLine();
        
        $this->line('// Count total codes');
        $this->line('$count = RentalCode::count();');
        $this->newLine();
        
        $this->line('// Get completed codes');
        $this->line('$completed = RentalCode::where("status", "completed")->get();');
        
        return 0;
    }
}
