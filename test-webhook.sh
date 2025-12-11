#!/bin/bash

# WhatsApp Bot Webhook Test Script
# Usage: ./test-webhook.sh [webhook-url]
# Example: ./test-webhook.sh http://localhost:8000/api/wasender/webhook

WEBHOOK_URL="${1:-http://localhost:8000/api/wasender/webhook}"

echo "🧪 Testing WhatsApp Bot Webhook"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Webhook URL: $WEBHOOK_URL"
echo ""

echo "📤 Sending test webhook payload..."
echo ""

RESPONSE=$(curl -s -w "\nHTTP_STATUS:%{http_code}" -X POST "$WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -d '{
    "key": {
      "fromMe": false,
      "cleanedParticipantPn": "1234567890",
      "cleanedSenderPn": "1234567890",
      "remoteJid": "1234567890@s.whatsapp.net",
      "id": "3EB0C42F8F4A1234567890ABCDEF"
    },
    "data": {
      "messages": {
        "messageBody": "Test message from webhook test script! Timestamp: '$(date +%Y-%m-%d\ %H:%M:%S)'"
      }
    },
    "messageTimestamp": '$(date +%s)'
  }')

HTTP_BODY=$(echo "$RESPONSE" | sed -e 's/HTTP_STATUS\:.*//g')
HTTP_STATUS=$(echo "$RESPONSE" | tr -d '\n' | sed -e 's/.*HTTP_STATUS://')

echo "📥 Response:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Status Code: $HTTP_STATUS"
echo ""
echo "Response Body:"
echo "$HTTP_BODY" | jq '.' 2>/dev/null || echo "$HTTP_BODY"
echo ""

if [ "$HTTP_STATUS" -eq 200 ]; then
  echo "✅ Success! Check your WhatsApp group for the forwarded message."
else
  echo "❌ Error! HTTP Status: $HTTP_STATUS"
  echo "Check Laravel logs: tail -f storage/logs/laravel.log"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

