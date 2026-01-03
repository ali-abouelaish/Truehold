@extends('layouts.admin')

@section('page-title', 'Manage Properties')

@section('content')
<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Properties Management</h2>
        <p class="text-gray-600">Manage all property listings and their details</p>
        </div>
        <a href="{{ route('properties.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            <i class="fas fa-plus mr-2"></i>Add New Property
        </a>
    </div>
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

<!-- Comprehensive Filters -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4">
    <div class="px-4 py-3 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Advanced Filters</h3>
            <div class="flex items-center space-x-2">
                <button type="button" onclick="toggleFilters()" class="text-sm text-blue-600 hover:text-blue-800">
                    <i class="fas fa-chevron-down" id="filterToggle"></i> Toggle Filters
                </button>
                <span class="text-xs text-gray-500" id="activeFiltersCount">0 active filters</span>
            </div>
        </div>
    </div>
    <div class="p-4" id="filterContent" style="display: none;">
        <form method="GET" action="{{ route('admin.properties') }}" class="space-y-3">
            <!-- Primary Filters Row -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Rented</option>
                        <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                        <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Property Type</label>
                    <select name="property_type" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        @foreach($propertyTypes ?? [] as $type)
                            <option value="{{ $type }}" {{ request('property_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Location</label>
                    <select name="location" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Locations</option>
                        @foreach($locations ?? [] as $location)
                            <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                {{ $location }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Management Company</label>
                    <select name="management_company" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Companies</option>
                        @foreach($managementCompanies ?? [] as $company)
                            <option value="{{ $company }}" {{ request('management_company') == $company ? 'selected' : '' }}>
                                {{ $company }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Agent</label>
                    <select name="agent_name" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Agents</option>
                        @foreach($agentNames ?? [] as $agent)
                            <option value="{{ $agent }}" {{ request('agent_name') == $agent ? 'selected' : '' }}>
                                {{ $agent }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Search properties...">
                </div>
            </div>
            
            <!-- Price & Area Filters Row -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Min Price (£)</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                           class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="500">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Max Price (£)</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                           class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="2000">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">London Area</label>
                    <select name="london_area" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Areas</option>
                        <option value="central" {{ request('london_area') == 'central' ? 'selected' : '' }}>Central</option>
                        <option value="north" {{ request('london_area') == 'north' ? 'selected' : '' }}>North</option>
                        <option value="south" {{ request('london_area') == 'south' ? 'selected' : '' }}>South</option>
                        <option value="east" {{ request('london_area') == 'east' ? 'selected' : '' }}>East</option>
                        <option value="west" {{ request('london_area') == 'west' ? 'selected' : '' }}>West</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Couples Allowed</label>
                    <select name="couples_allowed" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Properties</option>
                        <option value="yes" {{ request('couples_allowed') == 'yes' ? 'selected' : '' }}>Yes</option>
                        <option value="no" {{ request('couples_allowed') == 'no' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Beds</label>
                    <select name="beds" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Any</option>
                        <option value="1" {{ request('beds') == '1' ? 'selected' : '' }}>1 Bed</option>
                        <option value="2" {{ request('beds') == '2' ? 'selected' : '' }}>2 Beds</option>
                        <option value="3" {{ request('beds') == '3' ? 'selected' : '' }}>3 Beds</option>
                        <option value="4+" {{ request('beds') == '4+' ? 'selected' : '' }}>4+ Beds</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bathrooms</label>
                    <select name="bathrooms" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Any</option>
                        <option value="1" {{ request('bathrooms') == '1' ? 'selected' : '' }}>1 Bath</option>
                        <option value="2" {{ request('bathrooms') == '2' ? 'selected' : '' }}>2 Baths</option>
                        <option value="3+" {{ request('bathrooms') == '3+' ? 'selected' : '' }}>3+ Baths</option>
                    </select>
                </div>
            </div>
            
            <!-- Additional Filters Row -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Date Added</label>
                    <select name="date_added" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Any Time</option>
                        <option value="today" {{ request('date_added') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ request('date_added') == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('date_added') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="3months" {{ request('date_added') == '3months' ? 'selected' : '' }}>Last 3 Months</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Available Date</label>
                    <input type="date" name="available_date" value="{{ request('available_date') }}" 
                           class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Has Photos</label>
                    <select name="has_photos" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="yes" {{ request('has_photos') == 'yes' ? 'selected' : '' }}>With Photos</option>
                        <option value="no" {{ request('has_photos') == 'no' ? 'selected' : '' }}>No Photos</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Updatable</label>
                    <select name="updatable" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="yes" {{ request('updatable') == 'yes' ? 'selected' : '' }}>Updatable</option>
                        <option value="no" {{ request('updatable') == 'no' ? 'selected' : '' }}>Not Updatable</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort_by" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                        <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                        <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Title</option>
                        <option value="location" {{ request('sort_by') == 'location' ? 'selected' : '' }}>Location</option>
                        <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Order</label>
                    <select name="order" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-filter mr-1"></i>Apply Filters
                    </button>
                    <a href="{{ route('admin.properties') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-times mr-1"></i>Clear All
                    </a>
                    <button type="button" onclick="saveFilterPreset()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-save mr-1"></i>Save Preset
                    </button>
                </div>
                <div class="text-xs text-gray-500">
                    <span id="filterResultsCount">{{ $properties->total() }} properties found</span>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleFilters() {
    const content = document.getElementById('filterContent');
    const toggle = document.getElementById('filterToggle');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        toggle.className = 'fas fa-chevron-up';
    } else {
        content.style.display = 'none';
        toggle.className = 'fas fa-chevron-down';
    }
}

function updateActiveFiltersCount() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input, select');
    let activeCount = 0;
    
    inputs.forEach(input => {
        if (input.value && input.value !== '') {
            activeCount++;
        }
    });
    
    document.getElementById('activeFiltersCount').textContent = activeCount + ' active filters';
}

function saveFilterPreset() {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    const presetName = prompt('Enter a name for this filter preset:');
    
    if (presetName) {
        // Save to localStorage
        const preset = {};
        for (let [key, value] of formData.entries()) {
            if (value) preset[key] = value;
        }
        localStorage.setItem('filter_preset_' + presetName.toLowerCase().replace(/\s+/g, '_'), JSON.stringify(preset));
        alert('Filter preset saved!');
    }
}

// Update filter count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateActiveFiltersCount();
    
    // Add event listeners to all form inputs
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('change', updateActiveFiltersCount);
    });
});
</script>

<!-- Properties Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Properties List</h3>
            <div class="text-sm text-gray-500">
                Showing {{ $properties->firstItem() ?? 0 }} to {{ $properties->lastItem() ?? 0 }} of {{ $properties->total() }} results
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Property
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Location
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Price
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Management
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Agent
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Photos
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Updatable
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($properties as $property)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                @if($property->first_photo_url)
                                    <img src="{{ $property->first_photo_url }}" alt="Property" class="w-12 h-12 rounded-lg object-cover">
                                @else
                                    <i class="fas fa-home text-gray-400"></i>
                                @endif
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ Str::limit($property->title, 50) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $property->property_type ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $property->location }}</div>
                        @if($property->latitude && $property->longitude)
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $property->latitude }}, {{ $property->longitude }}
                            </div>
                        @endif
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">£{{ number_format($property->price) }}</div>
                        @if($property->available_date)
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $property->available_date }}
                            </div>
                        @endif
                    </td>
                    
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $property->status === 'available' ? 'bg-green-100 text-green-800' : 
                               ($property->status === 'rented' ? 'bg-red-100 text-red-800' : 
                               ($property->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($property->status ?? 'Available') }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            {{ Str::limit($property->management_company ?? 'N/A', 20) }}
                        </div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            {{ Str::limit($property->agent_name ?? 'N/A', 20) }}
                        </div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            <i class="fas fa-images mr-1"></i>
                            {{ $property->photo_count ?? 0 }} photos
                        </div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <form method="POST" action="{{ route('admin.properties.toggle-updatable', $property) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors duration-200 {{ $property->updatable ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}"
                                    title="{{ $property->updatable ? 'Click to disable updates' : 'Click to enable updates' }}">
                                <i class="fas {{ $property->updatable ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                {{ $property->updatable ? 'Yes' : 'No' }}
                            </button>
                        </form>
                    </td>
                    
                    <td class="px-6 py-4 text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('properties.show', $property) }}" 
                               class="text-blue-600 hover:text-blue-900" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.properties.edit', $property) }}" 
                               class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.properties.destroy', $property) }}" 
                                  class="inline" onsubmit="return confirm('Are you sure you want to delete this property?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        <div class="py-8">
                            <i class="fas fa-home text-4xl text-gray-300 mb-4"></i>
                            <p class="text-lg font-medium text-gray-900">No properties found</p>
                            <p class="text-gray-500">Get started by adding your first property</p>
                            <a href="{{ route('admin.properties.create') }}" 
                               class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                Add Property
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($properties->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $properties->links() }}
    </div>
    @endif
</div>
@endsection
