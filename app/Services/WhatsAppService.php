<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class WhatsAppService
{
    protected $client;
    protected $whatsappNumber;
    protected $messagingServiceSid;
    protected $adminNumber;

    public function __construct()
    {
        try {
            $this->whatsappNumber = config('services.twilio.whatsapp_number');
            $this->adminNumber = config('services.twilio.admin_whatsapp_number');
            
            if (config('services.twilio.account_sid') && config('services.twilio.auth_token')) {
                $this->client = new Client(
                    config('services.twilio.account_sid'),
                    config('services.twilio.auth_token')
                );
            } else {
                $this->client = null;
                Log::warning('Twilio credentials not configured');
            }
        } catch (\Exception $e) {
            Log::error('Failed to initialize Twilio client', ['error' => $e->getMessage()]);
            $this->client = null;
        }
    }

    /**
     * Send a WhatsApp message
     */
    public function sendMessage($to, $message)
    {
        if (!$this->client) {
            Log::error('Twilio client not initialized');
            return [
                'success' => false,
                'error' => 'Twilio client not initialized'
            ];
        }

        if (!$this->whatsappNumber) {
            Log::error('WhatsApp number not configured');
            return [
                'success' => false,
                'error' => 'WhatsApp number not configured'
            ];
        }

        try {
            // Format the phone number
            $formattedTo = $this->formatPhoneNumber($to);
            
            // Ensure the number has whatsapp: prefix
            if (!str_starts_with($formattedTo, 'whatsapp:')) {
                $formattedTo = 'whatsapp:' . $formattedTo;
            }

            Log::info('Sending WhatsApp message', [
                'to' => $formattedTo,
                'from' => $this->whatsappNumber,
                'message_length' => strlen($message)
            ]);

            $messageObj = $this->client->messages->create(
                $formattedTo,
                [
                    'from' => $this->whatsappNumber,
                    'body' => $message
                ]
            );

            Log::info('WhatsApp message sent successfully', [
                'sid' => $messageObj->sid,
                'to' => $formattedTo
            ]);

            return [
                'success' => true,
                'sid' => $messageObj->sid,
                'to' => $formattedTo
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp message', [
                'error' => $e->getMessage(),
                'to' => $to,
                'formatted_to' => $formattedTo ?? 'not_formatted'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send rental code notification via WhatsApp
     */
    public function sendRentalCodeNotification($rentalCode, $client)
    {
        $message = $this->buildRentalCodeMessage($rentalCode, $client);
        
        // Send to client if they have a phone number
        if ($client && $client->phone_number) {
            return $this->sendMessage($client->phone_number, $message);
        }

        return [
            'success' => false,
            'error' => 'Client phone number not available'
        ];
    }

    /**
     * Build the rental code message
     */
    private function buildRentalCodeMessage($rentalCode, $client)
    {
        $clientName = $client ? $client->full_name : 'Client';
        $rentalCodeValue = $rentalCode->rental_code;
        $agentName = $rentalCode->agent_name ?? $rentalCode->rent_by_agent ?? 'Agent';
        $createdDate = $rentalCode->created_at->format('d/m/Y');
        
        // Format rental date
        $rentalDate = $rentalCode->rental_date ? 
            \Carbon\Carbon::parse($rentalCode->rental_date)->format('d/m/Y') : 
            $createdDate;
        
        // Format payment method
        $paymentMethod = $this->formatPaymentMethod($rentalCode->payment_method ?? '');
        
        // Get property address
        $propertyAddress = $rentalCode->property_address ?? 'Not specified';
        
        // Get licensor
        $licensor = $rentalCode->licensor ?? 'Not specified';
        
        // Get consultation fee
        $consultationFee = $rentalCode->consultation_fee ?? $rentalCode->rent_amount ?? 'Not specified';
        if (is_numeric($consultationFee)) {
            $consultationFee = '£' . number_format($consultationFee, 0);
        }
        
        $message = "🏠 *RENTAL CODE: {$rentalCodeValue}*\n";
        $message .= "_\n";
        $message .= "Rental Date: {$rentalDate}\n";
        $message .= "Consultation fee: {$consultationFee}\n";
        $message .= "Method of Payment: {$paymentMethod}\n";
        $message .= "Property: {$propertyAddress}\n";
        $message .= "Licensor: {$licensor}\n\n";
        
        if ($client) {
            $message .= "*Full Name:* {$client->full_name}\n";
            $message .= "*Date of Birth:* " . ($client->date_of_birth ? 
                \Carbon\Carbon::parse($client->date_of_birth)->format('jS F Y') : 'Not provided') . "\n";
            $message .= "*Phone Number:* {$client->phone_number}\n";
            $message .= "*Email:* {$client->email}\n";
            $message .= "*Nationality:* " . ucfirst($client->nationality ?? 'Not provided') . "\n";
            $message .= "*Current Address:* {$client->current_address}\n";
            $message .= "*Company/University:* {$client->company_university_name}\n";
            $message .= "*Position/Role:* {$client->position_role}\n\n";
        }
        
        $message .= "*Agent:* {$agentName}\n";
        if ($rentalCode->marketingAgentUser) {
            $message .= "*Marketing Agent:* {$rentalCode->marketingAgentUser->name}\n";
        }
        
        $message .= "\n_This is an automated message sent from the CRM. Please contact agent to change any details._";

        return $message;
    }
    
    /**
     * Format payment method for display
     */
    private function formatPaymentMethod($paymentMethod)
    {
        $formattedMethods = [
            'bank_transfer' => 'Bank Transfer',
            'card_machine' => 'Card Machine',
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            'online_payment' => 'Online Payment'
        ];
        
        return $formattedMethods[$paymentMethod] ?? ucfirst(str_replace('_', ' ', $paymentMethod));
    }

    /**
     * Send rental code notification to admin/board
     */
    public function sendRentalCodeAdminNotification($rentalCode, $client)
    {
        $clientName = $client ? $client->full_name : 'Unknown Client';
        $rentalCodeValue = $rentalCode->rental_code;
        $agentName = $rentalCode->agent_name ?? $rentalCode->rent_by_agent ?? 'Unknown Agent';
        $createdDate = $rentalCode->created_at->format('d/m/Y H:i');
        
        // Format rental date
        $rentalDate = $rentalCode->rental_date ? 
            \Carbon\Carbon::parse($rentalCode->rental_date)->format('d/m/Y') : 
            $createdDate;
        
        // Format payment method
        $paymentMethod = $this->formatPaymentMethod($rentalCode->payment_method ?? '');
        
        // Get property address
        $propertyAddress = $rentalCode->property_address ?? 'Not specified';
        
        // Get licensor
        $licensor = $rentalCode->licensor ?? 'Not specified';
        
        // Get consultation fee
        $consultationFee = $rentalCode->consultation_fee ?? $rentalCode->rent_amount ?? 'Not specified';
        if (is_numeric($consultationFee)) {
            $consultationFee = '£' . number_format($consultationFee, 0);
        }
        
        $message = "🔔 *NEW RENTAL CODE GENERATED*\n\n";
        $message .= "🏠 *RENTAL CODE: {$rentalCodeValue}*\n";
        $message .= "_\n";
        $message .= "Rental Date: {$rentalDate}\n";
        $message .= "Consultation fee: {$consultationFee}\n";
        $message .= "Method of Payment: {$paymentMethod}\n";
        $message .= "Property: {$propertyAddress}\n";
        $message .= "Licensor: {$licensor}\n\n";
        
        if ($client) {
            $message .= "*Full Name:* {$client->full_name}\n";
            $message .= "*Date of Birth:* " . ($client->date_of_birth ? 
                \Carbon\Carbon::parse($client->date_of_birth)->format('jS F Y') : 'Not provided') . "\n";
            $message .= "*Phone Number:* {$client->phone_number}\n";
            $message .= "*Email:* {$client->email}\n";
            $message .= "*Nationality:* " . ucfirst($client->nationality ?? 'Not provided') . "\n";
            $message .= "*Current Address:* {$client->current_address}\n";
            $message .= "*Company/University:* {$client->company_university_name}\n";
            $message .= "*Position/Role:* {$client->position_role}\n\n";
        }
        
        $message .= "*Agent:* {$agentName}\n";
        if ($rentalCode->marketingAgentUser) {
            $message .= "*Marketing Agent:* {$rentalCode->marketingAgentUser->name}\n";
        }
        
        $message .= "\n📋 *Please review in the admin panel.*\n";
        $message .= "_This is an automated message sent from the CRM. Please contact agent to change any details._";

        // Send to admin number
        if ($this->adminNumber) {
            return $this->sendMessage($this->adminNumber, $message);
        }

        Log::error('Admin WhatsApp number not configured');
        return [
            'success' => false,
            'error' => 'Admin WhatsApp number not configured'
        ];
    }

    /**
     * Send rental code update notification to admin
     */
    public function sendRentalCodeUpdateNotification($rentalCode, $client, $action = 'updated')
    {
        $clientName = $client ? $client->full_name : 'Unknown Client';
        $rentalCodeValue = $rentalCode->rental_code;
        $agentName = $rentalCode->rent_by_agent ?? 'Unknown Agent';
        $updatedDate = $rentalCode->updated_at->format('d/m/Y H:i');
        
        // Format rental date
        $rentalDate = $rentalCode->rental_date ? 
            \Carbon\Carbon::parse($rentalCode->rental_date)->format('d/m/Y') : 
            $updatedDate;
        
        // Format payment method
        $paymentMethod = $this->formatPaymentMethod($rentalCode->payment_method ?? '');
        
        // Get property address
        $propertyAddress = $rentalCode->property ?? 'Not specified';
        
        // Get licensor
        $licensor = $rentalCode->licensor ?? 'Not specified';
        
        // Get consultation fee
        $consultationFee = $rentalCode->consultation_fee ?? 'Not specified';
        if (is_numeric($consultationFee)) {
            $consultationFee = '£' . number_format($consultationFee, 0);
        }
        
        $actionText = ucfirst($action);
        $message = "🔄 *RENTAL CODE " . strtoupper($actionText) . "*\n\n";
        $message .= "🏠 *RENTAL CODE: {$rentalCodeValue}*\n";
        $message .= "_\n";
        $message .= "Rental Date: {$rentalDate}\n";
        $message .= "Consultation fee: {$consultationFee}\n";
        $message .= "Method of Payment: {$paymentMethod}\n";
        $message .= "Property: {$propertyAddress}\n";
        $message .= "Licensor: {$licensor}\n\n";
        
        if ($client) {
            $message .= "*Full Name:* {$client->full_name}\n";
            $message .= "*Date of Birth:* " . ($client->date_of_birth ? 
                \Carbon\Carbon::parse($client->date_of_birth)->format('jS F Y') : 'Not provided') . "\n";
            $message .= "*Phone Number:* {$client->phone_number}\n";
            $message .= "*Email:* {$client->email}\n";
            $message .= "*Nationality:* " . ucfirst($client->nationality ?? 'Not provided') . "\n";
            $message .= "*Current Address:* {$client->current_address}\n";
            $message .= "*Company/University:* {$client->company_university_name}\n";
            $message .= "*Position/Role:* {$client->position_role}\n\n";
        }
        
        $message .= "*Agent:* {$agentName}\n";
        if ($rentalCode->marketingAgentUser) {
            $message .= "*Marketing Agent:* {$rentalCode->marketingAgentUser->name}\n";
        }
        
        $message .= "\n📋 *Please review the changes in the admin panel.*\n";
        $message .= "_This is an automated message sent from the CRM. Please contact agent to change any details._";

        // Send to admin number
        if ($this->adminNumber) {
            return $this->sendMessage($this->adminNumber, $message);
        }

        Log::error('Admin WhatsApp number not configured');
        return [
            'success' => false,
            'error' => 'Admin WhatsApp number not configured'
        ];
    }
    
    /**
     * Format phone number for WhatsApp
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Log original phone number
        Log::info('Formatting phone number', ['original' => $phoneNumber]);
        
        // Remove all non-numeric characters except +
        $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        Log::info('After regex cleanup', ['cleaned' => $phoneNumber]);
        
        // If it starts with +, keep it
        if (str_starts_with($phoneNumber, '+')) {
            Log::info('Phone number already has + prefix', ['formatted' => $phoneNumber]);
            return $phoneNumber;
        }
        
        // If it starts with 0, replace with +44
        if (str_starts_with($phoneNumber, '0')) {
            $formatted = '+44' . substr($phoneNumber, 1);
            Log::info('Converted 0 prefix to +44', ['formatted' => $formatted]);
            return $formatted;
        }
        
        // If it starts with 44, add +
        if (str_starts_with($phoneNumber, '44')) {
            $formatted = '+' . $phoneNumber;
            Log::info('Added + to 44 prefix', ['formatted' => $formatted]);
            return $formatted;
        }
        
        // If it's a UK number without country code, add +44
        if (strlen($phoneNumber) === 10 || strlen($phoneNumber) === 11) {
            $formatted = '+44' . $phoneNumber;
            Log::info('Added +44 to UK number', ['formatted' => $formatted]);
            return $formatted;
        }
        
        // Return as is if already formatted
        Log::info('Returning phone number as-is', ['formatted' => $phoneNumber]);
        return $phoneNumber;
    }
}
