<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\RentalCode;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;

class TestRentalCodeEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:rental-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test rental code email notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing rental code email notification...');
        
        try {
            // Create a test rental code (or use existing one)
            $rentalCode = RentalCode::with('client')->first();
            
            if (!$rentalCode) {
                $this->error('No rental codes found. Please create a rental code first.');
                return;
            }
            
            $agentName = $rentalCode->agent_name ?? $rentalCode->rent_by_agent ?? 'Test Agent';
            
            // Generate PDF
            $pdf = Pdf::loadView('admin.rental-codes.pdf', [
                'rentalCode' => $rentalCode,
                'agentName' => $agentName,
                'client' => $rentalCode->client
            ]);
            
            // Send test email with PDF attachment
            Mail::send('emails.rental-code-notification', [
                'rentalCode' => $rentalCode,
                'agentName' => $agentName,
                'client' => $rentalCode->client
            ], function ($message) use ($rentalCode, $agentName, $pdf) {
                $message->from('crm@truehold.co.uk', 'Truehold Group System')
                        ->to('board@truehold.co.uk')
                        ->subject('Test Rental Code Email - ' . $rentalCode->rental_code)
                        ->attachData($pdf->output(), "rental-code-{$rentalCode->rental_code}.pdf", [
                            'mime' => 'application/pdf',
                        ]);
            });
            
            $this->info('✅ Rental code test email sent successfully!');
            $this->info('Rental Code: ' . $rentalCode->rental_code);
            $this->info('Sent to: board@truehold.co.uk');
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send rental code email: ' . $e->getMessage());
        }
    }
}
