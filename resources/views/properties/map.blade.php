<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Map - LET CONNECT</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .gradient-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .gradient-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            z-index: 1;
        }
        
        .header-content {
            position: relative;
            z-index: 2;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            color: #1f2937;
        }
        
        .glass-card:hover {
            background: rgba(255, 255, 255, 0.98);
            border-color: rgba(255, 255, 255, 0.4);
            color: #111827;
            transform: scale(1.05);
        }
        
        .filters-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-bottom: 1px solid rgba(229, 231, 235, 0.5);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        .action-button {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #1f2937;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #d1d5db;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
            border-color: #9ca3af;
            color: #111827;
        }
        
        .secondary-button {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            color: #374151;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        
        .secondary-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-color: #d1d5db;
            color: #1f2937;
        }
        
        .search-button {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            color: #065f46;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.15);
            border: 1px solid #bbf7d0;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .search-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            border-color: #86efac;
            color: #064e3b;
        }
        
        .filter-input {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 0.875rem 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .filter-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .filter-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .filter-badge {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 1px solid #3b82f6;
            color: #1e40af;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(59, 130, 246, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.25rem;
        }
        
        .results-summary {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #0ea5e9;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.1);
        }
        
        .success-message {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 1px solid #10b981;
            color: #065f46;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
            margin-bottom: 2rem;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Mobile filter toggle */
        .filter-toggle {
            display: block;
            width: 100%;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            color: #475569;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }
        
        .filter-toggle:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        
        .filters-content {
            display: none;
        }
        
        @media (min-width: 640px) {
            .filters-content {
                display: block;
            }
        }
        
        .filters-content.show {
            display: block;
        }
        
        /* Mobile-first responsive design */
        @media (max-width: 767px) {
            .action-button, .secondary-button, .search-button {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
                min-height: 44px;
            }
            
            .filter-input {
                padding: 0.75rem;
                font-size: 1rem;
                min-height: 44px;
            }
            
            .filters-section {
                padding: 1.5rem 1rem;
            }
            
            .filters-section .max-w-7xl {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        
        /* Touch-friendly improvements */
        @media (max-width: 767px) {
            button, select, input, a {
                min-height: 44px;
                min-width: 44px;
            }
        }
        
        #map {
            height: calc(100vh - 200px);
            width: 100%;
        }
        
        
        
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            z-index: 1000;
        }
        
        .error-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fee2e2;
            color: #dc2626;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            z-index: 1000;
            max-width: 400px;
        }
        
        .info-window {
            max-width: 280px;
            padding: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(229, 231, 235, 0.5);
        }
        
        .property-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 7px 28px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(229, 231, 235, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        
        .property-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
            border-color: #3b82f6;
        }
        
        .property-image {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .property-card:hover .property-image {
            transform: scale(1.08);
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: white;
            color: #374151;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background: #f9fafb;
            transform: translateY(-2px);
        }
        
        .legend {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            max-width: 350px;
            max-height: 500px;
            overflow-y: auto;
        }
        
        .legend h3 {
            margin: 0 0 15px 0;
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            text-align: center;
        }
        
        .legend-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .legend-total, .legend-companies {
            font-size: 14px;
            color: #6b7280;
        }
        
        .legend-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin: 8px 0;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s;
            cursor: pointer;
            border: 1px solid transparent;
        }
        
        .legend-item:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
            transform: translateX(2px);
        }
        
        .legend-item.active {
            background-color: #dbeafe;
            border-color: #3b82f6;
        }
        
        .legend-color {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            margin-right: 12px;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            flex-shrink: 0;
        }
        
        .legend-text {
            font-size: 14px;
            color: #374151;
            flex: 1;
            font-weight: 500;
        }
        
        .legend-stats {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
        }
        
        .legend-count {
            font-size: 12px;
            color: #1f2937;
            background: #f3f4f6;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 600;
        }
        
        .legend-percentage {
            font-size: 11px;
            color: #6b7280;
        }
        
        .legend-actions {
            display: flex;
            gap: 8px;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }
        
        .legend-button {
            flex: 1;
            padding: 6px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: white;
            color: #374151;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .legend-button:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }
        
        .filters-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            min-width: 250px;
            max-height: 80vh;
                overflow-y: auto;
            }
            
        .filters-panel h3 {
            margin: 0 0 15px 0;
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .filter-group {
            margin-bottom: 15px;
        }
        
        .filter-label {
            display: block;
                font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }
        
        .filter-input {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
                font-size: 14px;
            transition: border-color 0.2s;
        }
        
        .filter-input:focus {
            outline: none;
            border-color: #3b82f6;
        }
        
        .filter-button {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            margin: 5px 0;
            transition: background-color 0.2s;
        }
        
        .filter-button:hover {
            background: #2563eb;
        }
        
        .filter-button.secondary {
            background: #6b7280;
        }
        
        .filter-button.secondary:hover {
            background: #4b5563;
        }
        
        .toggle-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 8px;
            cursor: pointer;
                font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .toggle-panel:hover {
            background: #2563eb;
        }
        
        .panel-hidden {
            display: none;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    @include('layouts.properties-navigation')
    <div class="min-h-screen">

        <!-- Property Map Header -->
        <div class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-3 sm:space-y-0">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Property Map</h1>
                        <div class="hidden sm:block h-6 w-px bg-gray-300"></div>
                        <div class="text-sm sm:text-base text-gray-600">
                            <i class="fas fa-map-marked-alt mr-2"></i><span id="propertyCount">Loading properties...</span>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                        @auth
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-800 font-medium transition-colors duration-200 text-sm sm:text-base">
                            <i class="fas fa-cog mr-2"></i>Admin
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800 font-medium transition-colors duration-200 text-sm sm:text-base">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        @endauth
                        <a href="{{ route('properties.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors text-sm sm:text-base">
                            <i class="fas fa-list mr-2"></i>List View
                        </a>
                </div>
            </div>
            </div>
        </div>

        <!-- Success Messages -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4">
                <div class="success-message animate-fade-in">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-2xl"></i>
                        <span class="text-lg font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Enhanced Filters -->
        <div class="filters-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                <!-- Mobile filter toggle -->
                <button type="button" class="filter-toggle sm:hidden w-full mb-4" onclick="toggleFilters()">
                    <i class="fas fa-filter mr-2"></i>
                    <span id="filterToggleText">Show Filters</span>
                    <i class="fas fa-chevron-down ml-2" id="filterToggleIcon"></i>
                </button>
                
                <div class="filters-content sm:block" id="filtersContent">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 space-y-4 sm:space-y-0">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 flex items-center space-x-2 sm:space-x-3">
                            <div class="bg-blue-100 p-2 sm:p-3 rounded-full">
                                <i class="fas fa-filter text-blue-600 text-lg sm:text-xl"></i>
                            </div>
                            <span>Search Filters</span>
                        </h2>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                            <a href="{{ route('properties.map') }}" class="text-gray-600 hover:text-gray-800 font-medium transition-colors duration-300 text-sm sm:text-base">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </a>
                            <a href="{{ route('properties.index') }}" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-300 text-sm sm:text-base">
                                <i class="fas fa-list mr-2"></i>Switch to List View
                        </a>
                    </div>
                </div>
                
                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                                   id="searchInput"
                               value="{{ request('search') }}" 
                               placeholder="Search properties by title, description, or location..." 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            @if(request('search'))
                                    <button type="button" onclick="clearSearch()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                    </button>
                            @endif
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Search through property titles, descriptions, and locations
                    </div>
                </div>
                
                    <!-- Combined filters form -->
                    <form id="filterForm" class="space-y-4 sm:space-y-6">
                        <!-- First row of filters -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        <div>
                                <label class="filter-label text-sm sm:text-base">Location</label>
                                <select id="locationFilter" class="filter-input w-full text-sm sm:text-base">
                                <option value="">All Locations</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                        {{ $location }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                                <label class="filter-label text-sm sm:text-base">Property Type</label>
                                <select id="propertyTypeFilter" class="filter-input w-full text-sm sm:text-base">
                                <option value="">All Types</option>
                                @foreach($propertyTypes as $type)
                                    <option value="{{ $type }}" {{ request('property_type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        @auth
                        <div>
                                <label class="filter-label text-sm sm:text-base">Agent Name</label>
                                <select id="agentFilter" class="filter-input w-full text-sm sm:text-base">
                                <option value="">All Agents</option>
                                @foreach($agentNames as $agent)
                                    <option value="{{ $agent }}" {{ request('agent_name') == $agent ? 'selected' : '' }}>
                                        {{ $agent }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endauth
                        
                        <div>
                                <label class="filter-label text-sm sm:text-base">London Area</label>
                                <select id="londonAreaFilter" class="filter-input w-full text-sm sm:text-base">
                                <option value="">All Areas</option>
                                <option value="central" {{ request('london_area') == 'central' ? 'selected' : '' }}>Central London</option>
                                <option value="east" {{ request('london_area') == 'east' ? 'selected' : '' }}>East London</option>
                                <option value="north" {{ request('london_area') == 'north' ? 'selected' : '' }}>North London</option>
                                <option value="south" {{ request('london_area') == 'south' ? 'selected' : '' }}>South London</option>
                                <option value="west" {{ request('london_area') == 'west' ? 'selected' : '' }}>West London</option>
                            </select>
                            </div>
                        </div>
                        
                        <!-- Second row of filters -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        <div>
                                <label class="filter-label text-sm sm:text-base">Min Price</label>
                                <input type="number" id="minPriceFilter" value="{{ request('min_price') }}" 
                                       placeholder="£0" class="filter-input w-full text-sm sm:text-base">
                        </div>
                    
                        <div>
                                <label class="filter-label text-sm sm:text-base">Max Price</label>
                                <input type="number" id="maxPriceFilter" value="{{ request('max_price') }}" 
                                       placeholder="£5000" class="filter-input w-full text-sm sm:text-base">
                        </div>
                        
                        <div>
                                <label class="filter-label text-sm sm:text-base">Couples Allowed</label>
                                <select id="couplesAllowedFilter" class="filter-input w-full text-sm sm:text-base">
                                    <option value="">All Properties</option>
                                    <option value="yes" {{ request('couples_allowed') == 'yes' ? 'selected' : '' }}>Couples Welcome</option>
                                    <option value="no" {{ request('couples_allowed') == 'no' ? 'selected' : '' }}>Singles Only</option>
                                </select>
                        </div>
                        
                            <div class="flex flex-col sm:flex-row items-end space-y-2 sm:space-y-0 sm:space-x-3">
                                <button type="button" onclick="applyFilters()" class="search-button w-full text-sm sm:text-base">
                                    <i class="fas fa-search mr-2"></i>Search Properties
                            </button>
                                <button type="button" onclick="clearFilters()" class="secondary-button text-sm sm:text-base">
                                <i class="fas fa-times mr-2"></i>Clear
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    <!-- Legend -->
    <div class="legend" id="legend">
        <h3><i class="fas fa-palette mr-2"></i>Property Managers</h3>
        <div id="legendContent">
            <!-- Legend items will be populated by JavaScript -->
                    </div>
                </div>

    <!-- Loading Screen -->
    <div class="loading" id="loadingScreen">
        <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-3"></i>
        <div class="text-lg font-semibold text-gray-800">Loading Map...</div>
        <div class="text-sm text-gray-600">Please wait while we load the properties</div>
                </div>
                
    <!-- Error Screen -->
    <div class="error-message" id="errorScreen" style="display: none;">
        <i class="fas fa-exclamation-triangle text-3xl text-red-600 mb-3"></i>
        <div class="text-lg font-semibold text-gray-800">Map Error</div>
        <div class="text-sm text-gray-600" id="errorMessage">Something went wrong loading the map</div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors mt-3" onclick="location.reload()">
            <i class="fas fa-refresh mr-2"></i>Retry
        </button>
    </div>

    <!-- Map Container -->
    <div id="map"></div>

    <!-- Properties Data -->
    <div id="properties-data" style="display: none;">{!! json_encode($properties) !!}</div>

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key', 'YOUR_GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap&v=weekly&loading=async"></script>
    

    <script>
        // Global variables
        let map;
        let markers = [];
        let infoWindow;
        let properties = [];
        let filteredProperties = [];
        let agentStats = {};
        let agentColors = {};
        let showOthersOnly = false;

        // Initialize map when Google Maps API loads
        function initMap() {
            try {
                console.log('🗺️ Initializing map...');
                
                // Hide loading screen
                document.getElementById('loadingScreen').style.display = 'none';
                
                // Create map
                map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 51.5074, lng: -0.1278 }, // London center
                    zoom: 12,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    styles: [
                        {
                            featureType: 'poi',
                            elementType: 'labels',
                            stylers: [{ visibility: 'off' }]
                        }
                    ]
                });
                
                // Create info window
                infoWindow = new google.maps.InfoWindow();

                // Attach legend to bottom-left of the map
                const legendEl = document.getElementById('legend');
                if (legendEl) {
                    map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(legendEl);
                }

                // Load properties
                loadProperties();
                
                console.log('✅ Map initialized successfully');
                
                } catch (error) {
                console.error('❌ Map initialization failed:', error);
                showError('Failed to initialize map: ' + error.message);
            }
        }

        // Load properties from data attribute
        function loadProperties() {
            try {
                const propertiesData = document.getElementById('properties-data');
                if (!propertiesData) {
                    throw new Error('Properties data not found');
                }

                const propertiesJson = propertiesData.textContent;
                if (!propertiesJson || propertiesJson.trim() === '') {
                    throw new Error('No properties data available');
                }

                properties = JSON.parse(propertiesJson);
                console.log(`📊 Loaded ${properties.length} properties`);

                // Filter properties with valid coordinates
                const validProperties = properties.filter(property => {
                    const lat = parseFloat(property.latitude);
                    const lng = parseFloat(property.longitude);
                    return !isNaN(lat) && !isNaN(lng) && 
                           lat >= -90 && lat <= 90 && 
                           lng >= -180 && lng <= 180;
                });

                console.log(`📍 Found ${validProperties.length} properties with valid coordinates`);

                if (validProperties.length === 0) {
                    showError('No properties with valid coordinates found');
                    return;
                }
                
                // Update property count
                document.getElementById('propertyCount').textContent = 
                    `${validProperties.length} properties loaded`;

                // Initialize agent colors
                initializeAgentColors(validProperties);

                // Initialize filters from URL
                initializeFiltersFromURL();

                // Add event listeners for real-time filtering
                addFilterEventListeners();

                // Create markers
                createMarkers(validProperties);

                // Update legend
                updateLegend(validProperties);

                // Fit map to bounds
                fitMapToProperties(validProperties);

                    } catch (error) {
                console.error('❌ Error loading properties:', error);
                showError('Failed to load properties: ' + error.message);
            }
        }

        // Create markers for properties
        function createMarkers(properties) {
            try {
                // Clear existing markers
                clearMarkers();

                    const bounds = new google.maps.LatLngBounds();

                properties.forEach((property, index) => {
                    const lat = parseFloat(property.latitude);
                    const lng = parseFloat(property.longitude);

                    // Create marker
                        const marker = new google.maps.Marker({
                        position: { lat, lng },
                        map: map,
                            title: property.title || 'Property',
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 8,
                            fillColor: getAgentColor(property.agent_name).fill,
                                fillOpacity: 1,
                            strokeColor: getAgentColor(property.agent_name).stroke,
                                strokeWeight: 2
                            }
                        });
                        
                    // Add click listener
                    marker.addListener('click', () => {
                        showPropertyInfo(property, marker);
                    });

                    // Add to markers array
                                markers.push(marker);
                    bounds.extend({ lat, lng });
                });


                console.log(`✅ Created ${markers.length} markers`);

                            } catch (error) {
                console.error('❌ Error creating markers:', error);
                showError('Failed to create markers: ' + error.message);
            }
        }

        // Show property info in info window
        function showPropertyInfo(property, marker) {
            try {
                const content = createInfoWindowContent(property);
                infoWindow.setContent(content);
                                    infoWindow.open(map, marker);
                            } catch (error) {
                console.error('❌ Error showing property info:', error);
            }
        }

        // Create info window content with card style matching listing view
        function createInfoWindowContent(property) {
            const title = property.title || 'Untitled Property';
            const location = property.location || 'Location not specified';
            const price = property.formatted_price || property.price || 'Price on request';
            const propertyType = property.property_type || 'Unknown type';
            const description = property.description || 'No description available';
            const bedrooms = property.bedrooms || 'N/A';
            const bathrooms = property.bathrooms || 'N/A';
            const availableDate = property.available_date || 'N/A';
            const propertyId = property.id || '';
            const photoCount = property.photo_count || 0;
            const allPhotos = property.all_photos_array || [];

            // Get first image with fallback
            let imageHtml = '';
            if (property.high_quality_photos_array && property.high_quality_photos_array.length > 0) {
                imageHtml = `<img src="${property.high_quality_photos_array[0]}" alt="${title}" class="property-image w-full h-32 sm:h-36 lg:h-40 object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />`;
            } else if (property.first_photo_url && property.first_photo_url !== 'N/A') {
                imageHtml = `<img src="${property.first_photo_url}" alt="${title}" class="property-image w-full h-32 sm:h-36 lg:h-40 object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />`;
            } else if (property.photos && property.photos.length > 0) {
                imageHtml = `<img src="${property.photos[0]}" alt="${title}" class="property-image w-full h-32 sm:h-36 lg:h-40 object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />`;
            }

            // Add placeholder if no image
            if (!imageHtml) {
                imageHtml = `<div class="flex items-center justify-center h-32 sm:h-36 lg:h-40 bg-gray-200"><i class="fas fa-home text-3xl sm:text-4xl lg:text-5xl text-gray-400"></i></div>`;
                    } else {
                imageHtml += `<div class="flex items-center justify-center h-32 sm:h-36 lg:h-40 bg-gray-200" style="display: none;"><i class="fas fa-home text-3xl sm:text-4xl lg:text-5xl text-gray-400"></i></div>`;
            }

            // Photo count badge
            let photoBadgeHtml = '';
            if ((allPhotos && allPhotos.length > 0) || photoCount > 0) {
                const count = allPhotos.length > 0 ? allPhotos.length : photoCount;
                photoBadgeHtml = `
                    <div class="absolute top-2 sm:top-4 right-2 sm:right-4 bg-black bg-opacity-80 backdrop-blur-sm border border-white border-opacity-20 text-white px-3 py-2 rounded-full font-semibold text-sm shadow-lg">
                        <i class="fas fa-camera mr-1 sm:mr-2"></i>
                        ${count}
                        <span class="hidden sm:inline"> photos</span>
                    </div>
                `;
            }

            return `
                <div class="property-card w-56 max-w-xs bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden cursor-pointer group hover:shadow-xl transition-all duration-300" onclick="window.open('/properties/${propertyId}', '_blank')">
                    <!-- Property Image -->
                    <div class="relative h-32 sm:h-36 lg:h-40 bg-gray-200 overflow-hidden">
                        ${imageHtml}
                        ${photoBadgeHtml}
                    </div>
                    
                    <!-- Property Details -->
                    <div class="p-3 sm:p-4 lg:p-5">
                        <!-- Title -->
                        <div class="flex items-start justify-between mb-2 sm:mb-3">
                            <h3 class="font-bold text-sm sm:text-base text-gray-900 line-clamp-2 leading-tight group-hover:text-blue-600 transition-colors duration-300">
                                ${title.length > 35 ? title.substring(0, 35) + '...' : title}
                            </h3>
                        </div>
                        
                        <!-- Location -->
                        <div class="flex items-center text-gray-600 mb-2 sm:mb-3">
                            <i class="fas fa-map-marker-alt text-red-500 mr-1 sm:mr-2 text-xs"></i>
                            <span class="font-medium text-xs sm:text-sm">${location}</span>
                        </div>
                        
                        <!-- Price and Type -->
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <span class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-3 py-1.5 rounded-full font-bold text-sm sm:text-base">
                                ${price}
                            </span>
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs font-medium">
                                ${propertyType}
                            </span>
                        </div>
                        
                        <!-- Description -->
                        ${description && description !== 'N/A' ? `
                            <p class="text-gray-600 mb-3 sm:mb-4 line-clamp-2 leading-relaxed text-xs sm:text-sm">
                                ${description.length > 70 ? description.substring(0, 70) + '...' : description}
                            </p>
                        ` : ''}
                        
                        <!-- Action Button -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-1 sm:space-y-0">
                            <div class="flex items-center space-x-2 w-full sm:w-auto">
                                <span class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg font-medium text-center w-full sm:w-auto transition-colors duration-200 text-xs sm:text-sm">
                                    <i class="fas fa-info-circle mr-1"></i>View Details
                                </span>
                            </div>
                            
                            ${availableDate && availableDate !== 'N/A' ? `
                                <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-medium border border-green-200 w-full sm:w-auto text-center">
                                    <i class="fas fa-calendar mr-1"></i>${availableDate}
                                </span>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        // Generate colors for property managers
        let companyColors = {};
        let colorPalette = [
            '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#84cc16', '#f97316', '#6366f1',
            '#ec4899', '#14b8a6', '#f43f5e', '#8b5a2b', '#7c3aed', '#059669', '#dc2626', '#0891b2',
            '#be185d', '#7c2d12', '#1e40af', '#e11d48', '#0d9488', '#ca8a04', '#9333ea', '#0891b2',
            '#dc2626', '#059669', '#7c3aed', '#8b5a2b', '#f43f5e', '#14b8a6', '#ec4899', '#6366f1',
            '#f97316', '#84cc16', '#06b6d4', '#8b5cf6', '#f59e0b', '#10b981', '#ef4444', '#e11d48',
            '#0d9488', '#ca8a04', '#9333ea', '#0891b2', '#dc2626', '#059669', '#7c3aed', '#8b5a2b'
        ];

        // Initialize agent colors based on unique agent names
        function initializeAgentColors(properties) {
            const uniqueAgents = [...new Set(properties.map(p => p.agent_name).filter(a => a && a !== 'N/A' && a !== '' && a !== null))];
            
            uniqueAgents.forEach((agent, index) => {
                const colorIndex = index % colorPalette.length;
                agentColors[agent] = {
                    fill: colorPalette[colorIndex],
                    stroke: '#ffffff'
                };
            });

            // Add Others category for null/empty values
            agentColors['Others'] = { fill: '#6b7280', stroke: '#ffffff' };
            agentColors['default'] = { fill: '#3b82f6', stroke: '#ffffff' };

            console.log('🎨 Initialized colors for agents:', Object.keys(agentColors));
        }

        // Get agent color for markers
        function getAgentColor(agent) {
            if (!agent || agent === 'N/A' || agent === '' || agent === null) {
                return agentColors['Others'] || agentColors['default'];
            }

            // Check for exact match first
            if (agentColors[agent]) {
                return agentColors[agent];
            }

            // Check for partial matches
            for (const [key, color] of Object.entries(agentColors)) {
                if (key !== 'default' && key !== 'Others' && key !== 'N/A' && key !== '') {
                    if (agent.toLowerCase().includes(key.toLowerCase()) || 
                        key.toLowerCase().includes(agent.toLowerCase())) {
                        return color;
                    }
                }
            }

            // Generate a new color for unknown agents
            const colorIndex = Object.keys(agentColors).length % colorPalette.length;
            const newColor = {
                fill: colorPalette[colorIndex],
                stroke: '#ffffff'
            };
            agentColors[agent] = newColor;
            return newColor;
        }

        // Clear all markers
        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
            
        }

        // Fit map to all properties
        function fitMapToProperties(properties) {
            if (properties.length === 0) return;

            const bounds = new google.maps.LatLngBounds();
            properties.forEach(property => {
                bounds.extend({
                    lat: parseFloat(property.latitude),
                    lng: parseFloat(property.longitude)
                });
            });

            if (properties.length === 1) {
                map.setCenter(bounds.getCenter());
                map.setZoom(15);
                        } else {
                map.fitBounds(bounds);
            }
        }


        // Fit map to bounds
        function fitToBounds() {
            if (properties.length === 0) return;
            
            const validProperties = properties.filter(property => {
                const lat = parseFloat(property.latitude);
                const lng = parseFloat(property.longitude);
                return !isNaN(lat) && !isNaN(lng);
            });
            
            fitMapToProperties(validProperties);
        }

        // Reset map view
        function resetMap() {
            map.setCenter({ lat: 51.5074, lng: -0.1278 });
            map.setZoom(12);
        }

        // Show error message
        function showError(message) {
            document.getElementById('loadingScreen').style.display = 'none';
            document.getElementById('errorScreen').style.display = 'block';
            document.getElementById('errorMessage').textContent = message;
        }

        // Handle Google Maps API errors
        window.gm_authFailure = function() {
            showError('Google Maps API authentication failed. Please check your API key.');
        };

        // Handle window errors
        window.addEventListener('error', function(event) {
            console.error('❌ JavaScript error:', event.error);
            if (event.error && event.error.message.includes('Google Maps')) {
                showError('Google Maps API error: ' + event.error.message);
            }
        });

        // Handle unhandled promise rejections
        window.addEventListener('unhandledrejection', function(event) {
            console.error('❌ Unhandled promise rejection:', event.reason);
            if (event.reason && event.reason.message && event.reason.message.includes('Google Maps')) {
                showError('Google Maps API error: ' + event.reason.message);
            }
        });

        // Filter properties based on current filter values
        function filterProperties() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const location = document.getElementById('locationFilter').value;
            const propertyType = document.getElementById('propertyTypeFilter').value;
            const agent = document.getElementById('agentFilter').value;
            const minPrice = parseFloat(document.getElementById('minPriceFilter').value) || 0;
            const maxPrice = parseFloat(document.getElementById('maxPriceFilter').value) || Infinity;
            const londonArea = document.getElementById('londonAreaFilter').value;
            const couplesAllowed = document.getElementById('couplesAllowedFilter').value;

            filteredProperties = properties.filter(property => {
                // Search filter
                if (searchTerm && !property.title?.toLowerCase().includes(searchTerm) && 
                    !property.description?.toLowerCase().includes(searchTerm) && 
                    !property.location?.toLowerCase().includes(searchTerm)) {
                    return false;
                }

                // Location filter
                if (location && property.location !== location) {
                    return false;
                }

                // Property type filter
                if (propertyType && property.property_type !== propertyType) {
                    return false;
                }

                // Agent filter
                if (agent && property.agent_name !== agent) {
                    return false;
                }

                // Handle "Others" category filter
                if (showOthersOnly) {
                    const hasAgent = property.agent_name && 
                                   property.agent_name !== 'N/A' && 
                                   property.agent_name !== '' && 
                                   property.agent_name !== null;
                    if (hasAgent) {
                        return false;
                    }
                }

                // Price filters
                const price = parseFloat(property.price) || 0;
                if (price < minPrice || price > maxPrice) {
                    return false;
                }

                // London area filter (basic implementation)
                if (londonArea && property.location) {
                    const locationLower = property.location.toLowerCase();
                    const areaKeywords = {
                        'central': ['central', 'westminster', 'city of london', 'camden', 'islington'],
                        'east': ['east', 'tower hamlets', 'hackney', 'newham', 'waltham forest'],
                        'north': ['north', 'haringey', 'enfield', 'barnet', 'harrow'],
                        'south': ['south', 'lambeth', 'southwark', 'lewisham', 'greenwich'],
                        'west': ['west', 'hammersmith', 'fulham', 'kensington', 'chelsea']
                    };
                    
                    if (areaKeywords[londonArea]) {
                        const hasAreaKeyword = areaKeywords[londonArea].some(keyword => 
                            locationLower.includes(keyword)
                        );
                        if (!hasAreaKeyword) {
                            return false;
                        }
                    }
                }

                // Couples allowed filter
                if (couplesAllowed && property.couples_allowed) {
                    const propertyCouplesAllowed = property.couples_allowed.toLowerCase();
                    if (couplesAllowed === 'yes' && !propertyCouplesAllowed.includes('yes') && !propertyCouplesAllowed.includes('welcome')) {
                        return false;
                    }
                    if (couplesAllowed === 'no' && !propertyCouplesAllowed.includes('no') && !propertyCouplesAllowed.includes('singles')) {
                        return false;
                    }
                }

                return true;
            });

            console.log(`🔍 Filtered to ${filteredProperties.length} properties`);
            return filteredProperties;
        }

        // Apply filters and update map
        function applyFilters() {
            const validProperties = filterProperties().filter(property => {
                const lat = parseFloat(property.latitude);
                const lng = parseFloat(property.longitude);
                return !isNaN(lat) && !isNaN(lng) && 
                       lat >= -90 && lat <= 90 && 
                       lng >= -180 && lng <= 180;
            });

            // Update property count
            document.getElementById('propertyCount').textContent = 
                `${validProperties.length} properties (${properties.length} total)`;

            // Recreate markers with filtered data
            createMarkers(validProperties);
            
            // Update legend
            updateLegend(validProperties);

            // Fit map to filtered properties
            if (validProperties.length > 0) {
                fitMapToProperties(validProperties);
            }
        }

        // Clear all filters
        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('locationFilter').value = '';
            document.getElementById('propertyTypeFilter').value = '';
            document.getElementById('agentFilter').value = '';
            document.getElementById('minPriceFilter').value = '';
            document.getElementById('maxPriceFilter').value = '';
            document.getElementById('londonAreaFilter').value = '';
            document.getElementById('couplesAllowedFilter').value = '';
            
            // Reset flags
            showOthersOnly = false;
            
            // Reset to show all properties
            filteredProperties = properties;
            applyFilters();
        }

        // Toggle filters panel
        function toggleFiltersPanel() {
            const panel = document.getElementById('filtersPanel');
            const button = document.getElementById('togglePanelBtn');
            
            if (panel.classList.contains('panel-hidden')) {
                panel.classList.remove('panel-hidden');
                button.innerHTML = '<i class="fas fa-times mr-2"></i>Close';
            } else {
                panel.classList.add('panel-hidden');
                button.innerHTML = '<i class="fas fa-filter mr-2"></i>Filters';
            }
        }

        // Update legend with agent statistics
        function updateLegend(properties) {
            // Count properties by agent
            agentStats = {};
            properties.forEach(property => {
                const agent = property.agent_name;
                // Group null/empty values into "Others" category
                if (!agent || agent === 'N/A' || agent === '' || agent === null) {
                    agentStats['Others'] = (agentStats['Others'] || 0) + 1;
                } else {
                    agentStats[agent] = (agentStats[agent] || 0) + 1;
                }
            });

            // Sort agents by count (descending)
            const sortedAgents = Object.entries(agentStats)
                .sort(([,a], [,b]) => b - a);

            // Create legend HTML
            const legendContent = document.getElementById('legendContent');
            legendContent.innerHTML = `
                <div class="legend-header">
                    <div class="legend-total">
                        <strong>${properties.length}</strong> properties
                    </div>
                    <div class="legend-agents">
                        <strong>${sortedAgents.length}</strong> agents
                        </div>
                    </div>
                <div class="legend-list">
                    ${sortedAgents.map(([agent, count]) => {
                        const color = getAgentColor(agent);
                        const percentage = ((count / properties.length) * 100).toFixed(1);
                return `
                            <div class="legend-item" onclick="filterByAgent('${agent}')">
                                <div class="legend-color" style="background-color: ${color.fill};"></div>
                                <div class="legend-text">${agent}</div>
                                <div class="legend-stats">
                                    <div class="legend-count">${count}</div>
                                    <div class="legend-percentage">${percentage}%</div>
                        </div>
                    </div>
                `;
                    }).join('')}
                </div>
                <div class="legend-actions">
                    <button onclick="showAllAgents()" class="legend-button">Show All</button>
                    <button onclick="hideAllAgents()" class="legend-button">Hide All</button>
                </div>
            `;

            // Show/hide legend based on whether there are agents
            const legend = document.getElementById('legend');
            if (sortedAgents.length > 0) {
                legend.style.display = 'block';
            } else {
                legend.style.display = 'none';
            }
        }

        // Initialize filters from URL parameters
        function initializeFiltersFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            
            if (urlParams.get('search')) {
                document.getElementById('searchInput').value = urlParams.get('search');
            }
            if (urlParams.get('location')) {
                document.getElementById('locationFilter').value = urlParams.get('location');
            }
            if (urlParams.get('property_type')) {
                document.getElementById('propertyTypeFilter').value = urlParams.get('property_type');
            }
            if (urlParams.get('agent_name')) {
                document.getElementById('agentFilter').value = urlParams.get('agent_name');
            }
            if (urlParams.get('min_price')) {
                document.getElementById('minPriceFilter').value = urlParams.get('min_price');
            }
            if (urlParams.get('max_price')) {
                document.getElementById('maxPriceFilter').value = urlParams.get('max_price');
            }
            if (urlParams.get('london_area')) {
                document.getElementById('londonAreaFilter').value = urlParams.get('london_area');
            }
            if (urlParams.get('couples_allowed')) {
                document.getElementById('couplesAllowedFilter').value = urlParams.get('couples_allowed');
            }
        }

        // Add event listeners for real-time filtering
        function addFilterEventListeners() {
            // Search input with debounce
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyFilters();
                }, 500);
            });

            // Select filters with immediate effect
            const selectFilters = [
                'locationFilter',
                'propertyTypeFilter', 
                'agentFilter',
                'londonAreaFilter',
                'couplesAllowedFilter'
            ];

            selectFilters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    element.addEventListener('change', applyFilters);
                }
            });

            // Price filters with debounce
            const priceFilters = ['minPriceFilter', 'maxPriceFilter'];
            priceFilters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    element.addEventListener('input', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            applyFilters();
                        }, 500);
                    });
                }
            });
        }

        // Toggle mobile filters
        function toggleFilters() {
            const filtersContent = document.getElementById('filtersContent');
            const filterToggleText = document.getElementById('filterToggleText');
            const filterToggleIcon = document.getElementById('filterToggleIcon');
            
            if (filtersContent.classList.contains('show')) {
                filtersContent.classList.remove('show');
                filterToggleText.textContent = 'Show Filters';
                filterToggleIcon.classList.remove('fa-chevron-up');
                filterToggleIcon.classList.add('fa-chevron-down');
            } else {
                filtersContent.classList.add('show');
                filterToggleText.textContent = 'Hide Filters';
                filterToggleIcon.classList.remove('fa-chevron-down');
                filterToggleIcon.classList.add('fa-chevron-up');
            }
        }
        
        // Clear search function
        function clearSearch() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
                applyFilters();
            }
        }

        // Filter by specific agent
        function filterByAgent(agent) {
            const agentFilter = document.getElementById('agentFilter');
            if (agentFilter) {
                // Handle "Others" category - show only properties without agent name
                if (agent === 'Others') {
                    agentFilter.value = '';
                    showOthersOnly = true;
                } else {
                    agentFilter.value = agent;
                    showOthersOnly = false;
                }
                applyFilters();
            }
            
            // Update legend active state
            document.querySelectorAll('.legend-item').forEach(item => {
                item.classList.remove('active');
            });
            event.target.closest('.legend-item').classList.add('active');
        }

        // Show all agents
        function showAllAgents() {
            const agentFilter = document.getElementById('agentFilter');
            if (agentFilter) {
                agentFilter.value = '';
                showOthersOnly = false;
                applyFilters();
            }
            
            // Remove active states
            document.querySelectorAll('.legend-item').forEach(item => {
                item.classList.remove('active');
            });
        }

        // Hide all agents (show none)
        function hideAllAgents() {
            // This would require a more complex implementation
            // For now, just clear the agent filter
            showAllAgents();
        }
    </script>
</body>
</html>
