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
        
        <!-- Phone Number Check - First Step -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">📞 Phone Number Check</h2>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-blue-800 mb-3">Start by entering the phone number to check if this contact has been called before.</p>
                <div class="relative">
                    <label for="landlord_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                    <input type="text" name="landlord_phone" id="landlord_phone" required
                           value="{{ old('landlord_phone') }}" 
                           placeholder="Enter phone number (e.g., +44 7700 900123)"
                           class="block w-full border-gray-300 rounded-md shadow-sm text-lg py-3 px-4">
                    <div id="phone-status" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                        <span id="phone-status-icon" class="text-lg"></span>
                    </div>
                </div>
                <div id="phone-history" class="mt-3 hidden">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-yellow-800">This number has been called before</span>
                        </div>
                        <div id="phone-history-details" class="mt-2 text-sm text-yellow-700"></div>
                        <button type="button" id="view-previous-calls" class="mt-2 text-sm text-yellow-600 hover:text-yellow-800 underline">
                            View previous calls
                        </button>
                    </div>
                </div>
            </div>
        </div>

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

        <!-- Automation Actions -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Automation Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex items-center">
                    <input type="checkbox" name="send_sms" id="send_sms" value="1" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="send_sms" class="ml-2 block text-sm text-gray-900">
                        Send SMS
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="send_email" id="send_email" value="1" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="send_email" class="ml-2 block text-sm text-gray-900">
                        Send Email
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="send_whatsapp" id="send_whatsapp" value="1" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="send_whatsapp" class="ml-2 block text-sm text-gray-900">
                        Send WhatsApp
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="remove_automation" id="remove_automation" value="1" 
                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="remove_automation" class="ml-2 block text-sm text-gray-900">
                        Remove
                    </label>
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
                
                <div>
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
                    <label for="number_of_beds" class="block text-sm font-medium text-gray-700">Number of Beds</label>
                    <input type="number" name="number_of_beds" id="number_of_beds" min="0" max="20" 
                           value="{{ old('number_of_beds') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                
                <div>
                    <label for="number_of_bathrooms" class="block text-sm font-medium text-gray-700">Number of Bathrooms</label>
                    <input type="number" name="number_of_bathrooms" id="number_of_bathrooms" min="0" max="10" 
                           value="{{ old('number_of_bathrooms') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
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
                
                <div class="md:col-span-2">
                    <label for="room_link" class="block text-sm font-medium text-gray-700">Room Link</label>
                    <input type="url" name="room_link" id="room_link" 
                           value="{{ old('room_link') }}" 
                           placeholder="https://example.com/room-listing"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
        </div>

        <!-- Discovery & Compliance -->
        <div class="mb-8 conditional-field">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Discovery & Compliance</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
    const phoneInput = document.getElementById('landlord_phone');
    const phoneStatus = document.getElementById('phone-status');
    const phoneStatusIcon = document.getElementById('phone-status-icon');
    const phoneHistory = document.getElementById('phone-history');
    const phoneHistoryDetails = document.getElementById('phone-history-details');
    const viewPreviousCallsBtn = document.getElementById('view-previous-calls');
    
    let phoneCheckTimeout;
    
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
    
    function checkPhoneNumber(phone) {
        if (!phone || phone.length < 5) {
            hidePhoneStatus();
            return;
        }
        
        // Clear previous timeout
        clearTimeout(phoneCheckTimeout);
        
        // Set loading state
        showPhoneStatus('loading');
        
        // Debounce the API call
        phoneCheckTimeout = setTimeout(() => {
            fetch(`/admin/call-logs/check-phone?phone=${encodeURIComponent(phone)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'include'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.has_been_called) {
                        showPhoneStatus('warning');
                        showPhoneHistory(data.call_history);
                    } else {
                        showPhoneStatus('success');
                        hidePhoneHistory();
                    }
                })
                .catch(error => {
                    console.error('Error checking phone number:', error);
                    hidePhoneStatus();
                });
        }, 500);
    }
    
    function showPhoneStatus(type) {
        phoneStatus.classList.remove('hidden');
        phoneStatusIcon.className = 'text-sm';
        
        if (type === 'loading') {
            phoneStatusIcon.innerHTML = '⏳';
            phoneStatusIcon.className += ' text-blue-500';
        } else if (type === 'warning') {
            phoneStatusIcon.innerHTML = '⚠️';
            phoneStatusIcon.className += ' text-yellow-500';
        } else if (type === 'success') {
            phoneStatusIcon.innerHTML = '✅';
            phoneStatusIcon.className += ' text-green-500';
        }
    }
    
    function hidePhoneStatus() {
        phoneStatus.classList.add('hidden');
    }
    
    function showPhoneHistory(history) {
        if (!history) return;
        
        const details = [];
        if (history.total_calls > 0) {
            details.push(`${history.total_calls} previous call${history.total_calls > 1 ? 's' : ''}`);
        }
        if (history.last_call_date) {
            const lastCallDate = new Date(history.last_call_date).toLocaleDateString();
            details.push(`Last call: ${lastCallDate}`);
        }
        if (history.landlord_names && history.landlord_names.length > 0) {
            details.push(`Names: ${history.landlord_names.join(', ')}`);
        }
        
        phoneHistoryDetails.textContent = details.join(' • ');
        phoneHistory.classList.remove('hidden');
    }
    
    function hidePhoneHistory() {
        phoneHistory.classList.add('hidden');
    }
    
    function showPreviousCalls(phone) {
        fetch(`/admin/call-logs/previous-calls?phone=${encodeURIComponent(phone)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'include'
        })
            .then(response => response.json())
            .then(data => {
                if (data.calls && data.calls.length > 0) {
                    showPreviousCallsModal(data.calls);
                }
            })
            .catch(error => {
                console.error('Error fetching previous calls:', error);
            });
    }
    
    function showPreviousCallsModal(calls) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Previous Calls</h3>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="this.closest('.fixed').remove()">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        ${calls.map(call => `
                            <div class="border-b border-gray-200 py-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">${call.landlord_name}</p>
                                        <p class="text-sm text-gray-600">${call.property_address}</p>
                                        <p class="text-xs text-gray-500">${call.call_datetime} • ${call.agent_name}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getOutcomeColor(call.call_outcome)}">
                                            ${call.call_outcome.replace('_', ' ')}
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">${call.call_type} • ${call.call_status}</p>
                                    </div>
                                </div>
                                ${call.agent_notes ? `<p class="text-sm text-gray-700 mt-2">${call.agent_notes}</p>` : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    function getOutcomeColor(outcome) {
        const colors = {
            'instruction_won': 'bg-green-100 text-green-800',
            'pending': 'bg-yellow-100 text-yellow-800',
            'lost': 'bg-red-100 text-red-800',
            'not_interested': 'bg-gray-100 text-gray-800'
        };
        return colors[outcome] || 'bg-gray-100 text-gray-800';
    }
    
    // Event listeners
    callStatusSelect.addEventListener('change', toggleFields);
    phoneInput.addEventListener('input', function() {
        checkPhoneNumber(this.value);
    });
    
    viewPreviousCallsBtn.addEventListener('click', function() {
        const phone = phoneInput.value;
        if (phone) {
            showPreviousCalls(phone);
        }
    });
    
    // Initialize
    toggleFields();
    if (phoneInput.value) {
        checkPhoneNumber(phoneInput.value);
    }
});

// Initialize automation actions
function initializeAutomationActions() {
    const automationCheckboxes = document.querySelectorAll('input[name^="send_"], input[name="remove_automation"]');
    
    automationCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                // Add visual feedback when automation action is selected
                this.parentElement.classList.add('bg-blue-50', 'border', 'border-blue-200', 'rounded-lg', 'p-2');
                
                // Show confirmation message
                showAutomationMessage(this.name, 'selected');
            } else {
                // Remove visual feedback
                this.parentElement.classList.remove('bg-blue-50', 'border', 'border-blue-200', 'rounded-lg', 'p-2');
            }
        });
    });
}

function showAutomationMessage(action, status) {
    const messages = {
        'send_sms': {
            selected: 'SMS will be sent to landlord after call log is saved',
            deselected: 'SMS sending cancelled'
        },
        'send_email': {
            selected: 'Email will be sent to landlord after call log is saved',
            deselected: 'Email sending cancelled'
        },
        'send_whatsapp': {
            selected: 'WhatsApp message will be sent to landlord after call log is saved',
            deselected: 'WhatsApp sending cancelled'
        },
        'remove_automation': {
            selected: 'Automation will be removed after call log is saved',
            deselected: 'Automation removal cancelled'
        }
    };
    
    const message = messages[action]?.[status];
    if (message) {
        // Create temporary notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded z-50';
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Initialize automation actions
initializeAutomationActions();
</script>
@endsection
