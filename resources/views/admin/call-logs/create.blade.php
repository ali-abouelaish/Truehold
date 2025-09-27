@extends('layouts.admin')

@section('page-title', 'Create Call Log')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create Call Log</h1>
        <a href="{{ route('admin.call-logs.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Call Logs
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.call-logs.store') }}" class="bg-white shadow rounded-lg p-6">
        @csrf
        
        <!-- Call Metadata -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Call Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="call_type" class="block text-sm font-medium text-gray-700">Call Type *</label>
                    <select name="call_type" id="call_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Call Type</option>
                        <option value="outbound" {{ old('call_type') == 'outbound' ? 'selected' : '' }}>Outbound</option>
                        <option value="inbound" {{ old('call_type') == 'inbound' ? 'selected' : '' }}>Inbound</option>
                        <option value="follow_up" {{ old('call_type') == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                        <option value="voicemail" {{ old('call_type') == 'voicemail' ? 'selected' : '' }}>Voicemail</option>
                        <option value="sms_whatsapp" {{ old('call_type') == 'sms_whatsapp' ? 'selected' : '' }}>SMS/WhatsApp</option>
                    </select>
                </div>
                
                <div>
                    <label for="call_status" class="block text-sm font-medium text-gray-700">Call Status *</label>
                    <select name="call_status" id="call_status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Call Status</option>
                        <option value="connected" {{ old('call_status') == 'connected' ? 'selected' : '' }}>Connected</option>
                        <option value="no_answer" {{ old('call_status') == 'no_answer' ? 'selected' : '' }}>No Answer</option>
                        <option value="wrong_number" {{ old('call_status') == 'wrong_number' ? 'selected' : '' }}>Wrong Number</option>
                        <option value="voicemail" {{ old('call_status') == 'voicemail' ? 'selected' : '' }}>Voicemail</option>
                        <option value="callback_requested" {{ old('call_status') == 'callback_requested' ? 'selected' : '' }}>Callback Requested</option>
                    </select>
                </div>
                
                <div>
                    <label for="call_datetime" class="block text-sm font-medium text-gray-700">Call Date & Time</label>
                    <input type="datetime-local" name="call_datetime" id="call_datetime" 
                           value="{{ old('call_datetime', now()->format('Y-m-d\TH:i')) }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
        </div>

        <!-- Landlord Details -->
        <div class="mb-8 conditional-field">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Landlord Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="landlord_name" class="block text-sm font-medium text-gray-700">Landlord Name *</label>
                    <input type="text" name="landlord_name" id="landlord_name" required 
                           value="{{ old('landlord_name') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                
                <div>
                    <label for="landlord_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="landlord_phone" id="landlord_phone" 
                           value="{{ old('landlord_phone') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                
                <div>
                    <label for="landlord_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="landlord_email" id="landlord_email" 
                           value="{{ old('landlord_email') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                
                <div>
                    <label for="landlord_company" class="block text-sm font-medium text-gray-700">Company</label>
                    <input type="text" name="landlord_company" id="landlord_company" 
                           value="{{ old('landlord_company') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                
                <div class="md:col-span-2">
                    <label for="contact_source" class="block text-sm font-medium text-gray-700">Contact Source *</label>
                    <select name="contact_source" id="contact_source" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Contact Source</option>
                        <option value="gumtree" {{ old('contact_source') == 'gumtree' ? 'selected' : '' }}>Gumtree</option>
                        <option value="spareroom" {{ old('contact_source') == 'spareroom' ? 'selected' : '' }}>SpareRoom</option>
                        <option value="zoopla" {{ old('contact_source') == 'zoopla' ? 'selected' : '' }}>Zoopla</option>
                        <option value="rightmove" {{ old('contact_source') == 'rightmove' ? 'selected' : '' }}>Rightmove</option>
                        <option value="referral" {{ old('contact_source') == 'referral' ? 'selected' : '' }}>Referral</option>
                        <option value="other" {{ old('contact_source') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Property Details -->
        <div class="mb-8 conditional-field">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Property Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="property_address" class="block text-sm font-medium text-gray-700">Property Address *</label>
                    <textarea name="property_address" id="property_address" required rows="2" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('property_address') }}</textarea>
                </div>
                
                <div>
                    <label for="property_type" class="block text-sm font-medium text-gray-700">Property Type *</label>
                    <select name="property_type" id="property_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Property Type</option>
                        <option value="studio" {{ old('property_type') == 'studio' ? 'selected' : '' }}>Studio</option>
                        <option value="one_bed" {{ old('property_type') == 'one_bed' ? 'selected' : '' }}>One Bed</option>
                        <option value="two_bed" {{ old('property_type') == 'two_bed' ? 'selected' : '' }}>Two Bed</option>
                        <option value="hmo" {{ old('property_type') == 'hmo' ? 'selected' : '' }}>HMO</option>
                        <option value="other" {{ old('property_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <div>
                    <label for="advertised_rent" class="block text-sm font-medium text-gray-700">Advertised Rent (£) *</label>
                    <input type="number" step="0.01" name="advertised_rent" id="advertised_rent" required 
                           value="{{ old('advertised_rent') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                
                <div>
                    <label for="availability_date" class="block text-sm font-medium text-gray-700">Availability Date</label>
                    <input type="date" name="availability_date" id="availability_date" 
                           value="{{ old('availability_date') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                
                <div>
                    <label for="furnished" class="block text-sm font-medium text-gray-700">Furnished *</label>
                    <select name="furnished" id="furnished" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Furnished Status</option>
                        <option value="furnished" {{ old('furnished') == 'furnished' ? 'selected' : '' }}>Furnished</option>
                        <option value="unfurnished" {{ old('furnished') == 'unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                        <option value="part_furnished" {{ old('furnished') == 'part_furnished' ? 'selected' : '' }}>Part Furnished</option>
                        <option value="other" {{ old('furnished') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="vacant_keys" id="vacant_keys" value="1" 
                           {{ old('vacant_keys') ? 'checked' : '' }} 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="vacant_keys" class="ml-2 block text-sm text-gray-900">Vacant Keys Available</label>
                </div>
            </div>
        </div>

        <!-- Discovery & Compliance -->
        <div class="mb-8 conditional-field">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Discovery & Compliance</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="works_pending" class="block text-sm font-medium text-gray-700">Works Pending</label>
                    <textarea name="works_pending" id="works_pending" rows="3" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('works_pending') }}</textarea>
                </div>
                
                <div>
                    <label for="landlord_priority" class="block text-sm font-medium text-gray-700">Landlord Priority *</label>
                    <select name="landlord_priority" id="landlord_priority" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Priority</option>
                        <option value="speed" {{ old('landlord_priority') == 'speed' ? 'selected' : '' }}>Speed</option>
                        <option value="best_price" {{ old('landlord_priority') == 'best_price' ? 'selected' : '' }}>Best Price</option>
                        <option value="hands_off" {{ old('landlord_priority') == 'hands_off' ? 'selected' : '' }}>Hands Off</option>
                        <option value="other" {{ old('landlord_priority') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label for="discovery_notes" class="block text-sm font-medium text-gray-700">Discovery Notes</label>
                    <textarea name="discovery_notes" id="discovery_notes" rows="3" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('discovery_notes') }}</textarea>
                </div>
                
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Compliance Status</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="compliance_epc" id="compliance_epc" value="1" 
                                   {{ old('compliance_epc') ? 'checked' : '' }} 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="compliance_epc" class="ml-2 block text-sm text-gray-900">EPC</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="compliance_eicr" id="compliance_eicr" value="1" 
                                   {{ old('compliance_eicr') ? 'checked' : '' }} 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="compliance_eicr" class="ml-2 block text-sm text-gray-900">EICR</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="compliance_gas" id="compliance_gas" value="1" 
                                   {{ old('compliance_gas') ? 'checked' : '' }} 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="compliance_gas" class="ml-2 block text-sm text-gray-900">Gas</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="compliance_licence" id="compliance_licence" value="1" 
                                   {{ old('compliance_licence') ? 'checked' : '' }} 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="compliance_licence" class="ml-2 block text-sm text-gray-900">Licence</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Offer Presentation -->
        <div class="mb-8 conditional-field">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Offer Presentation</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="landlord_preference" class="block text-sm font-medium text-gray-700">Landlord Preference *</label>
                    <select name="landlord_preference" id="landlord_preference" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Preference</option>
                        <option value="full_management" {{ old('landlord_preference') == 'full_management' ? 'selected' : '' }}>Full Management</option>
                        <option value="top_up" {{ old('landlord_preference') == 'top_up' ? 'selected' : '' }}>Top Up</option>
                        <option value="let_only" {{ old('landlord_preference') == 'let_only' ? 'selected' : '' }}>Let Only</option>
                        <option value="undecided" {{ old('landlord_preference') == 'undecided' ? 'selected' : '' }}>Undecided</option>
                    </select>
                </div>
                
            </div>
        </div>

        <!-- Outcome & Next Steps -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Outcome & Next Steps</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="next_step_status" class="block text-sm font-medium text-gray-700">Next Step Status *</label>
                    <select name="next_step_status" id="next_step_status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Next Step</option>
                        <option value="send_terms" {{ old('next_step_status') == 'send_terms' ? 'selected' : '' }}>Send Terms</option>
                        <option value="send_compliance_docs" {{ old('next_step_status') == 'send_compliance_docs' ? 'selected' : '' }}>Send Compliance Docs</option>
                        <option value="awaiting_response" {{ old('next_step_status') == 'awaiting_response' ? 'selected' : '' }}>Awaiting Response</option>
                        <option value="collect_keys" {{ old('next_step_status') == 'collect_keys' ? 'selected' : '' }}>Collect Keys</option>
                        <option value="tenant_reference_started" {{ old('next_step_status') == 'tenant_reference_started' ? 'selected' : '' }}>Tenant Reference Started</option>
                        <option value="other" {{ old('next_step_status') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <div>
                    <label for="call_outcome" class="block text-sm font-medium text-gray-700">Call Outcome *</label>
                    <select name="call_outcome" id="call_outcome" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Outcome</option>
                        <option value="instruction_won" {{ old('call_outcome') == 'instruction_won' ? 'selected' : '' }}>Instruction Won</option>
                        <option value="pending" {{ old('call_outcome') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="lost" {{ old('call_outcome') == 'lost' ? 'selected' : '' }}>Lost</option>
                        <option value="not_interested" {{ old('call_outcome') == 'not_interested' ? 'selected' : '' }}>Not Interested</option>
                    </select>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="viewing_booked" id="viewing_booked" value="1" 
                           {{ old('viewing_booked') ? 'checked' : '' }} 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="viewing_booked" class="ml-2 block text-sm text-gray-900">Viewing Booked</label>
                </div>
                
                <div>
                    <label for="viewing_datetime" class="block text-sm font-medium text-gray-700">Viewing Date & Time</label>
                    <input type="datetime-local" name="viewing_datetime" id="viewing_datetime" 
                           value="{{ old('viewing_datetime') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="follow_up_needed" id="follow_up_needed" value="1" 
                           {{ old('follow_up_needed') ? 'checked' : '' }} 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="follow_up_needed" class="ml-2 block text-sm text-gray-900">Follow-up Needed</label>
                </div>
                
                <div>
                    <label for="follow_up_datetime" class="block text-sm font-medium text-gray-700">Follow-up Date & Time</label>
                    <input type="datetime-local" name="follow_up_datetime" id="follow_up_datetime" 
                           value="{{ old('follow_up_datetime') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                
                <div class="md:col-span-2">
                    <label for="agent_notes" class="block text-sm font-medium text-gray-700">Agent Notes</label>
                    <textarea name="agent_notes" id="agent_notes" rows="4" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('agent_notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Automation Hooks -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Automation Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center">
                    <input type="checkbox" name="send_sms" id="send_sms" value="1" 
                           {{ old('send_sms') ? 'checked' : '' }} 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="send_sms" class="ml-2 block text-sm text-gray-900">Send SMS</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="send_email" id="send_email" value="1" 
                           {{ old('send_email') ? 'checked' : '' }} 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="send_email" class="ml-2 block text-sm text-gray-900">Send Email</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="send_whatsapp" id="send_whatsapp" value="1" 
                           {{ old('send_whatsapp') ? 'checked' : '' }} 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="send_whatsapp" class="ml-2 block text-sm text-gray-900">Send WhatsApp</label>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Call Log
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const callStatusSelect = document.getElementById('call_status');
    const conditionalFields = document.querySelectorAll('.conditional-field');
    
    function toggleFields() {
        const isNoAnswer = callStatusSelect.value === 'no_answer';
        
        conditionalFields.forEach(field => {
            if (isNoAnswer) {
                field.style.display = 'none';
                // Make fields not required
                const inputs = field.querySelectorAll('input[required], select[required], textarea[required]');
                inputs.forEach(input => {
                    input.removeAttribute('required');
                });
            } else {
                field.style.display = 'block';
                // Restore required attributes for essential fields
                const inputs = field.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (field.classList.contains('required-field')) {
                        input.setAttribute('required', 'required');
                    }
                });
            }
        });
    }
    
    callStatusSelect.addEventListener('change', toggleFields);
    toggleFields(); // Run on page load
});
</script>
@endsection
