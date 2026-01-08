@extends('layouts.admin')

@section('page-title', 'Property Flags Management')

@section('content')
<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Property Flags Management</h2>
            <p class="text-gray-600">Manage promotional flags and badges for properties</p>
        </div>
        <a href="{{ route('admin.properties') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Properties
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

<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('admin.properties.flags') }}" class="flex gap-4 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search by title or location..." 
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <select name="filter" class="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Properties</option>
                <option value="with_flag" {{ request('filter') == 'with_flag' ? 'selected' : '' }}>With Flag</option>
                <option value="no_flag" {{ request('filter') == 'no_flag' ? 'selected' : '' }}>No Flag</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
            <i class="fas fa-search mr-2"></i>Search
        </button>
        @if(request()->hasAny(['search', 'filter']))
            <a href="{{ route('admin.properties.flags') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium">
                <i class="fas fa-times mr-2"></i>Clear
            </a>
        @endif
    </form>
</div>

<!-- Available Flag Colors -->
<div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-lg border-2 border-yellow-200 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
        <i class="fas fa-palette mr-2 text-yellow-600"></i>Available Flag Colors
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded" style="background: linear-gradient(135deg, #f59e0b, #d97706);"></div>
            <span class="text-sm font-medium">Orange</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded" style="background: linear-gradient(135deg, #ef4444, #dc2626);"></div>
            <span class="text-sm font-medium">Red (Hot)</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded" style="background: linear-gradient(135deg, #10b981, #059669);"></div>
            <span class="text-sm font-medium">Green</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded" style="background: linear-gradient(135deg, #3b82f6, #2563eb);"></div>
            <span class="text-sm font-medium">Blue</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);"></div>
            <span class="text-sm font-medium">Purple</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded" style="background: linear-gradient(135deg, #ec4899, #db2777);"></div>
            <span class="text-sm font-medium">Pink</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded" style="background: linear-gradient(135deg, #14b8a6, #0d9488);"></div>
            <span class="text-sm font-medium">Teal</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded" style="background: linear-gradient(135deg, #d4af37, #b8941f);"></div>
            <span class="text-sm font-medium">Gold</span>
        </div>
    </div>
</div>

<!-- Properties Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Properties ({{ $properties->total() }})</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Flag</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flag Text</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flag Color</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($properties as $property)
                <tr id="property-row-{{ $property->id }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12">
                                @if($property->first_photo_url)
                                    <img class="h-12 w-12 rounded object-cover" src="{{ $property->first_photo_url }}" alt="">
                                @else
                                    <div class="h-12 w-12 rounded bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-home text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($property->title, 50) }}</div>
                                <div class="text-sm text-gray-500">{{ $property->location }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div id="flag-preview-{{ $property->id }}">
                            @if($property->flag)
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold text-white shadow-sm" 
                                      style="background: {{ $property->flag_color ?: 'linear-gradient(135deg, #d4af37, #b8941f)' }}">
                                    {{ $property->flag }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400 italic">No flag</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" id="flag-text-{{ $property->id }}" value="{{ $property->flag }}" 
                               placeholder="e.g., Premium, Hot Deal" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </td>
                    <td class="px-6 py-4">
                        <select id="flag-color-{{ $property->id }}" 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Default (Gold)</option>
                            <option value="linear-gradient(135deg, #f59e0b, #d97706)" {{ $property->flag_color == 'linear-gradient(135deg, #f59e0b, #d97706)' ? 'selected' : '' }}>🟠 Orange</option>
                            <option value="linear-gradient(135deg, #ef4444, #dc2626)" {{ $property->flag_color == 'linear-gradient(135deg, #ef4444, #dc2626)' ? 'selected' : '' }}>🔴 Red</option>
                            <option value="linear-gradient(135deg, #10b981, #059669)" {{ $property->flag_color == 'linear-gradient(135deg, #10b981, #059669)' ? 'selected' : '' }}>🟢 Green</option>
                            <option value="linear-gradient(135deg, #3b82f6, #2563eb)" {{ $property->flag_color == 'linear-gradient(135deg, #3b82f6, #2563eb)' ? 'selected' : '' }}>🔵 Blue</option>
                            <option value="linear-gradient(135deg, #8b5cf6, #7c3aed)" {{ $property->flag_color == 'linear-gradient(135deg, #8b5cf6, #7c3aed)' ? 'selected' : '' }}>🟣 Purple</option>
                            <option value="linear-gradient(135deg, #ec4899, #db2777)" {{ $property->flag_color == 'linear-gradient(135deg, #ec4899, #db2777)' ? 'selected' : '' }}>🌸 Pink</option>
                            <option value="linear-gradient(135deg, #14b8a6, #0d9488)" {{ $property->flag_color == 'linear-gradient(135deg, #14b8a6, #0d9488)' ? 'selected' : '' }}>🩵 Teal</option>
                            <option value="linear-gradient(135deg, #d4af37, #b8941f)" {{ $property->flag_color == 'linear-gradient(135deg, #d4af37, #b8941f)' ? 'selected' : '' }}>⭐ Gold</option>
                        </select>
                    </td>
                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                        <button onclick="updateFlag({{ $property->id }})" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                            <i class="fas fa-save mr-1"></i>Save
                        </button>
                        <button onclick="clearFlag({{ $property->id }})" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium text-sm ml-2">
                            <i class="fas fa-times mr-1"></i>Clear
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-flag text-4xl text-gray-300 mb-3"></i>
                        <p class="text-lg font-medium">No properties found</p>
                        <p class="text-sm mt-2">Try adjusting your search or filters</p>
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

<script>
    function updateFlag(propertyId) {
        const flagText = document.getElementById(`flag-text-${propertyId}`).value;
        const flagColor = document.getElementById(`flag-color-${propertyId}`).value;
        
        fetch(`/admin/properties/${propertyId}/update-flag`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                flag: flagText,
                flag_color: flagColor
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update preview
                const preview = document.getElementById(`flag-preview-${propertyId}`);
                if (flagText) {
                    preview.innerHTML = `<span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold text-white shadow-sm" style="background: ${flagColor || 'linear-gradient(135deg, #d4af37, #b8941f)'}">${flagText}</span>`;
                } else {
                    preview.innerHTML = '<span class="text-sm text-gray-400 italic">No flag</span>';
                }
                
                // Show success message
                showNotification('success', 'Flag updated successfully!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Failed to update flag');
        });
    }
    
    function clearFlag(propertyId) {
        document.getElementById(`flag-text-${propertyId}`).value = '';
        document.getElementById(`flag-color-${propertyId}`).value = '';
        updateFlag(propertyId);
    }
    
    function showNotification(type, message) {
        const color = type === 'success' ? 'green' : 'red';
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 bg-${color}-50 border border-${color}-200 rounded-lg p-4 shadow-lg z-50`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} text-${color}-400 mr-2"></i>
                <span class="text-${color}-800 font-medium">${message}</span>
            </div>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
</script>
@endsection

