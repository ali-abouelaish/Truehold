<?php

namespace App\Observers;

use App\Models\RentalCode;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RentalCodeObserver
{
    /**
     * Handle the RentalCode "created" event.
     */
    public function created(RentalCode $rentalCode): void
    {
        // Log the creation for debugging
        \Log::info('Rental code created', [
            'rental_code_id' => $rentalCode->id,
            'rental_code' => $rentalCode->rental_code,
            'consultation_fee' => $rentalCode->consultation_fee,
            'rent_by_agent' => $rentalCode->rent_by_agent,
            'marketing_agent_id' => $rentalCode->marketing_agent_id,
            'payment_method' => $rentalCode->payment_method
        ]);
        
        // Clear any earnings cache if it exists
        $this->clearEarningsCache();

        // Send WhatsApp notification using Twilio template if configured
        try {
            $client = $rentalCode->client ?? null;
            app(WhatsAppService::class)->sendRentalCodeNotification($rentalCode, $client);
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp on rental created', ['error' => $e->getMessage(), 'rental_code_id' => $rentalCode->id]);
        }

        // Send Zapier webhook with rental code details
        try {
            // Resolve related data if not loaded
            $rentalCode->loadMissing(['client', 'rentalAgent', 'marketingAgentUser']);

            $webhookUrl = config('services.zapier.zapier_webhook_url') ?: config('services.zapier.rental_code_webhook_url');
            if (empty($webhookUrl)) {
                Log::warning('Zapier webhook URL not configured. Skipping webhook send.');
                return;
            }

            $clientName = $rentalCode->client->full_name ?? ($rentalCode->client_full_name ?? null);
            $agentName = $rentalCode->rent_by_agent_name ?? ($rentalCode->agent_name ?? ($rentalCode->rent_by_agent ?? null));
            $marketingAgentName = $rentalCode->marketingAgentUser->name ?? ($rentalCode->marketing_agent_name ?? 'N/A');
            $propertyName = $rentalCode->property_address ?? ($rentalCode->property ?? null);
            $rentAmount = $rentalCode->rent_amount ?? $rentalCode->consultation_fee;

            // Compose detail blocks
            $createdDate = optional($rentalCode->created_at)?->format('d/m/Y');
            $rentalDate = $rentalCode->rental_date ? \Carbon\Carbon::parse($rentalCode->rental_date)->format('d/m/Y') : $createdDate;
            $paymentMethod = (string) ($rentalCode->payment_method ?? '');
            $licensor = $rentalCode->licensor ?? 'Not specified';
            $consultationFee = $rentAmount;
            if (is_numeric($consultationFee)) {
                $consultationFee = '£' . number_format((float) $consultationFee, 0);
            }

            $rentalDetails = "Rental Date: {$rentalDate}\n" .
                             "Consultation fee: {$consultationFee}\n" .
                             "Method of Payment: {$paymentMethod}\n" .
                             "Property: " . ($propertyName ?? 'Not specified') . "\n" .
                             "Licensor: {$licensor}";

            $clientProfile = '';
            if ($rentalCode->client) {
                $clientProfile .= 'Full Name: ' . ($rentalCode->client->full_name ?? 'N/A') . "\n";
                $clientProfile .= 'Date of Birth: ' . ($rentalCode->client->date_of_birth ? \Carbon\Carbon::parse($rentalCode->client->date_of_birth)->format('jS F Y') : 'Not provided') . "\n";
                $clientProfile .= 'Phone Number: ' . ($rentalCode->client->phone_number ?? 'N/A') . "\n";
                $clientProfile .= 'Email: ' . ($rentalCode->client->email ?? 'N/A') . "\n";
                $clientProfile .= 'Nationality: ' . ucfirst($rentalCode->client->nationality ?? 'Not provided') . "\n";
                $clientProfile .= 'Current Address: ' . ($rentalCode->client->current_address ?? 'N/A') . "\n";
                $clientProfile .= 'Company/University: ' . ($rentalCode->client->company_university_name ?? 'N/A') . "\n";
                $clientProfile .= 'Position/Role: ' . ($rentalCode->client->position_role ?? 'N/A');
            }

            $message = ($rentalCode->rental_code ?? 'N/A') . " number\n\n" .
                       $rentalDetails . "\n\n" .
                       "Client Profile:\n\n" .
                       $clientProfile . " user details\n\n" .
                       'Agent: ' . ($agentName ?? 'N/A') . "\n" .
                       'Marketing Agent: ' . ($marketingAgentName ?? 'N/A');

            $payload = [
                // Original fields
                'rental_code' => (string) ($rentalCode->rental_code ?? ''),
                'property_name' => $propertyName,
                'client_name' => $clientName,
                'agent_name' => $agentName,
                'rent_amount' => $rentAmount,
                'status' => (string) ($rentalCode->status ?? ''),
                'created_at' => optional($rentalCode->created_at)->toIso8601String(),
                // Requested template variables
                'rentalcode_details' => $rentalDetails,
                'clientprofile' => $clientProfile,
                'agent' => $agentName,
                'marketing_agent' => $marketingAgentName,
                // Composed message
                'message' => $message,
            ];

            $response = Http::asJson()->post($webhookUrl, $payload);

            if (!$response->successful()) {
                Log::error('Zapier webhook failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload,
                ]);
            } else {
                Log::info('Zapier webhook sent successfully for rental code', [
                    'rental_code_id' => $rentalCode->id,
                    'status' => $response->status(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Exception while sending Zapier webhook', [
                'error' => $e->getMessage(),
                'rental_code_id' => $rentalCode->id,
            ]);
        }
    }

    /**
     * Handle the RentalCode "updated" event.
     */
    public function updated(RentalCode $rentalCode): void
    {
        // Log the update for debugging
        \Log::info('Rental code updated', [
            'rental_code_id' => $rentalCode->id,
            'rental_code' => $rentalCode->rental_code,
            'consultation_fee' => $rentalCode->consultation_fee,
            'rent_by_agent' => $rentalCode->rent_by_agent,
            'marketing_agent_id' => $rentalCode->marketing_agent_id,
            'payment_method' => $rentalCode->payment_method,
            'paid' => $rentalCode->paid
        ]);
        
        // Clear any earnings cache if it exists
        $this->clearEarningsCache();

        // Send WhatsApp notification for update to admin
        try {
            $client = $rentalCode->client ?? null;
            app(WhatsAppService::class)->sendRentalCodeUpdateNotification($rentalCode, $client, 'updated');
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp on rental updated', ['error' => $e->getMessage(), 'rental_code_id' => $rentalCode->id]);
        }
    }

    /**
     * Handle the RentalCode "deleted" event.
     */
    public function deleted(RentalCode $rentalCode): void
    {
        // Log the deletion for debugging
        \Log::info('Rental code deleted', [
            'rental_code_id' => $rentalCode->id,
            'rental_code' => $rentalCode->rental_code
        ]);
        
        // Clear any earnings cache if it exists
        $this->clearEarningsCache();
    }

    /**
     * Handle the RentalCode "restored" event.
     */
    public function restored(RentalCode $rentalCode): void
    {
        //
    }

    /**
     * Handle the RentalCode "force deleted" event.
     */
    public function forceDeleted(RentalCode $rentalCode): void
    {
        // Log the force deletion for debugging
        \Log::info('Rental code force deleted', [
            'rental_code_id' => $rentalCode->id,
            'rental_code' => $rentalCode->rental_code
        ]);
        
        // Clear any earnings cache if it exists
        $this->clearEarningsCache();
    }
    
    /**
     * Clear earnings cache if it exists
     */
    private function clearEarningsCache(): void
    {
        // Clear any potential cache keys related to earnings
        $cacheKeys = [
            'agent_earnings_*',
            'rental_codes_earnings_*',
            'agent_analytics_*'
        ];
        
        foreach ($cacheKeys as $pattern) {
            if (function_exists('cache')) {
                // Clear cache by pattern if supported
                try {
                    cache()->forget($pattern);
                } catch (\Exception $e) {
                    // Cache driver might not support pattern clearing
                    \Log::info('Cache pattern clearing not supported', ['pattern' => $pattern]);
                }
            }
        }
        
        \Log::info('Earnings cache cleared after rental code change');
    }
}
