<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckEmailDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:email-delivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check email delivery and provide troubleshooting information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Email Delivery Troubleshooting');
        $this->newLine();
        
        // Check configuration
        $this->info('📧 Current Email Configuration:');
        $this->info('   Driver: ' . config('mail.default'));
        $this->info('   Host: ' . config('mail.mailers.smtp.host'));
        $this->info('   Port: ' . config('mail.mailers.smtp.port'));
        $this->info('   Username: ' . config('mail.mailers.smtp.username'));
        $this->info('   From: ' . config('mail.from.address'));
        $this->newLine();
        
        // Test with different recipients
        $testEmails = [
            'board@truehold.co.uk',
            'crm@truehold.co.uk', // Send to self
        ];
        
        foreach ($testEmails as $email) {
            $this->info("📤 Testing delivery to: {$email}");
            
            try {
                Mail::raw("Test email delivery check - " . now()->format('Y-m-d H:i:s'), function ($message) use ($email) {
                    $message->from('crm@truehold.co.uk', 'Truehold Group System')
                            ->to($email)
                            ->subject('Email Delivery Test - ' . now()->format('H:i:s'));
                });
                
                $this->info("   ✅ Email sent successfully to {$email}");
                
                // Log the attempt
                Log::info("Email delivery test sent to: {$email}");
                
            } catch (\Exception $e) {
                $this->error("   ❌ Failed to send to {$email}: " . $e->getMessage());
                Log::error("Email delivery test failed for {$email}: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info('🔧 Troubleshooting Tips:');
        $this->info('1. Check spam/junk folders in board@truehold.co.uk');
        $this->info('2. Verify board@truehold.co.uk email address is correct');
        $this->info('3. Check if Gmail account crm@truehold.co.uk has sending restrictions');
        $this->info('4. Verify the App Password is still valid');
        $this->info('5. Check Gmail account for any security alerts');
        
        $this->newLine();
        $this->info('📋 Next Steps:');
        $this->info('- Check the email logs: storage/logs/laravel.log');
        $this->info('- Try sending to a different email address');
        $this->info('- Verify the recipient email address exists');
    }
}
