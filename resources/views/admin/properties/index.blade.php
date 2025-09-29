@extends('layouts.admin')

@section('page-title', 'Manage Properties')

@section('content')
<!-- Page Header -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Properties Management</h2>
        <p class="text-gray-600">Manage all property listings and their details</p>
    </div>
    <a href="{{ route('admin.properties.create') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
        <i class="fas fa-plus mr-2"></i>Add New Property
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
        <form method="GET" action="{{ route('admin.properties') }}" class="space-y-4">
            <!-- First Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Rented</option>
                        <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                        <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <select name="location" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Locations</option>
                        @foreach($locations ?? [] as $location)
                            <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                {{ $location }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                    <select name="property_type" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        @foreach($propertyTypes ?? [] as $type)
                            <option value="{{ $type }}" {{ request('property_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Management Company</label>
                    <select name="management_company" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Companies</option>
                        @foreach($managementCompanies ?? [] as $company)
                            <option value="{{ $company }}" {{ request('management_company') == $company ? 'selected' : '' }}>
                                {{ $company }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Second Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Agent Name</label>
                    <select name="agent_name" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Agents</option>
                        @foreach($agentNames ?? [] as $agent)
                            <option value="{{ $agent }}" {{ request('agent_name') == $agent ? 'selected' : '' }}>
                                {{ $agent }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Price (£)</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="e.g. 500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Price (£)</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="e.g. 2000">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">London Area</label>
                    <select name="london_area" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Areas</option>
                        <option value="central" {{ request('london_area') == 'central' ? 'selected' : '' }}>Central</option>
                        <option value="north" {{ request('london_area') == 'north' ? 'selected' : '' }}>North</option>
                        <option value="south" {{ request('london_area') == 'south' ? 'selected' : '' }}>South</option>
                        <option value="east" {{ request('london_area') == 'east' ? 'selected' : '' }}>East</option>
                        <option value="west" {{ request('london_area') == 'west' ? 'selected' : '' }}>West</option>
                    </select>
                </div>
            </div>
            
            <!-- Third Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Couples Allowed</label>
                    <select name="couples_allowed" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Properties</option>
                        <option value="yes" {{ request('couples_allowed') == 'yes' ? 'selected' : '' }}>Yes</option>
                        <option value="no" {{ request('couples_allowed') == 'no' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Search properties...">
                </div>
                
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('admin.properties') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium text-center">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

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
