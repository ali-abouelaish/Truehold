<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>LET CONNECT - Find Your Perfect Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
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
        
        .property-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
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
        
        .photo-badge {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        
        .price-tag {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }
        
        .price-tag:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }
        
        .type-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
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
        
        /* Mobile-first responsive design */
        @media (max-width: 767px) {
            .property-card {
                border-radius: 16px;
                margin-bottom: 1rem;
            }
            
            .property-card .p-8 {
                padding: 1.5rem;
            }
            
            .property-image {
                height: 200px;
            }
            
            .photo-badge {
                padding: 0.25rem 0.75rem;
                font-size: 0.75rem;
            }
            
            .price-tag {
                padding: 0.5rem 1rem;
                font-size: 1.25rem;
            }
            
            .type-badge {
                padding: 0.25rem 0.75rem;
                font-size: 0.75rem;
            }
            
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
        
        /* Small screens */
        @media (max-width: 640px) {
            .property-card .p-8 {
                padding: 1rem;
            }
            
            .property-image {
                height: 180px;
            }
            
            .photo-badge {
                top: 0.5rem;
                right: 0.5rem;
                padding: 0.25rem 0.5rem;
                font-size: 0.7rem;
            }
            
            .price-tag {
                font-size: 1.125rem;
                padding: 0.5rem 0.75rem;
            }
            
            .type-badge {
                font-size: 0.7rem;
                padding: 0.25rem 0.5rem;
            }
        }
        
        /* Very small screens */
        @media (max-width: 375px) {
            .property-card .p-8 {
                padding: 0.75rem;
            }
            
            .property-image {
                height: 160px;
            }
            
            .photo-badge {
                font-size: 0.65rem;
                padding: 0.2rem 0.4rem;
            }
        }
        
        /* Touch-friendly improvements */
        @media (max-width: 767px) {
            button, select, input, a {
                min-height: 44px;
                min-width: 44px;
            }
            
            .property-card {
                -webkit-tap-highlight-color: transparent;
            }
            
            .property-card:active {
                transform: scale(0.98);
            }
        }
        
        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .property-image {
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
            }
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
        
        /* Mobile pagination */
        @media (max-width: 640px) {
            .pagination-container {
                flex-direction: column;
                align-items: center;
                space-y: 4;
            }
            
            .pagination-container .flex {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
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
        
        .empty-state {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 20px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .empty-icon {
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            border-radius: 50%;
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-slide-up {
            animation: slideUp 0.8s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .floating-action {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
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
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    @include('layouts.properties-navigation')
    <div class="min-h-screen">


        <!-- Property Listing Header -->
        <div class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-3 sm:space-y-0">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Property Listings</h1>
                        <div class="hidden sm:block h-6 w-px bg-gray-300"></div>
                        <div class="text-sm sm:text-base text-gray-600">
                            <i class="fas fa-home mr-2"></i>{{ $properties->total() }} properties found
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
                        <a href="{{ route('properties.map') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors text-sm sm:text-base">
                            <i class="fas fa-map-marked-alt mr-2"></i>Map View
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
                            <a href="{{ route('properties.index') }}" class="text-gray-600 hover:text-gray-800 font-medium transition-colors duration-300 text-sm sm:text-base">
                                <i class="fas fa-times mr-2"></i>Clear Filters
                            </a>
                            <a href="{{ route('properties.map') }}" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-300 text-sm sm:text-base">
                                <i class="fas fa-map-marked-alt mr-2"></i>Switch to Map View
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
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search properties by title, description, or location..." 
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base"
                                   onchange="updateFilters()">
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
                    <form method="GET" action="{{ route('properties.index') }}" id="filterForm" class="space-y-4 sm:space-y-6">
                        <!-- First row of filters -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                            <div>
                                <label class="filter-label text-sm sm:text-base">Location</label>
                                <select name="location" class="filter-input w-full text-sm sm:text-base">
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
                                <select name="property_type" class="filter-input w-full text-sm sm:text-base">
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
                                <label class="filter-label text-sm sm:text-base">Management Company</label>
                                <select name="management_company" class="filter-input w-full text-sm sm:text-base">
                                    <option value="">All Companies</option>
                                    @foreach($managementCompanies as $company)
                                        <option value="{{ $company }}" {{ request('management_company') == $company ? 'selected' : '' }}>
                                            {{ $company }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endauth
                            
                            <div>
                                <label class="filter-label text-sm sm:text-base">London Area</label>
                                <select name="london_area" class="filter-input w-full text-sm sm:text-base">
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
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                            <div>
                                <label class="filter-label text-sm sm:text-base">Min Price</label>
                                <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                       placeholder="£0" class="filter-input w-full text-sm sm:text-base">
                            </div>
                            
                            <div>
                                <label class="filter-label text-sm sm:text-base">Max Price</label>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                       placeholder="£5000" class="filter-input w-full text-sm sm:text-base">
                            </div>
                            
                            <div class="flex flex-col sm:flex-row items-end space-y-2 sm:space-y-0 sm:space-x-3">
                                <button type="submit" class="search-button w-full text-sm sm:text-base">
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

        <!-- Properties Gallery -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <!-- Enhanced Filter Summary -->
            @if(request('search') || request('location') || request('property_type') || request('min_price') || request('max_price') || request('available_date') || request('management_company') || request('london_area'))
                <div class="mb-6 sm:mb-8 p-4 sm:p-6 bg-blue-50 border border-blue-200 rounded-16 sm:rounded-20 animate-fade-in">
                    <h3 class="text-lg sm:text-xl font-bold text-blue-800 mb-3 sm:mb-4 flex items-center space-x-2">
                        <i class="fas fa-filter text-blue-600"></i>
                        <span>Active Filters:</span>
                    </h3>
                    <div class="flex flex-wrap gap-2 sm:gap-3">
                        @if(request('search'))
                            <span class="filter-badge text-xs sm:text-sm">
                                <i class="fas fa-search"></i>Search: "{{ request('search') }}"
                            </span>
                        @endif
                        @if(request('location'))
                            <span class="filter-badge text-xs sm:text-sm">
                                <i class="fas fa-map-marker-alt"></i>Location: {{ request('location') }}
                            </span>
                        @endif
                        @if(request('property_type'))
                            <span class="filter-badge text-xs sm:text-sm">
                                <i class="fas fa-home"></i>Type: {{ request('property_type') }}
                            </span>
                        @endif
                        @if(request('management_company'))
                            <span class="filter-badge text-xs sm:text-sm">
                                <i class="fas fa-building"></i>Company: {{ request('management_company') }}
                            </span>
                        @endif
                        @if(request('london_area'))
                            <span class="filter-badge text-xs sm:text-sm">
                                <i class="fas fa-compass"></i>Area: {{ ucfirst(request('london_area')) }} London
                            </span>
                        @endif
                        @if(request('min_price'))
                            <span class="filter-badge text-xs sm:text-sm">
                                <i class="fas fa-pound-sign"></i>Min: £{{ request('min_price') }}
                            </span>
                        @endif
                        @if(request('max_price'))
                            <span class="filter-badge text-xs sm:text-sm">
                                <i class="fas fa-pound-sign"></i>Max: £{{ request('max_price') }}
                            </span>
                        @endif
                        @if(request('available_date'))
                            <span class="filter-badge text-xs sm:text-sm">
                                <i class="fas fa-calendar"></i>Available: {{ request('available_date') }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Enhanced Results Summary -->
            <div class="results-summary mb-6 sm:mb-8 animate-fade-in">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-2 sm:space-y-0">
                    <div class="text-gray-700 text-sm sm:text-base">
                        <span class="font-bold text-lg sm:text-xl">{{ $properties->total() }}</span> properties found
                        @if(request('search') || request('location') || request('property_type') || request('min_price') || request('max_price') || request('available_date'))
                            <span class="text-gray-500 ml-2">(filtered)</span>
                        @endif
                    </div>
                    <div class="text-xs sm:text-sm text-gray-600 bg-white px-3 sm:px-4 py-2 rounded-full shadow-sm">
                        Showing {{ $properties->firstItem() ?? 0 }}-{{ $properties->lastItem() ?? 0 }} of {{ $properties->total() }}
                    </div>
                </div>
            </div>

            @if($properties->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                    @foreach($properties as $property)
                        <a href="{{ route('properties.show', $property->id) }}" class="block">
                            <div class="property-card animate-slide-up cursor-pointer group">
                                <!-- Enhanced Property Image Gallery -->
                                <div class="relative h-48 sm:h-56 lg:h-72 bg-gray-200 overflow-hidden">
                                    @if($property->high_quality_photos_array && count($property->high_quality_photos_array) > 0)
                                        <img src="{{ $property->high_quality_photos_array[0] }}" alt="{{ $property->title }}" 
                                             class="property-image w-full h-full object-cover">
                                    @elseif($property->first_photo_url && $property->first_photo_url !== 'N/A')
                                        <img src="{{ $property->first_photo_url }}" alt="{{ $property->title }}" 
                                             class="property-image w-full h-full object-cover">
                                    @else
                                        <div class="flex items-center justify-center h-full">
                                            <i class="fas fa-home text-4xl sm:text-5xl lg:text-6xl text-gray-400"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Enhanced Photo Count Badge -->
                                    @if(($property->all_photos_array && count($property->all_photos_array) > 0) || $property->photo_count > 0)
                                        <div class="photo-badge absolute top-2 sm:top-4 right-2 sm:right-4">
                                            <i class="fas fa-camera mr-1 sm:mr-2"></i>
                                            @if($property->all_photos_array && count($property->all_photos_array) > 0)
                                                {{ count($property->all_photos_array) }}
                                            @else
                                                {{ $property->photo_count }}
                                            @endif
                                            <span class="hidden sm:inline"> photos</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Enhanced Property Details -->
                                <div class="p-4 sm:p-6 lg:p-8">
                                    <div class="flex items-start justify-between mb-3 sm:mb-4">
                                        <h3 class="font-bold text-lg sm:text-xl text-gray-900 line-clamp-2 leading-tight group-hover:text-blue-600 transition-colors duration-300">
                                            {{ Str::limit($property->title, 50) }}
                                        </h3>
                                    </div>
                                    
                                    <div class="flex items-center text-gray-600 mb-3 sm:mb-4">
                                        <i class="fas fa-map-marker-alt text-red-500 mr-2 sm:mr-3"></i>
                                        <span class="font-medium text-sm sm:text-base">{{ $property->location ?: 'Location not specified' }}</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                                        <span class="price-tag text-lg sm:text-xl lg:text-2xl">
                                            {{ $property->formatted_price }}
                                        </span>
                                        @if($property->property_type)
                                            <span class="type-badge text-xs sm:text-sm">
                                                {{ $property->property_type }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($property->description && $property->description !== 'N/A')
                                        <p class="text-gray-600 mb-4 sm:mb-6 line-clamp-2 leading-relaxed text-sm sm:text-base">
                                            {{ Str::limit($property->description, 100) }}
                                        </p>
                                    @endif
                                    
                                    <!-- Enhanced Action Buttons -->
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-2 sm:space-y-0">
                                        <div class="flex items-center space-x-3 w-full sm:w-auto">
                                            <span class="action-button w-full sm:w-auto text-center">
                                                <i class="fas fa-info-circle mr-2"></i>View Details
                                            </span>
                                        </div>
                                        
                                        @if($property->available_date && $property->available_date !== 'N/A')
                                            <span class="text-xs text-green-600 bg-green-100 px-2 sm:px-3 py-1 sm:py-2 rounded-full font-medium border border-green-200 w-full sm:w-auto text-center">
                                                <i class="fas fa-calendar mr-1"></i>{{ $property->available_date }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <!-- Enhanced Pagination with Filter Preservation -->
                <div class="mt-12 sm:mt-16">
                    @if($properties->hasPages())
                        <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0 pagination-container">
                            <div class="text-gray-600 text-sm sm:text-base">
                                Showing {{ $properties->firstItem() ?? 0 }} to {{ $properties->lastItem() ?? 0 }} of {{ $properties->total() }} results
                            </div>
                            <div class="flex flex-wrap justify-center space-x-2">
                                @if($properties->onFirstPage())
                                    <span class="px-4 py-2 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                                @else
                                    <a href="{{ $properties->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" 
                                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                        Previous
                                    </a>
                                @endif
                                
                                @foreach($properties->getUrlRange(max(1, $properties->currentPage() - 2), min($properties->lastPage(), $properties->currentPage() + 2)) as $page => $url)
                                    @if($page == $properties->currentPage())
                                        <span class="px-4 py-2 bg-blue-600 text-white rounded-lg">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}" 
                                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach
                                
                                @if($properties->hasMorePages())
                                    <a href="{{ $properties->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" 
                                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                        Next
                                    </a>
                                @else
                                    <span class="px-4 py-2 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <!-- Enhanced Empty State -->
                <div class="empty-state animate-fade-in">
                    <div class="empty-icon">
                        <i class="fas fa-search text-3xl sm:text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 sm:mb-4">No properties found</h3>
                    <p class="text-gray-600 text-lg sm:text-xl mb-4 sm:mb-6 text-center">Try adjusting your search criteria or check back later.</p>
                    <button onclick="clearFilters()" class="action-button w-full sm:w-auto">
                        <i class="fas fa-refresh mr-2"></i>Clear All Filters
                    </button>
                </div>
            @endif
        </main>
    </div>

    <!-- Floating Action Button -->
    <div class="floating-action">
        <button onclick="scrollToTop()" class="glass-card p-4 rounded-full hover:scale-110 transition-transform duration-300 shadow-2xl">
            <i class="fas fa-arrow-up text-white text-xl"></i>
        </button>
    </div>

    <!-- Enhanced Filter Enhancement JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const minPriceInput = document.querySelector('input[name="min_price"]');
            const maxPriceInput = document.querySelector('input[name="max_price"]');
            const filterForm = document.getElementById('filterForm');

            // Real-time price validation
            function validatePriceInputs() {
                const minPrice = parseInt(minPriceInput.value) || 0;
                const maxPrice = parseInt(maxPriceInput.value) || 0;

                if (minPrice > 0 && maxPrice > 0 && minPrice > maxPrice) {
                    maxPriceInput.setCustomValidity('Maximum price must be greater than minimum price');
                    maxPriceInput.classList.add('border-red-500');
                } else {
                    maxPriceInput.setCustomValidity('');
                    maxPriceInput.classList.remove('border-red-500');
                }
            }

            // Add event listeners for price validation
            if (minPriceInput && maxPriceInput) {
                minPriceInput.addEventListener('input', validatePriceInputs);
                maxPriceInput.addEventListener('input', validatePriceInputs);
            }

            // Auto-submit form when select filters change
            const filterSelects = document.querySelectorAll('select[name="location"], select[name="property_type"], select[name="management_company"], select[name="london_area"]');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    // Auto-submit for immediate feedback
                    filterForm.submit();
                });
            });

            // Add loading state to search button
            filterForm.addEventListener('submit', function() {
                const searchButton = filterForm.querySelector('button[type="submit"]');
                if (searchButton) {
                    searchButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Searching...';
                    searchButton.disabled = true;
                }
            });

            // Store current filters in sessionStorage for persistence
            function storeFilters() {
                const formData = new FormData(filterForm);
                const filters = {};
                for (let [key, value] of formData.entries()) {
                    if (value) filters[key] = value;
                }
                sessionStorage.setItem('propertyFilters', JSON.stringify(filters));
            }

            // Restore filters from sessionStorage
            function restoreFilters() {
                const stored = sessionStorage.getItem('propertyFilters');
                if (stored) {
                    const filters = JSON.parse(stored);
                    Object.keys(filters).forEach(key => {
                        const input = filterForm.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.value = filters[key];
                        }
                    });
                }
            }

            // Store filters when form is submitted
            filterForm.addEventListener('submit', storeFilters);

            // Restore filters on page load
            restoreFilters();
        });

        // Clear all filters function
        function clearFilters() {
            const filterForm = document.getElementById('filterForm');
            filterForm.reset();
            sessionStorage.removeItem('propertyFilters');
            // Redirect to base URL without any parameters
            window.location.href = '{{ route("properties.index") }}';
        }

        // Apply filters function
        function applyFilters() {
            const filterForm = document.getElementById('filterForm');
            const formData = new FormData(filterForm);
            const params = new URLSearchParams();
            
            // Only add non-empty values to URL
            for (let [key, value] of formData.entries()) {
                if (value && value.trim() !== '') {
                    params.append(key, value);
                }
            }
            
            // Build the URL and redirect
            const url = '{{ route("properties.index") }}' + (params.toString() ? '?' + params.toString() : '');
            window.location.href = url;
        }

        // Update filters automatically when select/input changes
        function updateFilters() {
            // Store current filter state
            const filterForm = document.getElementById('filterForm');
            const formData = new FormData(filterForm);
            const filters = {};
            
            for (let [key, value] of formData.entries()) {
                if (value && value.trim() !== '') {
                    filters[key] = value;
                }
            }
            
            // Also get search input value
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput && searchInput.value.trim() !== '') {
                filters.search = searchInput.value.trim();
            }
            
            sessionStorage.setItem('propertyFilters', JSON.stringify(filters));
        }

        // Clear search function
        function clearSearch() {
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.value = '';
                updateFilters();
            }
            // Redirect to base URL without search parameter
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.delete('search');
            const url = '{{ route("properties.index") }}' + (urlParams.toString() ? '?' + urlParams.toString() : '');
            window.location.href = url;
        }

        // Reset individual filter to default
        function resetIndividualFilter() {
            const filterForm = document.getElementById('filterForm');
            const formData = new FormData(filterForm);
            const params = new URLSearchParams();
            
            // Get current URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            
            // Remove one parameter at a time (user can click multiple times)
            let removed = false;
            for (let [key, value] of formData.entries()) {
                if (value && value.trim() !== '' && !removed) {
                    // Remove this filter
                    removed = true;
                    continue;
                }
                if (value && value.trim() !== '') {
                    params.append(key, value);
                }
            }
            
            // Build the URL and redirect
            const url = '{{ route("properties.index") }}' + (params.toString() ? '?' + params.toString() : '');
            window.location.href = url;
        }

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';

        // Filter toggle functionality
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
        
        // Initialize filters state for mobile
        if (window.innerWidth < 640) {
            const filtersContent = document.getElementById('filtersContent');
            if (filtersContent) {
                filtersContent.classList.remove('show');
            }
        }
        
        // Add intersection observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        // Observe all property cards
        document.querySelectorAll('.property-card').forEach(card => {
            observer.observe(card);
        });
    </script>
</body>
</html>
