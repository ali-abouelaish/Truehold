<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class RentalWebhookController extends Controller
{
    /**
     * Handle incoming webhook when a rental code is created.
     * This will send the rental details to the WhatsApp group.
     */
    public function notifyGroup(Request $request)
    {
        try {
            Log::info('Rental webhook received', $request->all());

            // Extract rental data from the request
            $rentalCode = $request->input('rental_code', 'N/A');
            $rentalDetails = $request->input('rentalcode_details', '');
            $clientProfile = $request->input('clientprofile', '');
            $agentName = $request->input('agent', 'N/A');
            $marketingAgent = $request->input('marketing_agent', 'N/A');

            // Build the message (similar format to your Twilio webhook)
            $message = "*New Rental Created* 🏠\n\n";
            $message .= "*Rental Code:* {$rentalCode}\n\n";
            
            if ($rentalDetails) {
                $message .= "*Rental Details:*\n{$rentalDetails}\n\n";
            }
            
            if ($clientProfile) {
                $message .= "*Client Profile:*\n{$clientProfile}\n\n";
            }
            
            $message .= "*Agent:* {$agentName}\n";
            $message .= "*Marketing Agent:* {$marketingAgent}";

            // Send to WhatsApp group
            $result = $this->sendToGroup($message);

            if ($result['success']) {
                Log::info('Rental notification sent to WhatsApp group', [
                    'rental_code' => $rentalCode,
                    'response' => $result['data'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Rental notification sent to WhatsApp group',
                    'rental_code' => $rentalCode,
                ], 200);
            } else {
                Log::error('Failed to send rental notification to WhatsApp group', [
                    'rental_code' => $rentalCode,
                    'error' => $result['error'],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send to WhatsApp group',
                    'error' => $result['error'],
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception in rental webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a message to the WhatsApp group via WasenderAPI.
     */
    private function sendToGroup(string $text): array
    {
        $apiKey = config('services.wasender.key');
        $baseUrl = config('services.wasender.base_url');
        $groupJid = config('services.wasender.group_jid');

        if (!$apiKey || !$groupJid) {
            return [
                'success' => false,
                'error' => 'Missing WasenderAPI configuration (API key or Group JID)',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['x-api-key' => $apiKey])
                ->post("{$baseUrl}/send-message", [
                    'recipient' => $groupJid,
                    'message'   => $text,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            } else {
                return [
                    'success' => false,
                    'error' => "HTTP {$response->status()}: {$response->body()}",
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Test endpoint to verify webhook is working.
     */
    public function test(Request $request)
    {
        $testMessage = "*Test Message* ✅\n\n";
        $testMessage .= "This is a test from your Property Scraper App.\n";
        $testMessage .= "Time: " . now()->format('d/m/Y H:i:s');

        $result = $this->sendToGroup($testMessage);

        return response()->json([
            'message' => 'Test endpoint reached',
            'wasender_result' => $result,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

