# WhatsApp Bot Webhook Test Script (PowerShell)
# Usage: .\test-webhook.ps1 [webhook-url]
# Example: .\test-webhook.ps1 http://localhost:8000/api/wasender/webhook

param(
    [string]$WebhookUrl = "http://localhost:8000/api/wasender/webhook"
)

Write-Host "🧪 Testing WhatsApp Bot Webhook" -ForegroundColor Cyan
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host "Webhook URL: $WebhookUrl" -ForegroundColor Yellow
Write-Host ""

Write-Host "📤 Sending test webhook payload..." -ForegroundColor Cyan
Write-Host ""

$timestamp = [DateTimeOffset]::UtcNow.ToUnixTimeSeconds()
$datetime = Get-Date -Format "yyyy-MM-dd HH:mm:ss"

$payload = @{
    key = @{
        fromMe = $false
        cleanedParticipantPn = "1234567890"
        cleanedSenderPn = "1234567890"
        remoteJid = "1234567890@s.whatsapp.net"
        id = "3EB0C42F8F4A1234567890ABCDEF"
    }
    data = @{
        messages = @{
            messageBody = "Test message from webhook test script! Timestamp: $datetime"
        }
    }
    messageTimestamp = $timestamp
} | ConvertTo-Json -Depth 10

try {
    $response = Invoke-WebRequest -Uri $WebhookUrl `
        -Method POST `
        -ContentType "application/json" `
        -Body $payload `
        -UseBasicParsing

    Write-Host "📥 Response:" -ForegroundColor Cyan
    Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
    Write-Host "Status Code: $($response.StatusCode)" -ForegroundColor Green
    Write-Host ""
    Write-Host "Response Body:" -ForegroundColor Cyan
    
    try {
        $jsonResponse = $response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10
        Write-Host $jsonResponse -ForegroundColor White
    } catch {
        Write-Host $response.Content -ForegroundColor White
    }
    
    Write-Host ""
    
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Success! Check your WhatsApp group for the forwarded message." -ForegroundColor Green
    } else {
        Write-Host "⚠️  Warning! HTTP Status: $($response.StatusCode)" -ForegroundColor Yellow
    }
    
} catch {
    Write-Host "📥 Response:" -ForegroundColor Cyan
    Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
    Write-Host "❌ Error! $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    
    if ($_.Exception.Response) {
        $statusCode = [int]$_.Exception.Response.StatusCode
        Write-Host "Status Code: $statusCode" -ForegroundColor Red
        
        $stream = $_.Exception.Response.GetResponseStream()
        $reader = New-Object System.IO.StreamReader($stream)
        $responseBody = $reader.ReadToEnd()
        
        Write-Host "Response Body:" -ForegroundColor Cyan
        try {
            $jsonError = $responseBody | ConvertFrom-Json | ConvertTo-Json -Depth 10
            Write-Host $jsonError -ForegroundColor White
        } catch {
            Write-Host $responseBody -ForegroundColor White
        }
    }
    
    Write-Host ""
    Write-Host "Check Laravel logs: " -NoNewline -ForegroundColor Yellow
    Write-Host "tail -f storage/logs/laravel.log" -ForegroundColor White
}

Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host ""
Write-Host "💡 Tip: To test with a custom message, edit this script and change the messageBody field." -ForegroundColor Cyan

