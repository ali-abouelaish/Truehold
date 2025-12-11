# Test script for Rental Code -> WhatsApp Group webhook
# Run this in PowerShell

# Get ngrok URL from the user
Write-Host "Getting ngrok URL..." -ForegroundColor Cyan
Write-Host ""
Write-Host "Please enter your ngrok URL (e.g., https://abc123.ngrok-free.app):" -ForegroundColor Yellow
$ngrokUrl = Read-Host

if ([string]::IsNullOrWhiteSpace($ngrokUrl)) {
    Write-Host "No URL provided. Exiting." -ForegroundColor Red
    exit 1
}

# Extract base URL (remove any paths like /properties)
if ($ngrokUrl -match '^(https?://[^/]+)') {
    $ngrokUrl = $matches[1]
}

# Remove trailing slash if present
$ngrokUrl = $ngrokUrl.TrimEnd('/')

$webhookUrl = "$ngrokUrl/api/rental-codes/notify-group"

Write-Host ""
Write-Host "Sending test rental code to: $webhookUrl" -ForegroundColor Cyan
Write-Host ""

# Sample rental code data
$rentalDetails = "Rental Date: 11/12/2025`nConsultation fee: £1,500`nMethod of Payment: Bank Transfer`nProperty: 123 Test Street, London, SW1A 1AA`nLicensor: Test Landlord Ltd"

$clientProfile = "Full Name: John Test Smith`nDate of Birth: 15th January 1990`nPhone Number: +44 7700 900123`nEmail: john.test@example.com`nNationality: British`nCurrent Address: 456 Current St, London`nCompany/University: Test Company Ltd`nPosition/Role: Software Engineer"

$testData = @{
    rental_code = "CC9999"
    rentalcode_details = $rentalDetails
    clientprofile = $clientProfile
    agent = "Test Agent"
    marketing_agent = "Test Marketing Agent"
    property_name = "123 Test Street, London"
    client_name = "John Test Smith"
    rent_amount = 1500
    status = "pending"
}

$jsonData = $testData | ConvertTo-Json

Write-Host "Payload:" -ForegroundColor Yellow
Write-Host $jsonData
Write-Host ""

try {
    $response = Invoke-RestMethod -Uri $webhookUrl -Method Post -Body $jsonData -ContentType "application/json" -ErrorAction Stop
    
    Write-Host "SUCCESS!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Response:" -ForegroundColor Cyan
    $response | ConvertTo-Json -Depth 10 | Write-Host
    Write-Host ""
    Write-Host "Check your WhatsApp group for the test message!" -ForegroundColor Green
} catch {
    Write-Host "ERROR!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Status Code: $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    
    if ($_.ErrorDetails.Message) {
        Write-Host ""
        Write-Host "Details:" -ForegroundColor Yellow
        Write-Host $_.ErrorDetails.Message
    }
}

Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown')
