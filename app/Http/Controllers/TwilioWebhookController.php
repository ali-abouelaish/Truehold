<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\RentalCode;

class TwilioWebhookController extends Controller
{
    /**
     * Handle incoming Twilio webhooks for WhatsApp message status and replies.
     */
    public function handle(Request $request)
    {
        // ✅ Log the entire payload for debugging
        Log::channel('twilio')->info('Twilio Webhook Received', $request->all());

        $sid = $request->input('MessageSid');
        $status = $request->input('MessageStatus');
        $from = $request->input('From');
        $to = $request->input('To');
        $body = $request->input('Body');
        $direction = $request->input('Direction');

        // 🧩 Handle message status updates (outbound)
        if ($status) {
            Log::info("📦 Message [$sid] status: $status");

            // Example: update message status in your DB
            // Message::where('twilio_sid', $sid)->update(['status' => $status]);
        }

        // 💬 Handle inbound WhatsApp messages (replies)
        if ($direction === 'inbound' || str_starts_with($from, 'whatsapp:')) {
            Log::info("💬 Incoming WhatsApp message from {$from}: {$body}");

            // If user asks to unsubscribe/stop, skip Zapier send
            try {
                if (is_string($body) && preg_match('/\b(unsubscribe|stop|cancel|opt\s*out|remove\s*me)\b/i', $body)) {
                    Log::info('Skipping Zapier send due to unsubscribe keyword', [
                        'from' => $from,
                        'body' => $body,
                    ]);
                    return response('OK', 200);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed unsubscribe keyword check', ['error' => $e->getMessage()]);
            }

            // Example: store reply in DB
            // IncomingMessage::create([
            //     'from' => $from,
            //     'to' => $to,
            //     'body' => $body,
            //     'twilio_sid' => $sid,
            // ]);

            // Build full rental details like TestZapierWebhook and send to Zapier
            try {
                $webhookUrl = config('services.zapier.zapier_webhook_url') ?: config('services.zapier.rental_code_webhook_url');
                if (empty($webhookUrl)) {
                    Log::warning('Zapier webhook URL not configured. Skipping webhook send from TwilioWebhookController.');
                } else {
                    // Try to extract a rental code from the incoming message body (e.g., CC0168)
                    $matchedCode = null;
                    if (is_string($body) && preg_match('/\b[A-Z]{2}\d{4}\b/', $body, $m)) {
                        $matchedCode = $m[0];
                    }

                    // Locate the rental code record
                    $rentalCode = null;
                    if ($matchedCode) {
                        $rentalCode = RentalCode::where('rental_code', $matchedCode)->first();
                    }
                    if (!$rentalCode) {
                        $rentalCode = RentalCode::latest('id')->first();
                    }

                    if (!$rentalCode) {
                        Log::warning('No RentalCode found to compose Zapier payload from Twilio webhook.');
                    } else {
                        // Ensure relations are available
                        $rentalCode->loadMissing(['client', 'rentalAgent', 'marketingAgentUser']);

                        $clientName = $rentalCode->client->full_name ?? ($rentalCode->client_full_name ?? null);
                        $agentName = $rentalCode->rent_by_agent_name ?: ($rentalCode->agent_name ?? ($rentalCode->rent_by_agent ?? null));
                        $marketingAgentName = $rentalCode->marketingAgentUser->name ?? ($rentalCode->marketing_agent_name ?? 'N/A');
                        $propertyName = $rentalCode->property_address ?? ($rentalCode->property ?? null);
                        $rentAmount = $rentalCode->rent_amount ?? $rentalCode->consultation_fee;

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
                            'rental_code' => (string) ($rentalCode->rental_code ?? ''),
                            'property_name' => $propertyName,
                            'client_name' => $clientName,
                            'agent_name' => $agentName,
                            'rent_amount' => $rentAmount,
                            'status' => (string) ($rentalCode->status ?? ''),
                            'created_at' => optional($rentalCode->created_at)->toIso8601String(),
                            'rentalcode_details' => $rentalDetails,
                            'clientprofile' => $clientProfile,
                            'agent' => $agentName,
                            'marketing_agent' => $marketingAgentName,
                            'message' => $message,
                        ];

                        Log::info('Sending Zapier webhook from TwilioWebhookController', [
                            'url' => $webhookUrl,
                            'payload' => $payload,
                        ]);

                        $response = Http::asJson()->post($webhookUrl, $payload);
                        if ($response->successful()) {
                            Log::info('Zapier webhook sent successfully from TwilioWebhookController', [
                                'status' => $response->status(),
                            ]);
                        } else {
                            Log::error('Zapier webhook failed from TwilioWebhookController', [
                                'status' => $response->status(),
                                'body' => $response->body(),
                            ]);
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Exception while composing/sending Zapier webhook from TwilioWebhookController', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // ✅ Always respond with 200 OK so Twilio doesn’t retry
        return response('OK', 200);
    }
}
