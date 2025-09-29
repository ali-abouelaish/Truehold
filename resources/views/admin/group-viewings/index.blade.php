@extends('layouts.admin')

@section('page-title', 'Group Viewings')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-gray-900">Group Viewings</h2>
    <a href="{{ route('admin.group-viewings.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
        <i class="fas fa-plus mr-2"></i>Create Group Viewing
    </a>
    </div>

@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendees</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($groupViewings as $gv)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $gv->property->title ?? 'Property #'.$gv->property_id }}</div>
                            <div class="text-sm text-gray-500">{{ $gv->property->location ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $gv->scheduled_at->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $gv->attendees->count() }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex space-x-2">
                                <button onclick="showAttendees('{{ $gv->id }}', '{{ $gv->property->title ?? 'Property #'.$gv->property_id }}', '{{ $gv->scheduled_at->format('Y-m-d H:i') }}')" 
                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-users mr-1"></i>View Attendees
                                </button>
                                <a href="{{ route('admin.group-viewings.create', ['property_id' => $gv->property_id]) }}" class="text-green-600 hover:text-green-800 text-sm">
                                    <i class="fas fa-plus mr-1"></i>New for this property
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-gray-500">No group viewings yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4">
        {{ $groupViewings->links() }}
    </div>
    </div>

    <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Rooms with Interested Clients</h3>
            <span class="text-sm text-gray-600">{{ isset($propertiesWithInterests) ? $propertiesWithInterests->count() : 0 }} properties</span>
        </div>
        <div class="divide-y divide-gray-200">
            @isset($propertiesWithInterests)
            @forelse($propertiesWithInterests as $prop)
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-base font-semibold text-gray-900">{{ $prop->title ?? 'Property #'.$prop->id }}</div>
                            <div class="text-sm text-gray-500">{{ $prop->location }}</div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                <i class="fas fa-user-friends mr-2"></i>{{ $prop->interests_count }} interested
                            </span>
                            <button type="button" class="text-blue-600 hover:text-blue-800 text-sm" onclick="toggleDetails('prop-{{ $prop->id }}')">
                                Details
                            </button>
                            <a href="{{ route('admin.group-viewings.create', ['property_id' => $prop->id]) }}" class="text-green-600 hover:text-green-800 text-sm">Create Viewing</a>
                        </div>
                    </div>
                    <div id="prop-{{ $prop->id }}" class="mt-4 hidden">
                        <ul class="divide-y divide-gray-200">
                            @foreach($prop->interests as $interest)
                                <li class="py-3 flex items-start justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $interest->client->full_name }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $interest->client->email }} 
                                            @if($interest->client->phone_number) 
                                                • <span class="phone-number cursor-pointer text-blue-600 hover:text-blue-800 underline" 
                                                       onclick="showPhonePreview('{{ $interest->client->phone_number }}', '{{ $interest->client->full_name }}')"
                                                       title="Click to preview phone number">
                                                    {{ $interest->client->phone_number }}
                                                </span>
                                            @endif
                                        </p>
                                        @if($interest->notes)
                                            <p class="text-sm text-gray-500 mt-1">Notes: {{ $interest->notes }}</p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-1">Added {{ $interest->created_at->diffForHumans() }}</p>
                                    </div>
                                    <a href="{{ route('admin.group-viewings.create', ['property_id' => $prop->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">Include</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">No interested clients found.</div>
            @endforelse
            @endisset
        </div>
    </div>
</div>

<!-- Phone Preview Modal -->
<div id="phonePreviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Phone Number Preview</h3>
            </div>
            <div class="px-6 py-4">
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-phone text-3xl text-blue-600 mb-2"></i>
                        <p class="text-sm text-gray-600">Client: <span id="clientName" class="font-medium text-gray-900"></span></p>
                    </div>
                    <div class="mb-6">
                        <p class="text-2xl font-mono font-bold text-gray-900" id="phoneNumber"></p>
                        <p class="text-sm text-gray-500 mt-1">Click to copy or call</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="copyPhoneNumber()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                            <i class="fas fa-copy mr-2"></i>Copy
                        </button>
                        <button onclick="callPhoneNumber()" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                            <i class="fas fa-phone mr-2"></i>Call
                        </button>
                        <button onclick="closePhonePreview()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                            <i class="fas fa-times mr-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendees Modal -->
<div id="attendeesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Group Viewing Attendees</h3>
                    <p class="text-sm text-gray-600" id="viewingDetails"></p>
                </div>
                <button onclick="closeAttendees()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="px-6 py-4 overflow-y-auto max-h-96">
                <div id="attendeesList" class="space-y-3">
                    <!-- Attendees will be loaded here -->
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600" id="attendeesCount">0 attendees</span>
                    <button onclick="closeAttendees()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentPhoneNumber = '';

function toggleDetails(id) {
    var el = document.getElementById(id);
    if (!el) return;
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
    } else {
        el.classList.add('hidden');
    }
}

function showPhonePreview(phoneNumber, clientName) {
    currentPhoneNumber = phoneNumber;
    document.getElementById('phoneNumber').textContent = phoneNumber;
    document.getElementById('clientName').textContent = clientName;
    document.getElementById('phonePreviewModal').classList.remove('hidden');
}

function closePhonePreview() {
    document.getElementById('phonePreviewModal').classList.add('hidden');
    currentPhoneNumber = '';
}

function copyPhoneNumber() {
    if (currentPhoneNumber) {
        navigator.clipboard.writeText(currentPhoneNumber).then(function() {
            // Show success feedback
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
            button.classList.add('bg-green-600', 'hover:bg-green-700');
            button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600', 'hover:bg-green-700');
                button.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }, 2000);
        }).catch(function(err) {
            console.error('Could not copy text: ', err);
            alert('Failed to copy phone number');
        });
    }
}

function callPhoneNumber() {
    if (currentPhoneNumber) {
        // Remove any non-digit characters except + for international numbers
        const cleanNumber = currentPhoneNumber.replace(/[^\d+]/g, '');
        window.open(`tel:${cleanNumber}`, '_self');
    }
}

// Close modal when clicking outside
document.getElementById('phonePreviewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePhonePreview();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhonePreview();
        closeAttendees();
    }
});

// Attendees modal functions
function showAttendees(viewingId, propertyTitle, scheduledAt) {
    document.getElementById('viewingDetails').textContent = `${propertyTitle} - ${scheduledAt}`;
    document.getElementById('attendeesModal').classList.remove('hidden');
    
    // Load attendees via AJAX
    loadAttendees(viewingId);
}

function closeAttendees() {
    document.getElementById('attendeesModal').classList.add('hidden');
    document.getElementById('attendeesList').innerHTML = '';
    document.getElementById('attendeesCount').textContent = '0 attendees';
}

function loadAttendees(viewingId) {
    // Show loading state
    document.getElementById('attendeesList').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
            <p class="text-gray-500">Loading attendees...</p>
        </div>
    `;
    
    // Fetch attendees data
    fetch(`/admin/group-viewings/${viewingId}/attendees`)
        .then(response => response.json())
        .then(data => {
            displayAttendees(data.attendees || []);
        })
        .catch(error => {
            console.error('Error loading attendees:', error);
            document.getElementById('attendeesList').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-400 mb-2"></i>
                    <p class="text-red-500">Error loading attendees</p>
                </div>
            `;
        });
}

function displayAttendees(attendees) {
    const container = document.getElementById('attendeesList');
    const count = attendees.length;
    
    document.getElementById('attendeesCount').textContent = `${count} attendee${count !== 1 ? 's' : ''}`;
    
    if (attendees.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-users text-2xl text-gray-400 mb-2"></i>
                <p class="text-gray-500">No attendees yet</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = attendees.map(attendee => `
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">${attendee.client.full_name}</h4>
                            <p class="text-sm text-gray-600">${attendee.client.email}</p>
                            ${attendee.client.phone_number ? `
                                <p class="text-sm text-gray-600 mt-1">
                                    <span class="phone-number cursor-pointer text-blue-600 hover:text-blue-800 underline" 
                                          onclick="showPhonePreview('${attendee.client.phone_number}', '${attendee.client.full_name}')"
                                          title="Click to preview phone number">
                                        <i class="fas fa-phone mr-1"></i>${attendee.client.phone_number}
                                    </span>
                                </p>
                            ` : ''}
                            ${attendee.notes ? `<p class="text-sm text-gray-500 mt-1">Notes: ${attendee.notes}</p>` : ''}
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
                        attendee.status === 'confirmed' ? 'bg-green-100 text-green-800' :
                        attendee.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                        'bg-gray-100 text-gray-800'
                    }">
                        ${attendee.status || 'pending'}
                    </span>
                </div>
            </div>
        </div>
    `).join('');
}

// Close attendees modal when clicking outside
document.getElementById('attendeesModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAttendees();
    }
});
</script>
@endsection
