<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Http\Controllers\InvoiceController;
use Illuminate\Http\Request;

class TestInvoiceUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:invoice-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test invoice update functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing invoice update functionality...');
        
        try {
            // Get the first invoice
            $invoice = Invoice::first();
            
            if (!$invoice) {
                $this->error('No invoices found. Please create an invoice first.');
                return;
            }
            
            $this->info('Found invoice: ' . $invoice->invoice_number);
            $this->info('Current client name: ' . $invoice->client_name);
            
            // Test updating the invoice
            $originalName = $invoice->client_name;
            $newName = $originalName . ' (Updated)';
            
            $invoice->client_name = $newName;
            $invoice->save();
            
            // Refresh from database
            $invoice->refresh();
            
            if ($invoice->client_name === $newName) {
                $this->info('✅ Invoice update successful!');
                $this->info('Updated client name: ' . $invoice->client_name);
                
                // Revert the change
                $invoice->client_name = $originalName;
                $invoice->save();
                $this->info('Reverted changes for testing.');
            } else {
                $this->error('❌ Invoice update failed!');
                $this->error('Expected: ' . $newName);
                $this->error('Actual: ' . $invoice->client_name);
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error testing invoice update: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
