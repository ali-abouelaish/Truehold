@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Payment Record Details</h1>
                <div class="flex space-x-3">
                    @if($payment->canBeEdited())
                        <a href="{{ route('agent.profile.payments.edit', $payment) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                            Edit
                        </a>
                    @endif
                    <a href="{{ route('agent.profile.payments') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-200">
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Payment Information</h2>
            </div>
            
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->date->format('F d, Y') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Agent</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->agent->display_name }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $payment->type_display }}
                            </span>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->status_badge_class }}">
                                {{ $payment->status_display }}
                            </span>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Landlord</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->landlord ?? 'N/A' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Property</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->property ?? 'N/A' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Client</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->client ?? 'N/A' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Invoice Sent to Management</dt>
                        <dd class="mt-1">
                            @if($payment->invoice_sent_to_management)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Yes
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    No
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full Commission</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">£{{ number_format($payment->full_commission, 2) }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Agent Commission</dt>
                        <dd class="mt-1 text-lg font-semibold text-green-600">£{{ number_format($payment->agent_commission, 2) }}</dd>
                    </div>
                </div>
            </div>
            
            @if($payment->payment_method)
            <div class="px-6 py-4 border-t border-gray-200">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->method_display }}</dd>
                    </div>
                    
                    @if($payment->paid_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Paid Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->paid_date->format('F d, Y') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif
            
            @if($payment->notes)
            <div class="px-6 py-4 border-t border-gray-200">
                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $payment->notes }}</dd>
            </div>
            @endif
            
            @if($payment->admin_notes)
            <div class="px-6 py-4 border-t border-gray-200">
                <dt class="text-sm font-medium text-gray-500">Admin Notes</dt>
                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $payment->admin_notes }}</dd>
            </div>
            @endif
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Created: {{ $payment->created_at->format('M d, Y \a\t g:i A') }}</span>
                    <span>Last updated: {{ $payment->updated_at->format('M d, Y \a\t g:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
