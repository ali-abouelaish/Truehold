@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Bonus Record Details</h1>
                <div class="flex space-x-3">
                    <a href="{{ route('agent.profile.bonuses.edit', $bonus) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        Edit
                    </a>
                    <a href="{{ route('agent.profile.bonuses') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-200">
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Bonus Information</h2>
            </div>
            
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $bonus->date->format('F d, Y') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Agent</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $bonus->agent->display_name }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Landlord</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $bonus->landlord ?? 'N/A' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Property</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $bonus->property ?? 'N/A' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Client</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $bonus->client ?? 'N/A' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Invoice Sent to Management</dt>
                        <dd class="mt-1">
                            @if($bonus->invoice_sent_to_management)
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
                        <dd class="mt-1 text-lg font-semibold text-gray-900">£{{ number_format($bonus->full_commission, 2) }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Agent Commission</dt>
                        <dd class="mt-1 text-lg font-semibold text-green-600">£{{ number_format($bonus->agent_commission, 2) }}</dd>
                    </div>
                </div>
            </div>
            
            @if($bonus->notes)
            <div class="px-6 py-4 border-t border-gray-200">
                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $bonus->notes }}</dd>
            </div>
            @endif
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Created: {{ $bonus->created_at->format('M d, Y \a\t g:i A') }}</span>
                    <span>Last updated: {{ $bonus->updated_at->format('M d, Y \a\t g:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
