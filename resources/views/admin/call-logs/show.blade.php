@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Call Log Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.call-logs.edit', $callLog) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Edit Call Log
            </a>
            <a href="{{ route('admin.call-logs.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Call Logs
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- Call Information -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Call Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Call Type</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $callLog->call_type)) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Call Status</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $callLog->call_status)) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Call Date & Time</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $callLog->call_datetime->format('M j, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Landlord Information -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Landlord Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $callLog->landlord_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Phone</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $callLog->landlord_phone ?? 'Not provided' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Email</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $callLog->landlord_email ?? 'Not provided' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Company</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $callLog->landlord_company ?? 'Not provided' }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Contact Source</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($callLog->contact_source) }}</p>
                </div>
            </div>
        </div>

        <!-- Property Information -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Property Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Address</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $callLog->property_address }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Property Type</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $callLog->property_type)) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Advertised Rent</label>
                    <p class="mt-1 text-sm text-gray-900">£{{ number_format($callLog->advertised_rent, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Availability Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $callLog->availability_date ? $callLog->availability_date->format('M j, Y') : 'Not specified' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Furnished</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $callLog->furnished)) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Vacant Keys</label>
                    <p class="mt-1 text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $callLog->vacant_keys ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $callLog->vacant_keys ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Discovery & Compliance -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Discovery & Compliance</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Landlord Priority</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $callLog->landlord_priority)) }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Works Pending</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $callLog->works_pending ?? 'None specified' }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Discovery Notes</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $callLog->discovery_notes ?? 'None specified' }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Compliance Status</label>
                    <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $callLog->compliance_epc ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                EPC: {{ $callLog->compliance_epc ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $callLog->compliance_eicr ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                EICR: {{ $callLog->compliance_eicr ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $callLog->compliance_gas ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                Gas: {{ $callLog->compliance_gas ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $callLog->compliance_licence ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                Licence: {{ $callLog->compliance_licence ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Offer Presentation -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Offer Presentation</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Landlord Preference</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $callLog->landlord_preference)) }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Objection Response</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $callLog->objection_response ?? 'None specified' }}</p>
                </div>
            </div>
        </div>

        <!-- Outcome & Next Steps -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Outcome & Next Steps</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Call Outcome</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $callLog->call_outcome === 'instruction_won' ? 'bg-green-100 text-green-800' : 
                               ($callLog->call_outcome === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst(str_replace('_', ' ', $callLog->call_outcome)) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Next Step Status</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $callLog->next_step_status)) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Viewing Booked</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $callLog->viewing_booked ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $callLog->viewing_booked ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Viewing Date & Time</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $callLog->viewing_datetime ? $callLog->viewing_datetime->format('M j, Y g:i A') : 'Not scheduled' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Follow-up Needed</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $callLog->follow_up_needed ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                            {{ $callLog->follow_up_needed ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Follow-up Date & Time</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $callLog->follow_up_datetime ? $callLog->follow_up_datetime->format('M j, Y g:i A') : 'Not scheduled' }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Agent Notes</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $callLog->agent_notes ?? 'None specified' }}</p>
                </div>
            </div>
        </div>

        <!-- Automation Actions -->
        <div class="px-6 py-4">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Automation Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Send SMS</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $callLog->send_sms ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $callLog->send_sms ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Send Email</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $callLog->send_email ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $callLog->send_email ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Send WhatsApp</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $callLog->send_whatsapp ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $callLog->send_whatsapp ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Agent Information -->
    <div class="mt-6 bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Agent Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-500">Agent Name</label>
                <p class="mt-1 text-sm text-gray-900">{{ $callLog->agent->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Agent Email</label>
                <p class="mt-1 text-sm text-gray-900">{{ $callLog->agent->email }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
