<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            font-family: 'Arial', 'Helvetica', sans-serif;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        
        .company-logo {
            max-width: 120px;
            height: 120px;
            margin-bottom: 15px;
            object-fit: contain;
            border-radius: 8px;
        }
        
        .company-info h1 {
            font-size: 20px;
            font-weight: bold;
            color: #2c2c2c;
            margin-bottom: 10px;
        }
        
        .company-info p {
            font-size: 12px;
            color: #333;
            margin: 2px 0;
            line-height: 1.3;
        }
        
        .invoice-details {
            text-align: right;
        }
        
        .invoice-details h2 {
            font-size: 24px;
            color: #2c2c2c;
            margin-bottom: 10px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .invoice-meta {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        
        .invoice-meta p {
            margin: 3px 0;
            font-size: 12px;
        }
        
        .invoice-meta strong {
            color: #2c2c2c;
            font-weight: bold;
        }
        
        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .bill-to, .banking-details {
            flex: 1;
            margin-right: 20px;
        }
        
        .banking-details {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        
        .balance-due-box {
            background: #f0f0f0;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .balance-due-box strong {
            font-weight: bold;
            color: #2c2c2c;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c2c2c;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .client-info p, .banking-info p {
            margin: 3px 0;
            font-size: 12px;
            line-height: 1.3;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            table-layout: fixed;
        }
        
        .items-table th {
            background: #2c2c2c;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            letter-spacing: 0.5px;
            vertical-align: top;
        }
        
        .items-table th.text-center {
            text-align: center;
        }
        
        .items-table th.text-right {
            text-align: right;
        }
        
        .items-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 12px;
            vertical-align: top;
        }
        
        .items-table td.text-center {
            text-align: center;
        }
        
        .items-table td.text-right {
            text-align: right;
        }
        
        .items-table th:nth-child(1),
        .items-table td:nth-child(1) {
            width: 50%;
        }
        
        .items-table th:nth-child(2),
        .items-table td:nth-child(2) {
            width: 15%;
        }
        
        .items-table th:nth-child(3),
        .items-table td:nth-child(3) {
            width: 17.5%;
        }
        
        .items-table th:nth-child(4),
        .items-table td:nth-child(4) {
            width: 17.5%;
        }
        
        .items-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        
        .totals-table {
            width: 300px;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ddd;
        }
        
        .totals-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 12px;
        }
        
        .totals-table .total-row {
            background: #2c2c2c;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        
        .totals-table .total-row td {
            border-bottom: none;
        }
        
        .notes-section {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        
        .notes-section h3 {
            color: #2c2c2c;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .notes-section p {
            font-size: 12px;
            line-height: 1.5;
            color: #666;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            color: #999;
            font-size: 12px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-draft { background: #f3f4f6; color: #374151; }
        .status-sent { background: #dbeafe; color: #1e40af; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-overdue { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #f3f4f6; color: #374151; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <img src="{{ public_path('images/truehold-logo.png') }}" alt="TRUEHOLD GROUP LTD" class="company-logo">
                <h1>{{ $invoice->company_name }}</h1>
                <p><strong>Business Banking</strong></p>
                <p><strong>Account holder name:</strong> {{ $invoice->account_holder_name }}</p>
                <p><strong>Account number:</strong> {{ $invoice->account_number }}</p>
                <p><strong>Sort code:</strong> {{ $invoice->sort_code }}</p>
            </div>
            
            <div class="invoice-details">
                <h2>INVOICE</h2>
                <p><strong># {{ $invoice->invoice_number }}</strong></p>
                <div class="invoice-meta">
                    <p><strong>Date:</strong> {{ $invoice->invoice_date->format('M d, Y') }}</p>
                    <p><strong>Payment Terms:</strong> {{ $invoice->payment_terms }}</p>
                    <p><strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</p>
                    @if($invoice->po_number)
                        <p><strong>PO Number:</strong> {{ $invoice->po_number }}</p>
                    @endif
                </div>
                <div class="balance-due-box">
                    <strong>Balance Due:</strong>
                    <strong>£{{ number_format($invoice->balance_due, 2) }}</strong>
                </div>
            </div>
        </div>
        
        <!-- Billing Section -->
        <div class="billing-section">
            <div class="bill-to">
                <h3 class="section-title">Bill To:</h3>
                <div class="client-info">
                    <p><strong>{{ $invoice->client_name }}</strong></p>
                </div>
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-right">Rate</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item['description'] }}</td>
                    <td class="text-center">{{ $item['quantity'] }}</td>
                    <td class="text-right">£{{ number_format($item['rate'], 2) }}</td>
                    <td class="text-right">£{{ number_format($item['quantity'] * $item['rate'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">£{{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->tax_rate > 0)
                <tr>
                    <td>Tax ({{ $invoice->tax_rate }}%):</td>
                    <td class="text-right">£{{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Total:</td>
                    <td class="text-right">£{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                @if($invoice->amount_paid > 0)
                <tr>
                    <td>Amount Paid:</td>
                    <td class="text-right">£{{ number_format($invoice->amount_paid, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Balance Due:</td>
                    <td class="text-right">£{{ number_format($invoice->balance_due, 2) }}</td>
                </tr>
                @endif
            </table>
        </div>
        
        <!-- Notes and Terms -->
        @if($invoice->notes || $invoice->terms)
        <div class="notes-section">
            @if($invoice->notes)
            <h3>Notes:</h3>
            <p>{{ $invoice->notes }}</p>
            @endif
            
            @if($invoice->terms)
            <h3>Terms:</h3>
            <p>{{ $invoice->terms }}</p>
            @endif
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Generated on {{ now()->format('M d, Y \a\t g:i A') }}</p>
        </div>
    </div>
</body>
</html>
