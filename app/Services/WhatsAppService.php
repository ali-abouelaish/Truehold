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
    protected $rentalTemplateSid;

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
            $this->rentalTemplateSid = config('services.twilio.rental_template_sid');
            $this->messagingServiceSid = config('services.twilio.messaging_service_sid');
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
        // Prefer template message if configured
        if ($this->rentalTemplateSid && $client && $client->phone_number) {
            return $this->sendTemplateMessage($client->phone_number, $this->rentalTemplateSid, $this->buildTemplateVariables($rentalCode, $client));
        }

        // Fallback to plain text message
        $message = $this->buildRentalCodeMessage($rentalCode, $client);
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
     * Send a template-based WhatsApp message
     */
    private function sendTemplateMessage($to, $templateSid, array $templateVariables = [])
    {
        if (!$this->client) {
            return ['success' => false, 'error' => 'Twilio client not initialized'];
        }
        $formattedTo = $this->formatPhoneNumber($to);
        if (!str_starts_with($formattedTo, 'whatsapp:')) {
            $formattedTo = 'whatsapp:' . $formattedTo;
        }
        try {
            $params = [
                'contentSid' => $templateSid,
            ];
            // Prefer Messaging Service if configured; else use From number
            if (!empty($this->messagingServiceSid)) {
                $params['messagingServiceSid'] = $this->messagingServiceSid;
            } else {
                $params['from'] = $this->whatsappNumber;
            }
            // Optional: content language
            $contentLanguage = config('services.twilio.content_language');
            if (!empty($contentLanguage)) {
                $params['contentLanguage'] = $contentLanguage;
            }
            if (!empty($templateVariables)) {
                // Only include the exact variable names your template defines
                $allowedKeys = ['rental_code','rentalcode_details','clientprofile','agent','marketing_agent'];
                $payload = [];
                foreach ($allowedKeys as $key) {
                    if (array_key_exists($key, $templateVariables)) {
                        $payload[$key] = (string) $templateVariables[$key];
                    }
                }
                $params['contentVariables'] = json_encode($payload);
                Log::info('Twilio contentVariables payload', ['payload' => $params['contentVariables']]);
            }
            $messageObj = $this->client->messages->create($formattedTo, $params);
            return ['success' => true, 'sid' => $messageObj->sid];
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp template', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Build variables object for Twilio content template
     */
    private function buildTemplateVariables($rentalCode, $client)
    {
        $createdDate = $rentalCode->created_at->format('d/m/Y');
        $rentalDate = $rentalCode->rental_date ? \Carbon\Carbon::parse($rentalCode->rental_date)->format('d/m/Y') : $createdDate;
        $paymentMethod = $this->formatPaymentMethod($rentalCode->payment_method ?? '');
        $consultationFee = $rentalCode->consultation_fee ?? $rentalCode->rent_amount ?? '';
        if (is_numeric($consultationFee)) {
            $consultationFee = '£' . number_format($consultationFee, 0);
        }
        $agentName = $rentalCode->agent_name ?? $rentalCode->rent_by_agent ?? 'Agent';
        $marketingAgentName = $rentalCode->marketingAgentUser->name ?? ($rentalCode->marketing_agent_name ?? 'N/A');

        // Build composite text blocks
        $rentalDetails = "Rental Date: {$rentalDate}\n" .
                         "Consultation fee: {$consultationFee}\n" .
                         "Method of Payment: {$paymentMethod}\n" .
                         "Property: " . ($rentalCode->property_address ?? ($rentalCode->property ?? 'Not specified')) . "\n" .
                         "Licensor: " . ($rentalCode->licensor ?? 'Not specified');

        $clientProfile = '';
        if ($client) {
            $clientProfile .= "Full Name: " . ($client->full_name ?? 'N/A') . "\n";
            $clientProfile .= "Date of Birth: " . ($client->date_of_birth ? \Carbon\Carbon::parse($client->date_of_birth)->format('jS F Y') : 'Not provided') . "\n";
            $clientProfile .= "Phone Number: " . ($client->phone_number ?? 'N/A') . "\n";
            $clientProfile .= "Email: " . ($client->email ?? 'N/A') . "\n";
            $clientProfile .= "Nationality: " . ucfirst($client->nationality ?? 'Not provided') . "\n";
            $clientProfile .= "Current Address: " . ($client->current_address ?? 'N/A') . "\n";
            $clientProfile .= "Company/University: " . ($client->company_university_name ?? 'N/A') . "\n";
            $clientProfile .= "Position/Role: " . ($client->position_role ?? 'N/A');
        }

        // Sanitize values to meet Twilio Content constraints (no newlines/tabs, compressed spaces)
        $sanitize = function ($v) {
            if ($v === null) return 'N/A';
            $v = (string) $v;
            $v = str_replace(["\r", "\n", "\t"], ' ', $v);
            $v = preg_replace('/\s{2,}/', ' ', $v);
            $v = trim($v);
            return $v === '' ? 'N/A' : $v;
        };

        // Provide exactly the named variables used in your template (sanitized)
        $variables = [
            'rental_code' => $sanitize($rentalCode->rental_code ?? 'N/A'),
            'rentalcode_details' => $sanitize($rentalDetails),
            'clientprofile' => $sanitize($clientProfile),
            'agent' => $sanitize($agentName),
            'marketing_agent' => $sanitize($marketingAgentName),
        ];

        return $variables;
    }

    /**
     * Public helper: send rental template to a specific number (used for testing)
     */
    public function sendRentalTemplateTo(string $to, $rentalCode, $client)
    {
        if ($this->rentalTemplateSid) {
            // Try named variables first
            $named = $this->buildTemplateVariables($rentalCode, $client);
            $result = $this->sendTemplateMessage($to, $this->rentalTemplateSid, $named);
            if (!$result['success'] && isset($result['error']) && (stripos($result['error'], 'Content Variables') !== false || stripos($result['error'], 'Channel did not accept') !== false)) {
                // Fallback to numeric placeholders 1..5
                $numeric = $this->buildTemplateVariablesNumeric($rentalCode, $client);
                Log::warning('Retrying Twilio send with numeric variables', ['numeric' => $numeric]);
                $retry = $this->sendTemplateMessage($to, $this->rentalTemplateSid, $numeric);
                if ($retry['success']) {
                    return $retry;
                }
            } else {
                return $result;
            }
        }
        // Fallback to plain text message to target number
        $message = $this->buildRentalCodeMessage($rentalCode, $client);
        return $this->sendMessage($to, $message);
    }

    /**
     * Public helper: send rental details as plain text (no template)
     */
    public function sendRentalPlainTo(string $to, $rentalCode, $client)
    {
        $message = $this->buildRentalCodeMessage($rentalCode, $client);
        return $this->sendMessage($to, $message);
    }

    /**
     * Public helper: send current template with explicit variables
     */
    public function sendTemplateWithVariables(string $to, array $variables)
    {
        if (!$this->rentalTemplateSid) {
            return ['success' => false, 'error' => 'Template SID not configured'];
        }
        return $this->sendTemplateMessage($to, $this->rentalTemplateSid, $variables);
    }

    /**
     * Build numeric variables mapping {1..5} in the order expected by the template
     */
    private function buildTemplateVariablesNumeric($rentalCode, $client)
    {
        $createdDate = $rentalCode->created_at->format('d/m/Y');
        $rentalDate = $rentalCode->rental_date ? \Carbon\Carbon::parse($rentalCode->rental_date)->format('d/m/Y') : $createdDate;
        $paymentMethod = $this->formatPaymentMethod($rentalCode->payment_method ?? '');
        $consultationFee = $rentalCode->consultation_fee ?? $rentalCode->rent_amount ?? '';
        if (is_numeric($consultationFee)) {
            $consultationFee = '£' . number_format($consultationFee, 0);
        }
        $agentName = $rentalCode->agent_name ?? $rentalCode->rent_by_agent ?? 'Agent';
        $marketingAgentName = $rentalCode->marketingAgentUser->name ?? ($rentalCode->marketing_agent_name ?? 'N/A');

        $rentalDetails = "Rental Date: {$rentalDate}  Consultation fee: {$consultationFee}  Method of Payment: {$paymentMethod}  Property: " . ($rentalCode->property_address ?? ($rentalCode->property ?? 'Not specified')) . "  Licensor: " . ($rentalCode->licensor ?? 'Not specified');

        $clientProfile = '';
        if ($client) {
            $clientProfile = "Full Name: " . ($client->full_name ?? 'N/A') . "  Date of Birth: " . ($client->date_of_birth ? \Carbon\Carbon::parse($client->date_of_birth)->format('jS F Y') : 'Not provided') . "  Phone Number: " . ($client->phone_number ?? 'N/A') . "  Email: " . ($client->email ?? 'N/A') . "  Nationality: " . ucfirst($client->nationality ?? 'Not provided') . "  Current Address: " . ($client->current_address ?? 'N/A') . "  Company/University: " . ($client->company_university_name ?? 'N/A') . "  Position/Role: " . ($client->position_role ?? 'N/A');
        }

        $sanitize = function ($v) {
            if ($v === null) return 'N/A';
            $v = (string) $v;
            $v = str_replace(["\r", "\n", "\t"], ' ', $v);
            $v = preg_replace('/\s{2,}/', ' ', $v);
            $v = trim($v);
            return $v === '' ? 'N/A' : $v;
        };

        return [
            '1' => $sanitize($rentalCode->rental_code ?? 'N/A'),
            '2' => $sanitize($rentalDetails),
            '3' => $sanitize($clientProfile),
            '4' => $sanitize($agentName),
            '5' => $sanitize($marketingAgentName),
        ];
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
