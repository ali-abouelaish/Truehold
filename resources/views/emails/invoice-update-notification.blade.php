<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice Updated</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0; 
            padding: 0; 
            background-color: #f5f5f5;
        }
        .container { 
            max-width: 800px; 
            margin: 20px auto; 
            background: white; 
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header { 
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); 
            color: white; 
            padding: 30px; 
            text-align: center; 
        }
        .header h1 { 
            margin: 0; 
            font-size: 28px; 
            font-weight: 300;
        }
        .header p { 
            margin: 10px 0 0 0; 
            opacity: 0.9; 
            font-size: 16px;
        }
        .content { 
            padding: 40px; 
        }
        .section { 
            margin-bottom: 30px; 
            border-left: 4px solid #f59e0b; 
            padding-left: 20px;
        }
        .section h3 { 
            color: #f59e0b; 
            margin: 0 0 20px 0; 
            font-size: 20px; 
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 15px; 
        }
        .info-item { 
            margin-bottom: 15px; 
            padding: 10px; 
            background: #f8f9fa; 
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }
        .label { 
            font-weight: 600; 
            color: #495057; 
            display: block; 
            margin-bottom: 5px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .value { 
            color: #212529; 
            font-size: 16px;
            font-weight: 500;
        }
        .footer { 
            background: #f8f9fa; 
            padding: 20px; 
            text-align: center; 
            font-size: 12px; 
            color: #6c757d; 
            border-top: 1px solid #e9ecef;
        }
        .highlight {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .highlight h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 300;
        }
        .update-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        @media (max-width: 600px) {
            .info-grid { 
                grid-template-columns: 1fr; 
            }
            .container {
                margin: 10px;
            }
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Invoice Updated</h1>
            <p>Updated by: <strong>{{ $agentName }}</strong></p>
        </div>

        <div class="content">
            <div class="update-info">
                <strong>⚠️ Important:</strong> This invoice has been updated. Please review the changes and take any necessary action.
            </div>

            <div class="highlight">
                <h2>Invoice: {{ $invoice->invoice_number }}</h2>
            </div>

            <div class="section">
                <h3>📋 Invoice Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Invoice Number</span>
                        <span class="value">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Invoice Date</span>
                        <span class="value">{{ $invoice->invoice_date ? $invoice->invoice_date->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Due Date</span>
                        <span class="value">{{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Payment Terms</span>
                        <span class="value">{{ $invoice->payment_terms ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Total Amount</span>
                        <span class="value">£{{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Status</span>
                        <span class="value">{{ ucfirst($invoice->status ?? 'Draft') }}</span>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>👤 Client Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Client Name</span>
                        <span class="value">{{ $invoice->client_name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Client Email</span>
                        <span class="value">{{ $invoice->client_email ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Client Phone</span>
                        <span class="value">{{ $invoice->client_phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Client Address</span>
                        <span class="value">{{ $invoice->client_address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>💰 Financial Summary</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Subtotal</span>
                        <span class="value">£{{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Tax Rate</span>
                        <span class="value">{{ $invoice->tax_rate ?? 0 }}%</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Tax Amount</span>
                        <span class="value">£{{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Total Amount</span>
                        <span class="value">£{{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Amount Paid</span>
                        <span class="value">£{{ number_format($invoice->amount_paid, 2) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Balance Due</span>
                        <span class="value">£{{ number_format($invoice->balance_due, 2) }}</span>
                    </div>
                </div>
            </div>

            @if($invoice->notes)
            <div class="section">
                <h3>📝 Notes</h3>
                <div class="info-item">
                    <span class="value">{{ $invoice->notes }}</span>
                </div>
            </div>
            @endif

            @if($invoice->terms)
            <div class="section">
                <h3>📋 Terms & Conditions</h3>
                <div class="info-item">
                    <span class="value">{{ $invoice->terms }}</span>
                </div>
            </div>
            @endif
        </div>

        <div class="footer">
            <p>This email was automatically generated by the Truehold Group System.</p>
            <p>Updated on: {{ now()->format('d/m/Y H:i:s') }}</p>
            <p><strong>Note:</strong> Please review the attached PDF for the complete updated invoice details.</p>
        </div>
    </div>
</body>
</html>
