<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Properties - Property Scraper</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .management-header {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border-bottom: 2px solid #4b5563;
        }
        
        .management-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        
        .status-available { background-color: #dcfce7; color: #166534; }
        .status-rented { background-color: #fef3c7; color: #92400e; }
        .status-unavailable { background-color: #fee2e2; color: #991b1b; }
        .status-on-hold { background-color: #dbeafe; color: #1e40af; }
        
        .nav-link {
            color: #6b7280;
            transition: all 0.2s ease;
            border-radius: 8px;
            padding: 12px 16px;
        }
        
        .nav-link:hover {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .nav-link.active {
            background-color: #3b82f6;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="management-header shadow-lg">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <i class="fas fa-building text-3xl text-blue-400 mr-4"></i>
                        <h1 class="text-3xl font-bold text-white">Property Management</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-300">
                            Welcome, {{ Auth::user()->name }}
                        </span>
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-400 hover:text-white transition-colors">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-blue-400 hover:text-white transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex">
            <!-- Sidebar Navigation -->
            <aside class="w-64 bg-white shadow-lg min-h-screen">
                <nav class="p-6 space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link block">
                        <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                    </a>
                    <a href="{{ route('properties.index') }}" class="nav-link block">
                        <i class="fas fa-home mr-3"></i>All Properties
                    </a>
                    <a href="{{ route('properties.map') }}" class="nav-link block">
                        <i class="fas fa-map-marked-alt mr-3"></i>Property Map
                    </a>
                    <a href="{{ route('properties.manage') }}" class="nav-link active block">
                        <i class="fas fa-edit mr-3"></i>Manage Properties
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-8">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        {{ session('error') }}
                    </div>
                @endif
                
                <!-- Debug Info (remove in production) -->
                @if(config('app.debug'))
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-6 py-4 rounded-lg mb-6">
                        <i class="fas fa-bug mr-2"></i>
                        <strong>Debug Info:</strong> Request parameters: {{ json_encode(request()->all()) }}
                    </div>
                @endif

                <!-- Property Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <i class="fas fa-home text-green-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600">Total Properties</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalProperties }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <i class="fas fa-check-circle text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600">Available</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $availableCount }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <i class="fas fa-key text-yellow-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600">Rented</p>
                                <p class="text-2xl font-bold text-yellow-600">{{ $rentedCount }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 rounded-lg">
                                <i class="fas fa-ban text-red-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600">Unavailable</p>
                                <p class="text-2xl font-bold text-red-600">{{ $unavailableCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter Section -->
                <div class="management-card p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-search mr-2 text-blue-600"></i>Search & Filter Properties
                    </h2>
                    
                    <form method="GET" action="{{ route('properties.manage') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Search by title/location -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Title, location, company..." 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- Filter by status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Statuses</option>
                                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Rented</option>
                                    <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                                    <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                </select>
                            </div>
                            
                            <!-- Filter by price range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                                <select name="price_range" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Prices</option>
                                    <option value="0-500" {{ request('price_range') == '0-500' ? 'selected' : '' }}>£0 - £500</option>
                                    <option value="500-1000" {{ request('price_range') == '500-1000' ? 'selected' : '' }}>£500 - £1,000</option>
                                    <option value="1000-1500" {{ request('price_range') == '1000-1500' ? 'selected' : '' }}>£1,000 - £1,500</option>
                                    <option value="1500+" {{ request('price_range') == '1500+' ? 'selected' : '' }}>£1,500+</option>
                                </select>
                            </div>
                            
                            <!-- Filter by management company -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                                <select name="company" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Companies</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company }}" {{ request('company') == $company ? 'selected' : '' }}>{{ $company }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-3">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-filter mr-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('properties.manage') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                                <i class="fas fa-times mr-2"></i>Clear Filters
                            </a>
                            @if(request('search') || request('status') || request('price_range') || request('company'))
                                <a href="{{ route('properties.manage') }}?{{ http_build_query(request()->all()) }}&export=1" 
                                   class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-download mr-2"></i>Export Results
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Active Filters Display -->
                @if(request('search') || request('status') || request('price_range') || request('company'))
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-filter text-blue-600"></i>
                                <span class="text-sm font-medium text-blue-800">Active Filters:</span>
                            </div>
                            <a href="{{ route('properties.manage') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-times mr-1"></i>Clear All
                            </a>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @if(request('search'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                    Search: "{{ request('search') }}"
                                    <a href="{{ request()->except('search') }}" class="ml-2 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('status'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                    Status: {{ ucfirst(request('status')) }}
                                    <a href="{{ request()->except('status') }}" class="ml-2 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('price_range'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                    Price: {{ request('price_range') == '0-500' ? '£0 - £500' : (request('price_range') == '500-1000' ? '£500 - £1,000' : (request('price_range') == '1000-1500' ? '£1,000 - £1,500' : '£1,500+')) }}
                                    <a href="{{ request()->except('price_range') }}" class="ml-2 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('company'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                    Company: {{ request('company') }}
                                    <a href="{{ request()->except('company') }}" class="ml-2 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Results Summary -->
                <div class="flex justify-between items-center mb-4">
                    <div class="text-sm text-gray-600">
                        Showing {{ $properties->firstItem() ?? 0 }} - {{ $properties->lastItem() ?? 0 }} of {{ $properties->total() }} properties
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">Sort by:</span>
                        <select id="sort-select" class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title A-Z</option>
                        </select>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div class="management-card p-6 mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-tasks mr-3 text-blue-600"></i>Bulk Actions
                    </h2>
                    
                    <form method="POST" action="{{ route('properties.bulk-update-status') }}" id="bulkForm">
                        @csrf
                        <div class="flex items-center space-x-4 mb-4">
                            <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Status</option>
                                <option value="available">Available</option>
                                <option value="rented">Rented</option>
                                <option value="unavailable">Unavailable</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Update Selected
                            </button>
                        </div>
                        
                        <div class="text-sm text-gray-600">
                            <input type="checkbox" id="selectAll" class="mr-2">
                            <label for="selectAll">Select All Properties</label>
                        </div>
                    </form>
                </div>

                <!-- Properties Table -->
                <div class="management-card p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-list mr-3 text-blue-600"></i>All Properties
                    </h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" class="bulk-select-all rounded border-gray-300">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($properties as $property)
                                @try
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="property_ids[]" value="{{ $property->id ?? 0 }}" 
                                               class="bulk-select rounded border-gray-300">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $property->title ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $property->property_type ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $property->location ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $property->formatted_price ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full status-{{ strtolower($property->formatted_status ?? 'available') }}">
                                            {{ $property->formatted_status ?? 'Available' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('properties.show', $property) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('properties.edit', $property) }}" class="text-yellow-600 hover:text-yellow-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="showStatusModal({{ $property->id ?? 0 }}, '{{ $property->status ?? 'available' }}')" 
                                                    class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                            <a href="{{ route('admin.properties.edit', $property) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @catch(\Exception $e)
                                <!-- Property {{ $loop->index + 1 }} has invalid data and was skipped -->
                                @endtry
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        No properties found or an error occurred while loading properties.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $properties->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Update Property Status</h3>
                
                <form method="POST" id="statusForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="available">Available</option>
                            <option value="rented">Rented</option>
                            <option value="unavailable">Unavailable</option>
                            <option value="on_hold">On Hold</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideStatusModal()" 
                                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Bulk select functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.bulk-select');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Status modal functionality
        function showStatusModal(propertyId, currentStatus) {
            const modal = document.getElementById('statusModal');
            const form = document.getElementById('statusForm');
            const statusSelect = form.querySelector('select[name="status"]');
            
            // Ensure currentStatus is a valid string
            if (typeof currentStatus !== 'string' || !currentStatus || currentStatus === 'N/A' || currentStatus === 'undefined' || currentStatus === 'null') {
                currentStatus = 'available';
            }
            
            form.action = `/properties/${propertyId}/status`;
            statusSelect.value = currentStatus;
            
            modal.classList.remove('hidden');
        }

        function hideStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('statusModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideStatusModal();
            }
        });

        // Sorting functionality
        document.getElementById('sort-select').addEventListener('change', function() {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('sort', this.value);
            window.location.href = currentUrl.toString();
        });

        // Auto-submit form on filter change for better UX
        document.querySelectorAll('select[name="status"], select[name="price_range"], select[name="company"]').forEach(select => {
            select.addEventListener('change', function() {
                // Validate the selected value before submitting
                const value = this.value;
                if (value && value !== 'N/A' && value !== 'undefined' && value !== 'null') {
                    // Additional validation for price range
                    if (this.name === 'price_range') {
                        const validRanges = ['0-500', '500-1000', '1000-1500', '1500+'];
                        if (!validRanges.includes(value)) {
                            console.error('Invalid price range selected:', value);
                            return;
                        }
                    }
                    this.closest('form').submit();
                }
            });
        });
        
        // Add input validation for search field
        document.querySelector('input[name="search"]').addEventListener('input', function() {
            const value = this.value.trim();
            if (value === 'N/A' || value === 'undefined' || value === 'null') {
                this.value = '';
            }
        });
        
        // Add error handling for bulk actions
        document.getElementById('bulkForm').addEventListener('submit', function(e) {
            const selectedProperties = document.querySelectorAll('.bulk-select:checked');
            const status = document.querySelector('select[name="status"]').value;
            
            if (selectedProperties.length === 0) {
                e.preventDefault();
                alert('Please select at least one property to update.');
                return false;
            }
            
            if (!status) {
                e.preventDefault();
                alert('Please select a status to apply.');
                return false;
            }
        });
    </script>
</body>
</html>
