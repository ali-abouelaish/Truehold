<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\RentalCode;

class TestZapierWebhook extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'rental-code:test-zapier {--id=} {--code=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send a test Zapier webhook for an existing RentalCode (by --id or --code, defaults to latest)';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$webhookUrl = config('services.zapier.zapier_webhook_url') ?: config('services.zapier.rental_code_webhook_url');
		if (empty($webhookUrl)) {
			$this->error('Zapier webhook URL not configured in .env');
			return 1;
		}

		// Locate the rental code record
		$rentalCode = null;
		$id = $this->option('id');
		$code = $this->option('code');
		if ($id) {
			$rentalCode = RentalCode::find($id);
		} elseif ($code) {
			$rentalCode = RentalCode::where('rental_code', $code)->first();
		} else {
			$rentalCode = RentalCode::latest('id')->first();
		}

		if (!$rentalCode) {
			$this->error('No RentalCode found for the provided criteria.');
			return 1;
		}

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
			// Original fields
			'rental_code' => (string) ($rentalCode->rental_code ?? ''),
			'property_name' => $propertyName,
			'client_name' => $clientName,
			'agent_name' => $agentName,
			'rent_amount' => $rentAmount,
			'status' => (string) ($rentalCode->status ?? ''),
			'created_at' => optional($rentalCode->created_at)->toIso8601String(),
			// Requested template variables
			'rentalcode_details' => $rentalDetails,
			'clientprofile' => $clientProfile,
			'agent' => $agentName,
			'marketing_agent' => $marketingAgentName,
			// Composed message
			'message' => $message,
		];

		$this->info('Sending Zapier webhook...');
		$this->line('URL: ' . $webhookUrl);
		$this->line('Payload: ' . json_encode($payload));

		try {
			$response = Http::asJson()->post($webhookUrl, $payload);
			if ($response->successful()) {
				$this->info('Webhook sent successfully. Status: ' . $response->status());
				return 0;
			}
			$this->error('Webhook failed. Status: ' . $response->status());
			$this->line($response->body());
			Log::error('Zapier webhook test failed', [
				'status' => $response->status(),
				'body' => $response->body(),
				'payload' => $payload,
			]);
			return 1;
		} catch (\Throwable $e) {
			$this->error('Exception while sending webhook: ' . $e->getMessage());
			Log::error('Zapier webhook test exception', [
				'error' => $e->getMessage(),
			]);
			return 1;
		}
	}
}


