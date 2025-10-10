<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing email configuration...');
        
        // Display current mail configuration
        $this->info('Mail Driver: ' . config('mail.default'));
        $this->info('Mail Host: ' . config('mail.mailers.smtp.host'));
        $this->info('Mail Port: ' . config('mail.mailers.smtp.port'));
        $this->info('Mail Username: ' . config('mail.mailers.smtp.username'));
        $this->info('Mail From: ' . config('mail.from.address'));
        
        try {
            // Send a test email using Laravel's Mail facade
            Mail::raw('This is a test email from Truehold Group System.', function ($message) {
                $message->from('crm@truehold.co.uk', 'Truehold Group System')
                        ->to('board@truehold.co.uk')
                        ->subject('Test Email from Truehold System');
            });
            
            $this->info('✅ Test email sent successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
            
            // Try alternative approach with different mailer
            $this->info('Trying alternative authentication method...');
            try {
                Mail::mailer('smtp')->raw('Alternative test email', function ($message) {
                    $message->from('crm@truehold.co.uk', 'Truehold Group System')
                            ->to('board@truehold.co.uk')
                            ->subject('Alternative Test Email');
                });
                $this->info('✅ Alternative method worked!');
            } catch (\Exception $e2) {
                $this->error('❌ Alternative method also failed: ' . $e2->getMessage());
                
                // Show detailed error information
                $this->error('Detailed error: ' . $e2->getMessage());
                if (strpos($e2->getMessage(), '535') !== false) {
                    $this->error('🔑 Gmail Authentication Issue:');
                    $this->error('   - Check if the App Password is correct');
                    $this->error('   - Verify 2-Factor Authentication is enabled');
                    $this->error('   - Generate a new App Password if needed');
                }
            }
        }
    }
}
