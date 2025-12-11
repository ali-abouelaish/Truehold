#!/bin/bash

# Test script for Rental Code → WhatsApp Group webhook
# Run this in Bash/Git Bash

echo "🔗 Getting ngrok URL..."
echo ""
echo "Please enter your ngrok URL (e.g., https://abc123.ngrok-free.app):"
read NGROK_URL

if [ -z "$NGROK_URL" ]; then
    echo "❌ No URL provided. Exiting."
    exit 1
fi

# Remove trailing slash if present
NGROK_URL="${NGROK_URL%/}"

WEBHOOK_URL="$NGROK_URL/api/rental-codes/notify-group"

echo ""
echo "📤 Sending test rental code to: $WEBHOOK_URL"
echo ""

# Sample rental code data
curl -X POST "$WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -d '{
    "rental_code": "CC9999",
    "rentalcode_details": "Rental Date: 11/12/2025\nConsultation fee: £1,500\nMethod of Payment: Bank Transfer\nProperty: 123 Test Street, London, SW1A 1AA\nLicensor: Test Landlord Ltd",
    "clientprofile": "Full Name: John Test Smith\nDate of Birth: 15th January 1990\nPhone Number: +44 7700 900123\nEmail: john.test@example.com\nNationality: British\nCurrent Address: 456 Current St, London\nCompany/University: Test Company Ltd\nPosition/Role: Software Engineer",
    "agent": "Test Agent",
    "marketing_agent": "Test Marketing Agent",
    "property_name": "123 Test Street, London",
    "client_name": "John Test Smith",
    "rent_amount": 1500,
    "status": "pending"
  }'

echo ""
echo ""
echo "✅ Check your WhatsApp group for the test message! 📱"

