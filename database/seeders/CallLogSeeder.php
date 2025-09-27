<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CallLog;
use App\Models\User;
use Carbon\Carbon;

class CallLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first agent user (or create one if none exists)
        $agent = User::where('role', 'agent')->first();
        
        if (!$agent) {
            $agent = User::create([
                'name' => 'Test Agent',
                'email' => 'agent@example.com',
                'password' => bcrypt('password'),
                'role' => 'agent',
                'email_verified_at' => now(),
            ]);
        }

        $sampleCallLogs = [
            [
                'agent_id' => $agent->id,
                'call_type' => 'outbound',
                'call_status' => 'connected',
                'call_datetime' => now()->subDays(1),
                'landlord_name' => 'John Smith',
                'landlord_phone' => '+44 7700 900123',
                'landlord_email' => 'john.smith@email.com',
                'contact_source' => 'zoopla',
                'property_address' => '123 Main Street, London, SW1A 1AA',
                'property_type' => 'two_bed',
                'advertised_rent' => 2500.00,
                'availability_date' => Carbon::now()->addDays(30),
                'vacant_keys' => true,
                'furnished' => 'furnished',
                'landlord_priority' => 'best_price',
                'landlord_preference' => 'full_management',
                'next_step_status' => 'send_terms',
                'call_outcome' => 'instruction_won',
                'agent_notes' => 'Very interested landlord, wants to proceed quickly. Property in excellent condition.',
                'follow_up_needed' => false,
            ],
            [
                'agent_id' => $agent->id,
                'call_type' => 'inbound',
                'call_status' => 'connected',
                'call_datetime' => now()->subHours(6),
                'landlord_name' => 'Sarah Johnson',
                'landlord_phone' => '+44 7700 900456',
                'landlord_email' => 'sarah.johnson@email.com',
                'contact_source' => 'rightmove',
                'property_address' => '456 Oak Avenue, Manchester, M1 1AA',
                'property_type' => 'one_bed',
                'advertised_rent' => 1800.00,
                'availability_date' => Carbon::now()->addDays(14),
                'vacant_keys' => false,
                'furnished' => 'unfurnished',
                'compliance_epc' => true,
                'compliance_gas' => true,
                'landlord_priority' => 'speed',
                'discovery_notes' => 'Needs some minor repairs before letting.',
                'landlord_preference' => 'let_only',
                'next_step_status' => 'send_compliance_docs',
                'call_outcome' => 'pending',
                'agent_notes' => 'Interested but wants to see compliance documents first.',
                'follow_up_needed' => true,
                'follow_up_datetime' => Carbon::now()->addDays(3),
            ],
            [
                'agent_id' => $agent->id,
                'call_type' => 'follow_up',
                'call_status' => 'connected',
                'call_datetime' => now()->subHours(2),
                'landlord_name' => 'Michael Brown',
                'landlord_phone' => '+44 7700 900789',
                'landlord_email' => 'michael.brown@email.com',
                'contact_source' => 'gumtree',
                'property_address' => '789 Pine Road, Birmingham, B1 1AA',
                'property_type' => 'studio',
                'advertised_rent' => 1200.00,
                'availability_date' => Carbon::now()->addDays(7),
                'vacant_keys' => true,
                'furnished' => 'part_furnished',
                'works_pending' => 'Painting and deep clean required.',
                'compliance_epc' => false,
                'compliance_eicr' => false,
                'compliance_gas' => false,
                'compliance_licence' => false,
                'landlord_priority' => 'hands_off',
                'discovery_notes' => 'First-time landlord, needs guidance on process.',
                'packages_discussed' => ['full_management', 'top_up'],
                'landlord_preference' => 'undecided',
                'objections' => ['price_concern', 'service_fees'],
                'objection_response' => 'Explained value proposition and competitive pricing.',
                'next_step_status' => 'awaiting_response',
                'call_outcome' => 'pending',
                'agent_notes' => 'Needs time to think about our services. Follow up in 2 days.',
                'follow_up_needed' => true,
                'follow_up_datetime' => Carbon::now()->addDays(2),
                'viewing_booked' => true,
                'viewing_datetime' => Carbon::now()->addDays(5),
            ],
            [
                'agent_id' => $agent->id,
                'call_type' => 'outbound',
                'call_status' => 'no_answer',
                'call_datetime' => now()->subMinutes(30),
                'landlord_name' => 'Emma Wilson',
                'landlord_phone' => '+44 7700 900321',
                'contact_source' => 'referral',
                'property_address' => '321 Elm Street, Liverpool, L1 1AA',
                'property_type' => 'hmo',
                'advertised_rent' => 3500.00,
                'vacant_keys' => false,
                'furnished' => 'furnished',
                'landlord_priority' => 'best_price',
                'landlord_preference' => 'full_management',
                'next_step_status' => 'other',
                'call_outcome' => 'pending',
                'agent_notes' => 'No answer, left voicemail. Will try again tomorrow.',
                'follow_up_needed' => true,
                'follow_up_datetime' => Carbon::now()->addDay(),
                'send_sms' => true,
            ],
            [
                'agent_id' => $agent->id,
                'call_type' => 'voicemail',
                'call_status' => 'voicemail',
                'call_datetime' => now()->subHours(12),
                'landlord_name' => 'David Taylor',
                'landlord_phone' => '+44 7700 900654',
                'landlord_email' => 'david.taylor@email.com',
                'contact_source' => 'spareroom',
                'property_address' => '654 Maple Lane, Bristol, BS1 1AA',
                'property_type' => 'two_bed',
                'advertised_rent' => 2200.00,
                'availability_date' => Carbon::now()->addDays(21),
                'vacant_keys' => true,
                'furnished' => 'unfurnished',
                'compliance_epc' => true,
                'compliance_eicr' => true,
                'compliance_gas' => true,
                'landlord_priority' => 'speed',
                'landlord_preference' => 'top_up',
                'next_step_status' => 'tenant_reference_started',
                'call_outcome' => 'instruction_won',
                'agent_notes' => 'Left detailed voicemail about our services. Property already has tenant interested.',
                'follow_up_needed' => false,
                'viewing_booked' => true,
                'viewing_datetime' => Carbon::now()->addDays(3),
                'send_email' => true,
            ]
        ];

        foreach ($sampleCallLogs as $callLogData) {
            CallLog::create($callLogData);
        }
    }
}
