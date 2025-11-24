Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

Write-Host "== Twilio Webhook end-to-end test ==" -ForegroundColor Cyan

# Move to project root (this script is expected at scripts/)
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location (Join-Path $scriptDir '..')

function Ensure-ZapierUrl {
    $envPath = ".env"
    if (-not (Test-Path $envPath)) {
        Write-Host ".env not found. Skipping Zapier URL setup." -ForegroundColor Yellow
        return
    }
    $content = Get-Content -LiteralPath $envPath -Raw
    if ($content -notmatch "(?m)^ZAPIER_WEBHOOK_URL=") {
        $defaultUrl = "https://httpbin.org/post"
        Add-Content -LiteralPath $envPath -Value "`nZAPIER_WEBHOOK_URL=$defaultUrl"
        Write-Host "Added ZAPIER_WEBHOOK_URL=$defaultUrl to .env" -ForegroundColor Green
    } else {
        Write-Host "ZAPIER_WEBHOOK_URL already set in .env" -ForegroundColor DarkGray
    }
}

function Clear-ConfigCache {
    Write-Host "Clearing Laravel config cache..." -ForegroundColor DarkGray
    php artisan config:clear | Out-Null
}

function Seed-RentalCode {
    $seedPhp = @'
<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RentalCode;

$code = 'CC0168';
$existing = RentalCode::where('rental_code', $code)->first();
if (!$existing) {
    $r = new RentalCode();
    $r->rental_code = $code;
    $r->rent_amount = 1200;
    $r->status = 'paid';
    $r->payment_method = 'cash';
    $r->property_address = '123 Test Street';
    $r->licensor = 'Test Licensor';
    $r->client_full_name = 'John Doe';
    $r->save();
    echo "Seeded RentalCode $code\n";
} else {
    echo "RentalCode $code already exists\n";
}
'@

    $tempFile = Join-Path $PWD "seed_rental_code.php"
    Set-Content -LiteralPath $tempFile -Value $seedPhp -Encoding UTF8
    try {
        php $tempFile | Write-Host
    } finally {
        Remove-Item -LiteralPath $tempFile -Force -ErrorAction SilentlyContinue
    }
}

function Start-Server {
    Write-Host "Starting Laravel dev server on http://127.0.0.1:8000 ..." -ForegroundColor DarkGray
    $psi = New-Object System.Diagnostics.ProcessStartInfo
    $psi.FileName = 'php'
    $psi.Arguments = 'artisan serve --host 127.0.0.1 --port 8000'
    $psi.RedirectStandardOutput = $true
    $psi.RedirectStandardError = $true
    $psi.UseShellExecute = $false
    $proc = [System.Diagnostics.Process]::Start($psi)
    Start-Sleep -Seconds 3
    return $proc
}

function Stop-Server($proc) {
    if ($proc -and -not $proc.HasExited) {
        Write-Host "Stopping Laravel dev server (PID=$($proc.Id))..." -ForegroundColor DarkGray
        try { $proc.Kill() } catch {}
    }
}

function Post-WebhookTests {
    $base = 'http://127.0.0.1:8000/twilio/webhook'

    Write-Host "Posting status callback test..." -ForegroundColor Cyan
    $resp1 = Invoke-WebRequest -Uri $base -Method Post -Body @{
        MessageSid    = 'SM1234567890'
        MessageStatus = 'delivered'
    } -ContentType 'application/x-www-form-urlencoded'
    Write-Host "Status callback HTTP: $($resp1.StatusCode)" -ForegroundColor Green

    Write-Host "Posting inbound WhatsApp test..." -ForegroundColor Cyan
    $resp2 = Invoke-WebRequest -Uri $base -Method Post -Body @{
        From      = 'whatsapp:+447900000000'
        To        = 'whatsapp:+14155238886'
        Body      = 'Hi, my code is CC0168'
        Direction = 'inbound'
    } -ContentType 'application/x-www-form-urlencoded'
    Write-Host "Inbound HTTP: $($resp2.StatusCode)" -ForegroundColor Green
}

function Show-Logs {
    $twilioLog = 'storage/logs/twilio.log'
    $laravelLog = 'storage/logs/laravel.log'

    if (Test-Path $twilioLog) {
        Write-Host "\n== storage/logs/twilio.log (tail) ==" -ForegroundColor Yellow
        Get-Content -LiteralPath $twilioLog -Tail 50
    } else {
        Write-Host "twilio.log not found yet." -ForegroundColor DarkGray
    }

    if (Test-Path $laravelLog) {
        Write-Host "\n== storage/logs/laravel.log (tail) ==" -ForegroundColor Yellow
        Get-Content -LiteralPath $laravelLog -Tail 50
    } else {
        Write-Host "laravel.log not found yet." -ForegroundColor DarkGray
    }
}

try {
    Ensure-ZapierUrl
    Clear-ConfigCache
    Seed-RentalCode
    $server = Start-Server
    try {
        Post-WebhookTests
        Show-Logs
    } finally {
        Stop-Server -proc $server
    }
    Write-Host "\nDone." -ForegroundColor Cyan
} catch {
    Write-Error $_
    exit 1
}








