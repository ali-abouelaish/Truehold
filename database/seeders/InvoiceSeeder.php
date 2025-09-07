<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleInvoices = [
            [
                'invoice_number' => 'INV-000001',
                'invoice_date' => now()->subDays(5),
                'due_date' => now()->addDays(25),
                'payment_terms' => '30 days',
                'po_number' => 'PO-2025-001',
                'company_name' => 'Truehold Group Limited',
                'company_address' => 'Business Banking',
                'company_phone' => '+44 20 1234 5678',
                'company_email' => 'billing@trueholdgroup.com',
                'company_website' => 'www.trueholdgroup.com',
                'account_holder_name' => 'TRUEHOLD GROUP LTD',
                'account_number' => '63935841',
                'sort_code' => '20-41-50',
                'bank_name' => 'Business Banking',
                'client_name' => 'Banksia LTD',
                'client_address' => "32 Rectory Square\nStepney Green\nLondon E1 6BW",
                'client_email' => 'accounts@banksia.com',
                'client_phone' => '+44 20 9876 5432',
                'items' => [
                    [
                        'description' => 'Tenant Sourcing - Sheron Hamelesshaner Stepney Green 32 Rectory Square £1050 monthly rent',
                        'quantity' => 1,
                        'rate' => 242.00
                    ]
                ],
                'subtotal' => 242.00,
                'tax_rate' => 0.00,
                'tax_amount' => 0.00,
                'total_amount' => 242.00,
                'balance_due' => 242.00,
                'amount_paid' => 0.00,
                'status' => 'sent',
                'notes' => 'Thank you for your business!',
                'terms' => 'Payment on Check-In'
            ],
            [
                'invoice_number' => 'INV-000002',
                'invoice_date' => now()->subDays(10),
                'due_date' => now()->addDays(20),
                'payment_terms' => '30 days',
                'po_number' => 'PO-2025-002',
                'company_name' => 'Truehold Group Limited',
                'company_address' => 'Business Banking',
                'company_phone' => '+44 20 1234 5678',
                'company_email' => 'billing@trueholdgroup.com',
                'company_website' => 'www.trueholdgroup.com',
                'account_holder_name' => 'TRUEHOLD GROUP LTD',
                'account_number' => '63935841',
                'sort_code' => '20-41-50',
                'bank_name' => 'Business Banking',
                'client_name' => 'Property Management Solutions Ltd',
                'client_address' => "123 Business Park\nManchester M1 2AB",
                'client_email' => 'finance@propman.com',
                'client_phone' => '+44 161 123 4567',
                'items' => [
                    [
                        'description' => 'Property Management Services - Monthly Fee',
                        'quantity' => 1,
                        'rate' => 500.00
                    ],
                    [
                        'description' => 'Tenant Screening and Background Checks',
                        'quantity' => 3,
                        'rate' => 75.00
                    ]
                ],
                'subtotal' => 725.00,
                'tax_rate' => 20.00,
                'tax_amount' => 145.00,
                'total_amount' => 870.00,
                'balance_due' => 0.00,
                'amount_paid' => 870.00,
                'status' => 'paid',
                'notes' => 'Payment received. Thank you for your prompt payment.',
                'terms' => 'Payment due within 30 days of invoice date'
            ]
        ];

        foreach ($sampleInvoices as $invoiceData) {
            Invoice::create($invoiceData);
        }
    }
}
