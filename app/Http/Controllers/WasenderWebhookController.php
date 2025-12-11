<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WasenderWebhookController extends Controller
{
    /**
     * Handle incoming webhook from WasenderAPI
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        try {
            // Log the incoming webhook payload for debugging
            Log::channel('daily')->info('WasenderAPI Webhook Received', [
                'payload' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Get the webhook data
            $data = $request->all();

            // Validate required structure
            if (!isset($data['key']) || !isset($data['data']['messages']['messageBody'])) {
                Log::warning('Invalid webhook structure received', ['data' => $data]);
                return response()->json([
                    'success' => true,
                    'message' => 'Webhook received but missing required fields'
                ], 200);
            }

            // Ignore messages sent by us (avoid loops)
            if (isset($data['key']['fromMe']) && $data['key']['fromMe'] === true) {
                Log::info('Ignoring message from self to avoid loop');
                return response()->json([
                    'success' => true,
                    'message' => 'Message from self ignored'
                ], 200);
            }

            // Extract sender information (try multiple fields)
            $sender = $this->extractSender($data['key']);
            
            if (empty($sender)) {
                Log::warning('Could not extract sender from webhook', ['key' => $data['key']]);
                return response()->json([
                    'success' => true,
                    'message' => 'Could not extract sender'
                ], 200);
            }

            // Extract message body
            $messageBody = $data['data']['messages']['messageBody'] ?? '';
            
            // Handle empty messages gracefully
            if (empty(trim($messageBody))) {
                Log::info('Empty message received, skipping forward', ['sender' => $sender]);
                return response()->json([
                    'success' => true,
                    'message' => 'Empty message skipped'
                ], 200);
            }

            // Build forwarded message text
            $forwardedText = $this->formatMessage($sender, $messageBody);

            // Forward message to group
            $result = $this->sendToGroup($forwardedText);

            if ($result['success']) {
                Log::info('Message forwarded successfully', [
                    'sender' => $sender,
                    'message_preview' => substr($messageBody, 0, 50)
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Message forwarded to group'
                ], 200);
            } else {
                Log::error('Failed to forward message to group', [
                    'sender' => $sender,
                    'error' => $result['error']
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to forward message',
                    'error' => $result['error']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error processing Wasender webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract sender from webhook key data
     *
     * @param array $key
     * @return string
     */
    protected function extractSender(array $key): string
    {
        // Try multiple fields in order of preference
        $senderFields = [
            'cleanedParticipantPn',
            'cleanedSenderPn',
            'remoteJid',
            'participant',
            'sender'
        ];

        foreach ($senderFields as $field) {
            if (isset($key[$field]) && !empty($key[$field])) {
                $sender = $key[$field];
                
                // Clean up the sender format (remove @s.whatsapp.net suffix if present)
                $sender = str_replace('@s.whatsapp.net', '', $sender);
                $sender = str_replace('@c.us', '', $sender);
                
                return $sender;
            }
        }

        return '';
    }

    /**
     * Format the message with sender information
     *
     * @param string $sender
     * @param string $message
     * @return string
     */
    protected function formatMessage(string $sender, string $message): string
    {
        // Format: "Sender: Message"
        // You can customize this format as needed
        return "📱 *{$sender}*:\n{$message}";
    }

    /**
     * Send message to WhatsApp group via WasenderAPI
     *
     * @param string $text
     * @return array
     */
    protected function sendToGroup(string $text): array
    {
        try {
            $apiKey = config('services.wasender.key');
            $baseUrl = config('services.wasender.base_url');
            $groupJid = config('services.wasender.group_jid');

            // Validate configuration
            if (empty($apiKey) || empty($baseUrl) || empty($groupJid)) {
                Log::error('Wasender configuration missing', [
                    'has_api_key' => !empty($apiKey),
                    'has_base_url' => !empty($baseUrl),
                    'has_group_jid' => !empty($groupJid)
                ]);

                return [
                    'success' => false,
                    'error' => 'Wasender configuration incomplete'
                ];
            }

            // Send message to group
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->timeout(30)->post($baseUrl . '/api/send-message', [
                'to' => $groupJid,
                'text' => $text
            ]);

            // Log the response for debugging
            Log::info('WasenderAPI send-message response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'API returned error: ' . $response->status(),
                    'body' => $response->body()
                ];
            }

        } catch (\Exception $e) {
            Log::error('Error sending message to group', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test endpoint to verify the bot is working
     * This can be called manually to test the group message sending
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function test()
    {
        $testMessage = "🤖 Bot Test Message\nThis is a test message from the WhatsApp bot.\nTimestamp: " . now()->toDateTimeString();
        
        $result = $this->sendToGroup($testMessage);
        
        return response()->json($result);
    }
}

