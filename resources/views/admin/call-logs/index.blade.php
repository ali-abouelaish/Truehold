@extends('layouts.admin')

@section('page-title', 'Call Logs')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Call Logs</h1>
        <a href="{{ route('admin.call-logs.create') }}" class="font-bold py-2 px-4 rounded transition-colors"
           style="background: linear-gradient(135deg, #1f2937, #374151); border: 1px solid #fbbf24; color: #fbbf24;"
           onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.color='#1f2937';"
           onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.color='#fbbf24';">
            Add New Call Log
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Filters</h2>
        <form method="GET" action="{{ route('admin.call-logs.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Agent</label>
                <select name="agent_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">All Agents</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                            {{ $agent->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="landlord_phone" value="{{ request('landlord_phone') }}" 
                       placeholder="Search by phone number" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Call Status</label>
                <select name="call_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">All Statuses</option>
                    <option value="connected" {{ request('call_status') == 'connected' ? 'selected' : '' }}>Connected</option>
                    <option value="no_answer" {{ request('call_status') == 'no_answer' ? 'selected' : '' }}>No Answer</option>
                    <option value="wrong_number" {{ request('call_status') == 'wrong_number' ? 'selected' : '' }}>Wrong Number</option>
                    <option value="voicemail" {{ request('call_status') == 'voicemail' ? 'selected' : '' }}>Voicemail</option>
                    <option value="callback_requested" {{ request('call_status') == 'callback_requested' ? 'selected' : '' }}>Callback Requested</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Call Outcome</label>
                <select name="call_outcome" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">All Outcomes</option>
                    <option value="instruction_won" {{ request('call_outcome') == 'instruction_won' ? 'selected' : '' }}>Instruction Won</option>
                    <option value="pending" {{ request('call_outcome') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="lost" {{ request('call_outcome') == 'lost' ? 'selected' : '' }}>Lost</option>
                    <option value="not_interested" {{ request('call_outcome') == 'not_interested' ? 'selected' : '' }}>Not Interested</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Filter
                </button>
                <a href="{{ route('admin.call-logs.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded ml-2">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Call Logs Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Landlord</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beds</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bathrooms</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Step</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outcome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($callLogs as $callLog)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $callLog->call_datetime->format('M j, Y g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $callLog->agent->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $callLog->landlord_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $callLog->landlord_phone ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ Str::limit($callLog->property_address, 30) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                £{{ number_format($callLog->advertised_rent ?? 0, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $callLog->number_of_beds ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $callLog->number_of_bathrooms ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <select class="next-step-status border-gray-300 rounded-md text-sm w-40" 
                                        data-call-log-id="{{ $callLog->id }}"
                                        onchange="updateNextStepStatus({{ $callLog->id }}, this.value)">
                                    <option value="">Select Next Step</option>
                                    <option value="send_terms" {{ $callLog->next_step_status == 'send_terms' ? 'selected' : '' }}>Send Terms</option>
                                    <option value="send_compliance_docs" {{ $callLog->next_step_status == 'send_compliance_docs' ? 'selected' : '' }}>Send Compliance Docs</option>
                                    <option value="awaiting_response" {{ $callLog->next_step_status == 'awaiting_response' ? 'selected' : '' }}>Awaiting Response</option>
                                    <option value="collect_keys" {{ $callLog->next_step_status == 'collect_keys' ? 'selected' : '' }}>Collect Keys</option>
                                    <option value="tenant_reference_started" {{ $callLog->next_step_status == 'tenant_reference_started' ? 'selected' : '' }}>Tenant Reference Started</option>
                                    <option value="other" {{ $callLog->next_step_status == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $callLog->call_status === 'connected' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $callLog->call_status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $callLog->call_outcome === 'instruction_won' ? 'bg-green-100 text-green-800' : 
                                       ($callLog->call_outcome === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $callLog->call_outcome)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.call-logs.show', $callLog) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                <a href="{{ route('admin.call-logs.edit', $callLog) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <form method="POST" action="{{ route('admin.call-logs.destroy', $callLog) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this call log?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No call logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">{{ $callLogs->count() }}</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Calls</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">{{ $callLogs->where('call_outcome', 'instruction_won')->count() }}</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Instructions Won</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">{{ $callLogs->where('call_outcome', 'pending')->count() }}</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">{{ $callLogs->where('follow_up_needed', true)->count() }}</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Follow-ups Needed</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateNextStepStatus(callLogId, nextStepStatus) {
    // Show loading state
    const select = document.querySelector(`select[data-call-log-id="${callLogId}"]`);
    select.disabled = true;
    select.classList.add('opacity-50');
    
    // Send AJAX request
    fetch(`/admin/call-logs/${callLogId}/update-next-step`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'include',
        body: JSON.stringify({
            next_step_status: nextStepStatus
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Show success message
        showNotification('Next step status updated successfully', 'success');
        
        // Re-enable select
        select.disabled = false;
        select.classList.remove('opacity-50');
    })
    .catch(error => {
        console.error('Error updating next step status:', error);
        
        // Show error message
        showNotification('Error updating next step status', 'error');
        
        // Re-enable select
        select.disabled = false;
        select.classList.remove('opacity-50');
        
        // Revert the value
        select.value = select.getAttribute('data-original-value') || '';
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-3 rounded z-50 ${
        type === 'success' 
            ? 'bg-green-100 border border-green-400 text-green-700' 
            : 'bg-red-100 border border-red-400 text-red-700'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Store original values for revert functionality
document.addEventListener('DOMContentLoaded', function() {
    const nextStepSelects = document.querySelectorAll('.next-step-status');
    nextStepSelects.forEach(select => {
        select.setAttribute('data-original-value', select.value);
    });
});
</script>
@endsection
