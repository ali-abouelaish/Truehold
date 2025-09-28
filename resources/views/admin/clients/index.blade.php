@extends('layouts.admin')

@section('page-title', 'Manage Clients')

@section('content')
<!-- Page Header -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Clients Management</h2>
        <p class="text-gray-600">Manage all client information and their details</p>
    </div>
    <a href="{{ route('admin.clients.create') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
        <i class="fas fa-plus mr-2"></i>Add New Client
    </a>
</div>

<!-- Success Message -->
@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Filters</h3>
    </div>
    <div class="p-6">
        <form method="GET" action="{{ route('admin.clients') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Agent</label>
                <select name="agent_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Agents</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                            {{ $agent->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nationality</label>
                <select name="nationality" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Nationalities</option>
                    @foreach($nationalities as $nationality)
                        <option value="{{ $nationality }}" {{ request('nationality') == $nationality ? 'selected' : '' }}>
                            {{ $nationality }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Client Type</label>
                <select name="client_type" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    <option value="student" {{ request('client_type') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="professional" {{ request('client_type') == 'professional' ? 'selected' : '' }}>Professional</option>
                    <option value="other" {{ request('client_type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Clients Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Clients List</h3>
            <div class="text-sm text-gray-500">
                Showing {{ $clients->firstItem() ?? 0 }} to {{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} results
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Client
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Contact Info
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nationality
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Agent
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($clients as $client)
                <tr class="hover:bg-gray-50 cursor-pointer" onclick="toggleClientDetails({{ $client->id }})">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $client->full_name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Age: {{ $client->age ?? 'N/A' }} ({{ $client->age_group }})
                                </div>
                            </div>
                            <div class="ml-2">
                                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="chevron-{{ $client->id }}"></i>
                            </div>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            @if($client->email)
                                <div><i class="fas fa-envelope mr-1"></i>{{ $client->email }}</div>
                            @endif
                            @if($client->phone_number)
                                <div><i class="fas fa-phone mr-1"></i>{{ $client->formatted_phone }}</div>
                            @endif
                        </div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $client->nationality ?? 'N/A' }}</div>
                        @if($client->current_address)
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ Str::limit($client->current_address, 30) }}
                            </div>
                        @endif
                    </td>
                    
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $client->client_type === 'Student' ? 'bg-green-100 text-green-800' : 
                               ($client->client_type === 'Professional' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ $client->client_type }}
                        </span>
                        @if($client->company_university_name)
                            <div class="text-xs text-gray-500 mt-1">
                                {{ Str::limit($client->company_university_name, 25) }}
                            </div>
                        @endif
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            @if($client->agent)
                                <div class="flex items-center">
                                    <i class="fas fa-user-tie mr-2 text-blue-600"></i>
                                    {{ $client->agent->display_name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $client->agent->company_name }}
                                </div>
                            @else
                                <span class="text-gray-400">No agent assigned</span>
                            @endif
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.clients.edit', $client) }}" 
                               class="text-indigo-600 hover:text-indigo-900" title="Edit" onclick="event.stopPropagation()">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" 
                                  class="inline" onsubmit="return confirm('Are you sure you want to delete this client?')" onclick="event.stopPropagation()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                
                <!-- Expandable Details Row -->
                <tr class="hidden bg-gray-50" id="details-{{ $client->id }}">
                    <td colspan="6" class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Budget & Moving Info -->
                            <div class="space-y-4">
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                        <i class="fas fa-pound-sign mr-2 text-green-600"></i>
                                        Budget & Moving
                                    </h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Budget:</span>
                                            <span class="text-sm font-medium text-gray-900">{{ $client->formatted_budget }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Moving Date:</span>
                                            <span class="text-sm font-medium text-gray-900">{{ $client->formatted_moving_date }}</span>
                                        </div>
                                        @if($client->days_until_moving !== null)
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600">Days Until Moving:</span>
                                                <span class="text-sm font-medium {{ $client->days_until_moving < 0 ? 'text-red-600' : ($client->days_until_moving < 30 ? 'text-orange-600' : 'text-green-600') }}">
                                                    {{ $client->days_until_moving }} days
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Area of Interest -->
                            <div class="space-y-4">
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                        Area of Interest
                                    </h4>
                                    <div class="text-sm text-gray-900">
                                        {{ $client->area_of_interest ?? 'Not specified' }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Notes -->
                            <div class="space-y-4 md:col-span-2 lg:col-span-1">
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                        <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>
                                        Notes
                                    </h4>
                                    <div class="text-sm text-gray-900">
                                        {{ $client->notes ?? 'No notes available' }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Details -->
                            <div class="space-y-4 md:col-span-2 lg:col-span-3">
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                        <i class="fas fa-info-circle mr-2 text-purple-600"></i>
                                        Additional Details
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <div class="text-sm text-gray-600 mb-1">Current Address:</div>
                                            <div class="text-sm text-gray-900">{{ $client->current_address ?? 'Not provided' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-600 mb-1">Company/University:</div>
                                            <div class="text-sm text-gray-900">{{ $client->company_university_name ?? 'Not provided' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-600 mb-1">Position/Role:</div>
                                            <div class="text-sm text-gray-900">{{ $client->position_role ?? 'Not provided' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-600 mb-1">Date of Birth:</div>
                                            <div class="text-sm text-gray-900">{{ $client->formatted_date_of_birth }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        <div class="py-8">
                            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                            <p class="text-lg font-medium text-gray-900">No clients found</p>
                            <p class="text-gray-500">Get started by adding your first client</p>
                            <a href="{{ route('admin.clients.create') }}" 
                               class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                Add Client
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($clients->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $clients->links() }}
    </div>
    @endif
</div>

<script>
function toggleClientDetails(clientId) {
    const detailsRow = document.getElementById(`details-${clientId}`);
    const chevron = document.getElementById(`chevron-${clientId}`);
    
    if (detailsRow.classList.contains('hidden')) {
        // Show details
        detailsRow.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
    } else {
        // Hide details
        detailsRow.classList.add('hidden');
        chevron.style.transform = 'rotate(0deg)';
    }
}

// Add smooth transitions for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Add transition classes to all detail rows
    const detailRows = document.querySelectorAll('[id^="details-"]');
    detailRows.forEach(row => {
        row.style.transition = 'all 0.3s ease-in-out';
    });
});
</script>
@endsection
