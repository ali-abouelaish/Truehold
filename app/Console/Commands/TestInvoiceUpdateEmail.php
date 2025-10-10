<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class TestInvoiceUpdateEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:invoice-update-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test invoice update email notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing invoice update email notification...');
        
        try {
            // Get the first invoice
            $invoice = Invoice::first();
            
            if (!$invoice) {
                $this->error('No invoices found. Please create an invoice first.');
                return;
            }
            
            $this->info('Found invoice: ' . $invoice->invoice_number);
            
            $agentName = $invoice->agent_name ?? 'Test Agent';
            
            // Generate PDF
            $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
            
            // Send test email
            Mail::send('emails.invoice-update-notification', [
                'invoice' => $invoice,
                'agentName' => $agentName
            ], function ($message) use ($invoice, $agentName, $pdf) {
                $message->from('crm@truehold.co.uk', 'Truehold Group System')
                        ->to('board@truehold.co.uk')
                        ->subject('Test Invoice Update Email - ' . $invoice->invoice_number)
                        ->attachData($pdf->output(), "invoice-{$invoice->invoice_number}.pdf", [
                            'mime' => 'application/pdf',
                        ]);
            });
            
            $this->info('✅ Invoice update test email sent successfully!');
            $this->info('Invoice: ' . $invoice->invoice_number);
            $this->info('Sent to: board@truehold.co.uk');
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send invoice update email: ' . $e->getMessage());
        }
    }
}
