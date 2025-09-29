@extends('layouts.admin')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Invoice {{ $invoice->invoice_number }}</h1>
            <p class="text-gray-600 mt-2">Created {{ $invoice->created_at->format('M d, Y \a\t g:i A') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.invoices.pdf', $invoice) }}" 
               class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
            </a>
            <a href="{{ route('admin.invoices.edit', $invoice) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete this invoice?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" />
                    </svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Status and Actions -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $invoice->getStatusBadgeClass() }}">
                    {{ ucfirst($invoice->status) }}
                </span>
                @if($invoice->isOverdue())
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                        Overdue
                    </span>
                @endif
            </div>
            
            <div class="flex space-x-2">
                @if($invoice->status === 'draft')
                    <form method="POST" action="{{ route('admin.invoices.mark-sent', $invoice) }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                            Mark as Sent
                        </button>
                    </form>
                @endif
                
                @if($invoice->status === 'sent')
                    <form method="POST" action="{{ route('admin.invoices.mark-paid', $invoice) }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                            Mark as Paid
                        </button>
                    </form>
                @endif
                
                <form method="POST" action="{{ route('admin.invoices.duplicate', $invoice) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        Duplicate
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Invoice Details -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Company & Client Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">From</h3>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-900">{{ $invoice->company_name }}</p>
                        <p class="text-gray-600">{{ $invoice->company_address }}</p>
                        @if($invoice->company_phone)
                            <p class="text-gray-600">Phone: {{ $invoice->company_phone }}</p>
                        @endif
                        @if($invoice->company_email)
                            <p class="text-gray-600">Email: {{ $invoice->company_email }}</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Receiver's Details</h3>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-900">{{ $invoice->client_name }}</p>
                        <p class="text-gray-600">{{ nl2br(e($invoice->client_address)) }}</p>
                        @if($invoice->client_email)
                            <p class="text-gray-600">Email: {{ $invoice->client_email }}</p>
                        @endif
                        @if($invoice->client_phone)
                            <p class="text-gray-600">Phone: {{ $invoice->client_phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Banking Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Banking Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Account Holder</p>
                        <p class="font-medium">{{ $invoice->account_holder_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Account Number</p>
                        <p class="font-medium">{{ $invoice->account_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Sort Code</p>
                        <p class="font-medium">{{ $invoice->sort_code }}</p>
                    </div>
                    @if($invoice->bank_name)
                    <div>
                        <p class="text-sm text-gray-600">Bank</p>
                        <p class="font-medium">{{ $invoice->bank_name }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item['description'] }}</td>
                                <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $item['quantity'] }}</td>
                                <td class="px-6 py-4 text-right text-sm text-gray-900">£{{ number_format($item['rate'], 2) }}</td>
                                <td class="px-6 py-4 text-right text-sm text-gray-900">£{{ number_format($item['quantity'] * $item['rate'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Invoice Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Invoice Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Invoice Number:</span>
                        <span class="font-medium">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="font-medium">{{ $invoice->invoice_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Due Date:</span>
                        <span class="font-medium">{{ $invoice->due_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Terms:</span>
                        <span class="font-medium">{{ $invoice->payment_terms }}</span>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">£{{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    @if($invoice->tax_rate > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax ({{ $invoice->tax_rate }}%):</span>
                        <span class="font-medium">£{{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-lg font-semibold border-t pt-3">
                        <span>Total:</span>
                        <span>£{{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                    @if($invoice->amount_paid > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount Paid:</span>
                        <span class="font-medium text-green-600">£{{ number_format($invoice->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-semibold border-t pt-3">
                        <span>Balance Due:</span>
                        <span class="text-red-600">£{{ number_format($invoice->balance_due, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Notes & Terms -->
            @if($invoice->notes || $invoice->terms)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes & Terms</h3>
                <div class="space-y-4">
                    @if($invoice->notes)
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Notes:</h4>
                        <p class="text-gray-600 text-sm">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                    @if($invoice->terms)
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Terms:</h4>
                        <p class="text-gray-600 text-sm">{{ $invoice->terms }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
