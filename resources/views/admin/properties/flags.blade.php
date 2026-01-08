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

<!-- Troubleshooting Info -->
<div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-400 text-lg"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-semibold text-blue-900 mb-1">📊 Updates are saved to Google Sheets</h3>
            <p class="text-xs text-blue-700 mb-2">
                Flag changes are stored directly in your Google Sheets properties spreadsheet.
            </p>
            <details class="text-xs text-blue-700">
                <summary class="cursor-pointer font-medium hover:text-blue-900">
                    <i class="fas fa-wrench mr-1"></i>Troubleshooting failed updates
                </summary>
                <ul class="mt-2 space-y-1 ml-4 list-disc">
                    <li><strong>Property ID not found:</strong> The property may have been deleted from the sheet</li>
                    <li><strong>Missing columns:</strong> The system will automatically add 'flag' and 'flag_color' columns if missing</li>
                    <li><strong>Permission error:</strong> Check Google Sheets API permissions in your service account</li>
                    <li><strong>Rate limiting:</strong> Updating many properties at once may hit API limits (try smaller batches)</li>
                    <li><strong>Check logs:</strong> Run <code class="bg-blue-100 px-1 rounded">tail -f storage/logs/laravel.log</code> for detailed errors</li>
                </ul>
            </details>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('admin.properties.flags') }}" class="space-y-4">
        <div class="flex gap-4 flex-wrap">
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
            <div>
                <select name="agent" class="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 min-w-[180px]">
                    <option value="">All Agents</option>
                    @foreach($agentNames as $agentName)
                        @if($agentName && $agentName !== 'N/A')
                            <option value="{{ $agentName }}" {{ request('agent') == $agentName ? 'selected' : '' }}>
                                {{ $agentName }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                <i class="fas fa-search mr-2"></i>Search
            </button>
            @if(request()->hasAny(['search', 'filter', 'agent']))
                <a href="{{ route('admin.properties.flags') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Bulk Actions -->
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border-2 border-blue-200 p-6 mb-6" id="bulkActionsBar" style="display: none;">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-blue-600 text-2xl mr-3"></i>
                <div>
                    <p class="text-sm font-medium text-gray-700">Bulk Actions</p>
                    <p class="text-xs text-gray-500"><span id="selectedCount">0</span> properties selected</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <select id="bulkFlagText" class="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Flag...</option>
                <option value="Premium">Premium</option>
                <option value="Hot Deal">Hot Deal</option>
                <option value="Featured">Featured</option>
                <option value="New">New</option>
                <option value="Sale">Sale</option>
                <option value="Discount">Discount</option>
                <option value="Limited">Limited</option>
                <option value="Exclusive">Exclusive</option>
            </select>
            <select id="bulkFlagColor" class="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Default (Gold)</option>
                <option value="linear-gradient(135deg, #f59e0b, #d97706)">🟠 Orange</option>
                <option value="linear-gradient(135deg, #ef4444, #dc2626)">🔴 Red</option>
                <option value="linear-gradient(135deg, #10b981, #059669)">🟢 Green</option>
                <option value="linear-gradient(135deg, #3b82f6, #2563eb)">🔵 Blue</option>
                <option value="linear-gradient(135deg, #8b5cf6, #7c3aed)">🟣 Purple</option>
                <option value="linear-gradient(135deg, #ec4899, #db2777)">🌸 Pink</option>
                <option value="linear-gradient(135deg, #14b8a6, #0d9488)">🩵 Teal</option>
                <option value="linear-gradient(135deg, #d4af37, #b8941f)">⭐ Gold</option>
            </select>
            <button onclick="applyBulkFlag()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium">
                <i class="fas fa-check mr-2"></i>Apply to Selected
            </button>
            <button onclick="clearBulkFlag()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium">
                <i class="fas fa-trash mr-2"></i>Remove Flags
            </button>
            <button onclick="deselectAll()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-times mr-2"></i>Deselect All
            </button>
        </div>
    </div>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" id="selectAll" onclick="toggleSelectAll()" 
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Flag</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flag Text</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flag Color</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($properties as $property)
                @php
                    $propertyId = $property['id'] ?? uniqid();
                    $propertyTitle = $property['title'] ?? 'Untitled';
                    $propertyLocation = $property['location'] ?? 'Unknown';
                    $propertyFlag = $property['flag'] ?? '';
                    $propertyFlagColor = $property['flag_color'] ?? '';
                    $propertyPhoto = $property['first_photo_url'] ?? '';
                @endphp
                <tr id="property-row-{{ $propertyId }}">
                    <td class="px-6 py-4 text-center">
                        <input type="checkbox" class="property-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" 
                               value="{{ $propertyId }}" onchange="updateBulkActionsBar()">
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12">
                                @if($propertyPhoto && $propertyPhoto !== 'N/A')
                                    <img class="h-12 w-12 rounded object-cover" src="{{ $propertyPhoto }}" alt="">
                                @else
                                    <div class="h-12 w-12 rounded bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-home text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($propertyTitle, 50) }}</div>
                                <div class="text-sm text-gray-500">{{ $propertyLocation }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div id="flag-preview-{{ $propertyId }}">
                            @if($propertyFlag)
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold text-white shadow-sm" 
                                      style="background: {{ $propertyFlagColor ?: 'linear-gradient(135deg, #d4af37, #b8941f)' }}">
                                    {{ $propertyFlag }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400 italic">No flag</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" id="flag-text-{{ $propertyId }}" value="{{ $propertyFlag }}" 
                               placeholder="e.g., Premium, Hot Deal" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </td>
                    <td class="px-6 py-4">
                        <select id="flag-color-{{ $propertyId }}" 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Default (Gold)</option>
                            <option value="linear-gradient(135deg, #f59e0b, #d97706)" {{ $propertyFlagColor == 'linear-gradient(135deg, #f59e0b, #d97706)' ? 'selected' : '' }}>🟠 Orange</option>
                            <option value="linear-gradient(135deg, #ef4444, #dc2626)" {{ $propertyFlagColor == 'linear-gradient(135deg, #ef4444, #dc2626)' ? 'selected' : '' }}>🔴 Red</option>
                            <option value="linear-gradient(135deg, #10b981, #059669)" {{ $propertyFlagColor == 'linear-gradient(135deg, #10b981, #059669)' ? 'selected' : '' }}>🟢 Green</option>
                            <option value="linear-gradient(135deg, #3b82f6, #2563eb)" {{ $propertyFlagColor == 'linear-gradient(135deg, #3b82f6, #2563eb)' ? 'selected' : '' }}>🔵 Blue</option>
                            <option value="linear-gradient(135deg, #8b5cf6, #7c3aed)" {{ $propertyFlagColor == 'linear-gradient(135deg, #8b5cf6, #7c3aed)' ? 'selected' : '' }}>🟣 Purple</option>
                            <option value="linear-gradient(135deg, #ec4899, #db2777)" {{ $propertyFlagColor == 'linear-gradient(135deg, #ec4899, #db2777)' ? 'selected' : '' }}>🌸 Pink</option>
                            <option value="linear-gradient(135deg, #14b8a6, #0d9488)" {{ $propertyFlagColor == 'linear-gradient(135deg, #14b8a6, #0d9488)' ? 'selected' : '' }}>🩵 Teal</option>
                            <option value="linear-gradient(135deg, #d4af37, #b8941f)" {{ $propertyFlagColor == 'linear-gradient(135deg, #d4af37, #b8941f)' ? 'selected' : '' }}>⭐ Gold</option>
                        </select>
                    </td>
                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                        <button onclick="updateFlag('{{ $propertyId }}')" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                            <i class="fas fa-save mr-1"></i>Save
                        </button>
                        <button onclick="clearFlag('{{ $propertyId }}')" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium text-sm ml-2">
                            <i class="fas fa-times mr-1"></i>Clear
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
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
    // Bulk Actions Functions
    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.property-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateBulkActionsBar();
    }
    
    function updateBulkActionsBar() {
        const checkboxes = document.querySelectorAll('.property-checkbox:checked');
        const count = checkboxes.length;
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        const selectedCount = document.getElementById('selectedCount');
        
        if (count > 0) {
            bulkActionsBar.style.display = 'block';
            selectedCount.textContent = count;
        } else {
            bulkActionsBar.style.display = 'none';
        }
        
        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.property-checkbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        selectAllCheckbox.checked = allCheckboxes.length > 0 && count === allCheckboxes.length;
    }
    
    function getSelectedPropertyIds() {
        const checkboxes = document.querySelectorAll('.property-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }
    
    function deselectAll() {
        document.querySelectorAll('.property-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        updateBulkActionsBar();
    }
    
    function applyBulkFlag() {
        const flagText = document.getElementById('bulkFlagText').value;
        const flagColor = document.getElementById('bulkFlagColor').value;
        const propertyIds = getSelectedPropertyIds();
        
        if (!flagText) {
            showNotification('error', 'Please select a flag text');
            return;
        }
        
        if (propertyIds.length === 0) {
            showNotification('error', 'Please select at least one property');
            return;
        }
        
        // Update each selected property
        let completed = 0;
        let failed = 0;
        propertyIds.forEach(propertyId => {
            fetch(`/admin/properties/${propertyId}/update-flag`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    flag: flagText,
                    flag_color: flagColor
                })
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    throw new Error('Response was not JSON');
                }
            })
            .then(data => {
                if (data.success) {
                    // Update individual inputs
                    document.getElementById(`flag-text-${propertyId}`).value = flagText;
                    document.getElementById(`flag-color-${propertyId}`).value = flagColor;
                    
                    // Update preview
                    const preview = document.getElementById(`flag-preview-${propertyId}`);
                    preview.innerHTML = `<span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold text-white shadow-sm" style="background: ${flagColor || 'linear-gradient(135deg, #d4af37, #b8941f)'}">${flagText}</span>`;
                    
                    completed++;
                    console.log(`✓ Property ${propertyId} updated successfully`);
                } else {
                    failed++;
                    console.error(`✗ Property ${propertyId} failed:`, data.message || 'Unknown error');
                }
                
                if (completed + failed === propertyIds.length) {
                    if (completed > 0) {
                        showNotification('success', `Flag applied to ${completed} properties!${failed > 0 ? ` (${failed} failed - check console)` : ''}`);
                    } else {
                        showNotification('error', `Failed to update ${failed} properties. Check browser console and server logs for details.`);
                    }
                    deselectAll();
                }
            })
            .catch(error => {
                console.error(`✗ Error updating property ${propertyId}:`, error);
                failed++;
                if (completed + failed === propertyIds.length) {
                    if (completed > 0) {
                        showNotification('success', `Flag applied to ${completed} properties! (${failed} failed - check console)`);
                    } else {
                        showNotification('error', `Failed to update ${failed} properties. Check browser console and server logs.`);
                    }
                    deselectAll();
                }
            });
        });
    }
    
    function clearBulkFlag() {
        const propertyIds = getSelectedPropertyIds();
        
        if (propertyIds.length === 0) {
            showNotification('error', 'Please select at least one property');
            return;
        }
        
        if (!confirm(`Remove flags from ${propertyIds.length} properties?`)) {
            return;
        }
        
        // Clear flag for each selected property
        let completed = 0;
        let failed = 0;
        propertyIds.forEach(propertyId => {
            fetch(`/admin/properties/${propertyId}/update-flag`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    flag: '',
                    flag_color: ''
                })
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    throw new Error('Response was not JSON');
                }
            })
            .then(data => {
                if (data.success) {
                    // Update individual inputs
                    document.getElementById(`flag-text-${propertyId}`).value = '';
                    document.getElementById(`flag-color-${propertyId}`).value = '';
                    
                    // Update preview
                    const preview = document.getElementById(`flag-preview-${propertyId}`);
                    preview.innerHTML = '<span class="text-sm text-gray-400 italic">No flag</span>';
                    
                    completed++;
                } else {
                    failed++;
                }
                
                if (completed + failed === propertyIds.length) {
                    if (completed > 0) {
                        showNotification('success', `Flags removed from ${completed} properties!${failed > 0 ? ` (${failed} failed)` : ''}`);
                    } else {
                        showNotification('error', `Failed to remove flags from ${failed} properties`);
                    }
                    deselectAll();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                failed++;
                if (completed + failed === propertyIds.length) {
                    if (completed > 0) {
                        showNotification('success', `Flags removed from ${completed} properties! (${failed} failed)`);
                    } else {
                        showNotification('error', `Failed to remove flags from ${failed} properties`);
                    }
                    deselectAll();
                }
            });
        });
    }
    
    // Single Property Functions
    function updateFlag(propertyId) {
        const flagText = document.getElementById(`flag-text-${propertyId}`).value;
        const flagColor = document.getElementById(`flag-color-${propertyId}`).value;
        
        fetch(`/admin/properties/${propertyId}/update-flag`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                flag: flagText,
                flag_color: flagColor
            })
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!response.ok) {
                console.error('Response not OK:', response.status, response.statusText);
                const text = await response.text();
                console.error('Response body:', text.substring(0, 500));
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                const text = await response.text();
                console.error('Expected JSON but got:', contentType, text.substring(0, 500));
                throw new Error('Response was not JSON');
            }
        })
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
            } else {
                showNotification('error', data.message || 'Failed to update flag');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Failed to update flag: ' + error.message);
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

