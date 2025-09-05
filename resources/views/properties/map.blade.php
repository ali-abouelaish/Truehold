<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="cache-buster" content="{{ time() }}">
    <meta name="version" content="3.0.0">
    <meta name="build-time" content="{{ now() }}">
    <title>Property Map - Property Scraper</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        console.log('🧪 Page head script - if you see this, the page is loading');
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🧪 Page DOM loaded - basic page functionality working');
        });
    </script>
    
    <!-- Google Maps CSS -->
    <style>
        .gm-style {
            font-family: inherit;
        }
        
        .price-label {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: 600;
            color: #1f2937;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
            white-space: nowrap;
            pointer-events: none;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
    </style>
    
    <style>
        /* Mobile-first responsive design - Bigger map */
        #map {
            height: calc(100vh - 80px);
            min-height: 500px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            background: #f8f9fa;
        }
        
        @media (min-width: 768px) {
            #map {
                height: calc(100vh - 120px);
                min-height: 600px;
            }
        }
        
        @media (min-width: 1024px) {
            #map {
                height: calc(100vh - 100px);
                min-height: 700px;
            }
        }
        
        .map-container {
            position: relative;
            width: 100%;
            height: calc(100vh - 80px);
            min-height: 500px;
        }
        
        @media (min-width: 768px) {
            .map-container {
                height: calc(100vh - 120px);
                min-height: 600px;
            }
        }
        
        @media (min-width: 1024px) {
            .map-container {
                height: calc(100vh - 100px);
                min-height: 700px;
            }
        }
        
        .map-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            background: #f8f9fa;
        }
        
        .property-marker {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .property-marker:hover {
            transform: scale(1.1);
        }
        
        /* Mobile-first map controls */
        .map-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border: 1px solid #e5e7eb;
            max-width: 280px;
            width: calc(100vw - 20px);
        }
        
        @media (min-width: 640px) {
            .map-controls {
                top: 20px;
                right: 20px;
                padding: 16px;
                max-width: 320px;
                width: auto;
            }
        }
        
        @media (min-width: 768px) {
            .map-controls {
                max-width: 350px;
            }
        }
        
        /* Mobile-friendly company legend */
        .company-legend {
            max-height: 120px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        
        .company-legend::-webkit-scrollbar {
            width: 4px;
        }
        
        .company-legend::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 2px;
        }
        
        .company-legend::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        
        .company-legend::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        @media (min-width: 768px) {
            .company-legend {
                max-height: 200px;
            }
        }
        
        /* Mobile-friendly buttons */
        .map-controls .formal-button {
            padding: 10px 16px;
            font-size: 13px;
            min-height: 44px; /* Touch-friendly height */
        }
        
        @media (min-width: 640px) {
            .map-controls .formal-button {
                padding: 12px 20px;
                font-size: 14px;
            }
        }
        
        .image-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            padding: 20px;
        }
        
        .image-modal img {
            max-width: 95%;
            max-height: 95%;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }
        
        @media (min-width: 640px) {
            .image-modal img {
                max-width: 90%;
                max-height: 90%;
            }
        }
        
        .image-modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 44px; /* Touch-friendly */
        }
        
        @media (min-width: 640px) {
            .image-modal-close {
                top: 20px;
                right: 20px;
                font-size: 24px;
                width: 40px;
                height: 40px;
            }
        }
        
        .property-count-badge {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            padding: 8px 12px;
            font-size: 12px;
        }
        
        @media (min-width: 640px) {
            .property-count-badge {
                padding: 12px 16px;
                font-size: 14px;
            }
        }
        
        .formal-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
            min-height: 44px; /* Touch-friendly height */
        }
        
        .formal-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        /* Mobile-friendly price labels */
        .price-label {
            font-size: 10px;
            padding: 1px 4px;
        }
        
        @media (min-width: 640px) {
            .price-label {
                font-size: 11px;
                padding: 2px 6px;
            }
        }
        
        /* Mobile-friendly filter collapse */
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
        }
        
        @media (min-width: 768px) {
            .filter-toggle {
                display: none;
            }
        }
        
        .filters-collapsible {
            display: none;
        }
        
        @media (min-width: 768px) {
            .filters-collapsible {
                display: block;
            }
        }
        
        .filters-collapsible.show {
            display: block;
        }
        
        /* Mobile-friendly info windows */
        .gm-style .gm-style-iw-c {
            max-width: 280px !important;
            padding: 0 !important;
        }
        
        .gm-style .gm-style-iw-d {
            overflow: hidden !important;
        }
        
        @media (min-width: 640px) {
            .gm-style .gm-style-iw-c {
                max-width: 320px !important;
            }
        }
        
        /* Mobile touch improvements */
        @media (max-width: 767px) {
            /* Ensure touch targets are at least 44px */
            button, select, input, a {
                min-height: 44px;
                min-width: 44px;
            }
            
            /* Improve mobile scrolling */
            .map-container {
                -webkit-overflow-scrolling: touch;
            }
            
            /* Mobile-friendly spacing */
            .map-controls {
                max-height: 70vh;
                overflow-y: auto;
            }
            
            /* Mobile-friendly company legend */
            .company-legend {
                max-height: 100px;
            }
            
            /* Mobile-friendly buttons */
            .map-controls .formal-button {
                min-height: 48px;
                font-size: 14px;
            }
        }
        
        /* Landscape mobile improvements */
        @media (max-width: 767px) and (orientation: landscape) {
            .map-controls {
                max-height: 50vh;
            }
            
            .company-legend {
                max-height: 80px;
            }
        }
        
        /* Very small screens */
        @media (max-width: 375px) {
            .map-controls {
                max-width: calc(100vw - 20px);
                right: 10px;
                left: 10px;
                width: auto;
            }
            
            .property-count-badge {
                bottom: 10px;
                left: 10px;
                right: 10px;
                text-align: center;
            }
        }
        
        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .price-label {
                font-weight: 500;
            }
            
            .map-controls .formal-button {
                font-weight: 600;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.properties-navigation')
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 sm:py-3">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-2 sm:space-y-0">
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900">Property Map</h1>
                    <nav class="flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-4">
                        @auth
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-800 font-medium text-sm">
                            <i class="fas fa-cog mr-1"></i>Admin
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800 font-medium text-sm">
                            <i class="fas fa-sign-in-alt mr-1"></i>Login
                        </a>
                        @endauth
                        <a href="{{ route('properties.index') }}" class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                            <i class="fas fa-th-large mr-1"></i>List View
                        </a>
                        <a href="{{ route('properties.map') }}" class="text-blue-600 font-medium text-sm">
                            <i class="fas fa-map mr-1"></i>Map View
                        </a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Search Filters -->
        <div class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
            <div class="max-w-7xl mx-auto">
                <!-- Mobile filter toggle -->
                <button type="button" class="filter-toggle" onclick="toggleFilters()">
                    <i class="fas fa-filter mr-2"></i>
                    <span id="filterToggleText">Show Filters</span>
                    <i class="fas fa-chevron-down ml-2" id="filterToggleIcon"></i>
                </button>
                
                <div class="flex justify-between items-center mb-4 sm:mb-6">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Search Filters</h2>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('properties.map') }}" class="text-gray-600 hover:text-gray-800 font-medium text-sm sm:text-base">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </a>
                        <a href="{{ route('properties.index') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm sm:text-base">
                            <i class="fas fa-th-large mr-2"></i>Switch to List View
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
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            @if(request('search'))
                                <a href="{{ route('properties.map') }}" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Search through property titles, descriptions, and locations
                    </div>
                </div>
                
                <div class="filters-collapsible" id="filtersContent">
                    <form method="GET" action="{{ route('properties.map') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                            <select name="location" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2 sm:py-3 px-3 sm:px-4 text-sm sm:text-base">
                                <option value="">All Locations</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                        {{ $location }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Property Type</label>
                            <select name="property_type" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2 sm:py-3 px-3 sm:px-4 text-sm sm:text-base">
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
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Management Company</label>
                            <select name="management_company" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2 sm:py-3 px-3 sm:px-4 text-sm sm:text-base">
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
                            <label class="block text-sm font-semibold text-gray-700 mb-2">London Area</label>
                            <select name="london_area" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2 sm:py-3 px-3 sm:px-4 text-sm sm:text-base">
                                <option value="">All Areas</option>
                                <option value="central" {{ request('london_area') == 'central' ? 'selected' : '' }}>Central London</option>
                                <option value="east" {{ request('london_area') == 'east' ? 'selected' : '' }}>East London</option>
                                <option value="north" {{ request('london_area') == 'north' ? 'selected' : '' }}>North London</option>
                                <option value="south" {{ request('london_area') == 'south' ? 'selected' : '' }}>South London</option>
                                <option value="west" {{ request('london_area') == 'west' ? 'selected' : '' }}>West London</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Couples Allowed</label>
                            <select name="couples_allowed" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2 sm:py-3 px-3 sm:px-4 text-sm sm:text-base">
                                <option value="">All Properties</option>
                                <option value="yes" {{ request('couples_allowed') == 'yes' ? 'selected' : '' }}>Couples Welcome</option>
                                <option value="no" {{ request('couples_allowed') == 'no' ? 'selected' : '' }}>Singles Only</option>
                            </select>
                        </div>
                    </form>
                    
                    <!-- Second row of filters -->
                    <form method="GET" action="{{ route('properties.map') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mt-4 sm:mt-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Min Price</label>
                            <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                   placeholder="£0" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2 sm:py-3 px-3 sm:px-4 text-sm sm:text-base">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Max Price</label>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                   placeholder="£5000" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2 sm:py-3 px-3 sm:px-4 text-sm sm:text-base">
                        </div>
                        
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="flex-1 formal-button text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-medium shadow-lg text-sm sm:text-base">
                                <i class="fas fa-search mr-2"></i>Search
                            </button>
                            <a href="{{ route('properties.map') }}" class="px-4 sm:px-6 py-2 sm:py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium shadow-lg text-sm sm:text-base transition-colors duration-200">
                                <i class="fas fa-times mr-2"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <main class="w-full px-2 sm:px-4 py-2 sm:py-4">
            <div class="map-container">
                <!-- Map Controls -->
                <div class="map-controls">
                    <div class="flex flex-col space-y-2 sm:space-y-3">
                        <!-- Search Box -->
                        <div class="search-box bg-white p-2 rounded-lg shadow-md border border-gray-200">
                            <div class="relative">
                                <input type="text" id="mapSearch" placeholder="Search locations..." 
                                       class="w-full px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                            <div id="searchResults" class="mt-2 max-h-40 overflow-y-auto hidden">
                                <!-- Search results will be populated here -->
                            </div>
                        </div>
                        
                        <button id="fitBounds" class="formal-button text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium shadow-md">
                            <i class="fas fa-crosshairs mr-1 sm:mr-2"></i>
                            <span class="hidden sm:inline">Fit All</span>
                            <span class="sm:hidden">Fit</span>
                        </button>
                        
                        <button id="focusLondon" class="formal-button text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium shadow-md">
                            <i class="fas fa-map-marker-alt mr-1 sm:mr-2"></i>
                            <span class="hidden sm:inline">Focus London</span>
                            <span class="sm:hidden">London</span>
                        </button>
                        
                        <button id="toggleClustering" class="formal-button text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium shadow-md">
                            <i class="fas fa-layer-group mr-1 sm:mr-2"></i>
                            <span class="hidden sm:inline">Toggle Clustering</span>
                            <span class="sm:hidden">Cluster</span>
                        </button>
                        
                        @auth
                        <!-- Company Legend -->
                        <div class="company-legend bg-white p-2 sm:p-3 rounded-lg shadow-md border border-gray-200">
                            <h4 class="text-xs sm:text-sm font-semibold text-gray-800 mb-2">Company Colors</h4>
                            <div class="space-y-1 text-xs">
                                @foreach($managementCompanies as $company)
                                    @if($company && $company !== 'N/A' && $company !== '')
                                        <div class="flex items-center">
                                            @php
                                                $companyColor = \App\Models\Property::getCompanyColor($company);
                                            @endphp
                                            <div class="w-2 sm:w-3 h-2 sm:h-3 rounded-full mr-1 sm:mr-2 flex-shrink-0" style="background-color: {{ $companyColor['fill'] }}; border: 1px solid {{ $companyColor['stroke'] }};"></div>
                                            <span class="truncate text-xs">{{ $company }}</span>
                                        </div>
                                    @endif
                                @endforeach
                                <!-- Add "Other" entry for null management companies -->
                                <div class="flex items-center">
                                    @php
                                        $otherColor = \App\Models\Property::getCompanyColor(null);
                                    @endphp
                                    <div class="w-2 sm:w-3 h-2 sm:h-3 rounded-full mr-1 sm:mr-2 flex-shrink-0" style="background-color: {{ $otherColor['fill'] }}; border: 1px solid {{ $otherColor['stroke'] }};"></div>
                                    <span class="truncate text-xs">Other</span>
                                </div>
                            </div>
                        </div>
                        @else
                        <!-- Login Prompt for Company Legend -->
                        <div class="bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg shadow-md border border-gray-200">
                            <div class="text-center">
                                <i class="fas fa-lock text-sm mb-1"></i>
                                <p class="text-xs">Company info available to registered users</p>
                                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Login</a>
                            </div>
                        </div>
                        @endauth
                    </div>
                </div>

                <!-- Map -->
                <div id="map">
                    <div id="mapLoading" class="map-loading">
                        <div class="text-center px-4">
                            <i class="fas fa-spinner fa-spin text-3xl sm:text-4xl text-blue-600 mb-3 sm:mb-4"></i>
                            <p class="text-gray-600 text-sm sm:text-base">Loading map...</p>
                            <p class="text-xs sm:text-sm text-gray-500 mt-2">This may take a few seconds</p>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden properties data -->
                <div id="properties-data" data-properties="{{ json_encode($properties) }}" style="display: none;"></div>
                
                <!-- Property Count Badge -->
                <div class="absolute bottom-4 sm:bottom-6 left-4 sm:left-6 property-count-badge px-3 sm:px-6 py-2 sm:py-3 rounded-lg">
                    <div class="text-xs sm:text-sm text-gray-700">
                        <i class="fas fa-map-marker-alt text-red-500 mr-1 sm:mr-2"></i>
                        <span class="font-semibold">{{ $properties->count() }}</span> properties
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="image-modal">
        <div class="image-modal-close" onclick="closeImageModal()">
            <i class="fas fa-times"></i>
        </div>
        <img id="modalImage" src="" alt="Property Image">
    </div>

    <!-- Google Maps JS -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key', 'YOUR_API_KEY') }}&libraries=places&callback=initMap&v=weekly&loading=async&t={{ time() }}&r={{ rand(1000, 9999) }}"></script>
    <!-- Marker Clustering Library -->
    <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js?v=1.0.0&t={{ time() }}&r={{ rand(1000, 9999) }}"></script>
    
    <script id="map-script-{{ uniqid() }}">
        // Always define initMap function first, regardless of duplicate execution
        window.initMap = function() {
            console.log('🎯 Google Maps API loaded, initializing map...');
            
            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                console.error('Google Maps not loaded');
                showSimpleFallback('Google Maps library failed to load');
                return;
            }
            
            console.log('Google Maps loaded, initializing map...');
            
            const mapContainer = document.getElementById('map');
            if (!mapContainer) {
                console.error('Map container not found');
                return;
            }
            
            // Add timeout to prevent infinite loading
            const loadingTimeout = setTimeout(() => {
                console.log('Loading timeout reached, showing fallback');
                showSimpleFallback('Map is taking too long to load. This usually happens when there are many properties. Try filtering by location.');
            }, 8000); // 8 second timeout
            
            try {
                // Clear loading spinner
                mapContainer.innerHTML = '';
                
                // Create map with Google Maps
                map = new google.maps.Map(mapContainer, {
                    center: { lat: 51.505, lng: -0.09 }, // London coordinates
                    zoom: 10,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    mapTypeControl: true,
                    streetViewControl: true,
                    fullscreenControl: true,
                    zoomControl: true,
                    styles: [
                        {
                            featureType: 'poi',
                            elementType: 'labels',
                            stylers: [{ visibility: 'off' }]
                        }
                    ]
                });
                console.log('Map created:', map);
                
                // Get properties data - load from a data attribute instead
                let properties = [];
                try {
                    console.log('🧪 Attempting to load properties from data attribute...');
                    const propertiesData = document.getElementById('properties-data');
                    if (propertiesData && propertiesData.dataset.properties) {
                        const rawProperties = JSON.parse(propertiesData.dataset.properties);
                        
                        // Clean and validate properties data
                        properties = rawProperties.filter(property => {
                            // Check if property exists and has required fields
                            if (!property || typeof property !== 'object') {
                                console.warn('Invalid property object:', property);
                                return false;
                            }
                            
                            // Ensure all string fields are properly handled
                            const cleanProperty = {
                                id: property.id || null,
                                title: property.title || 'Untitled Property',
                                location: property.location || 'Location not specified',
                                latitude: property.latitude || null,
                                longitude: property.longitude || null,
                                price: property.price || null,
                                formatted_price: property.formatted_price || 'Price not specified',
                                property_type: property.property_type || null,
                                management_company: property.management_company || null,
                                available_date: property.available_date || null,
                                amenities: property.amenities || null,
                                furnishings: property.furnishings || null,
                                bills_included: property.bills_included || null,
                                balcony_roof_terrace: property.balcony_roof_terrace || null,
                                garden_patio: property.garden_patio || null,
                                parking: property.parking || null,
                                description: property.description || null,
                                high_quality_photos_array: property.high_quality_photos_array || null,
                                all_photos_array: property.all_photos_array || null,
                                first_photo_url: property.first_photo_url || null
                            };
                            
                            // Replace the original property with cleaned version
                            Object.assign(property, cleanProperty);
                            
                            return true;
                        });
                        
                        allProperties = properties; // Store for search functionality
                        console.log('🚀 Properties loaded and cleaned from data attribute:', properties.length);
                        console.log('📋 Properties data sample:', properties.slice(0, 3));
                    } else {
                        console.log('📋 No properties data found in data attribute');
                        properties = [];
                        allProperties = [];
                    }
                } catch (error) {
                    console.error('❌ Error loading properties data:', error);
                    console.error('❌ Error message:', error.message);
                    console.error('❌ Error stack:', error.stack);
                    properties = [];
                    allProperties = [];
                }
                
                // Check if properties have the expected structure
                if (properties.length > 0) {
                    const firstProp = properties[0];
                    console.log('🔍 First property structure:', {
                        hasId: 'id' in firstProp,
                        hasTitle: 'title' in firstProp,
                        hasLocation: 'location' in firstProp,
                        hasLatitude: 'latitude' in firstProp,
                        hasLongitude: 'longitude' in firstProp,
                        latitude: firstProp.latitude,
                        longitude: firstProp.longitude
                    });
                }
                
                // Debug: Log property coordinates to see the geographic spread
                const propertiesWithCoords = properties.filter(p => {
                    try {
                        // Check if property exists and has valid coordinates
                        if (!p || typeof p !== 'object') {
                            return false;
                        }
                        
                        const lat = p.latitude;
                        const lng = p.longitude;
                        
                        // Check if coordinates exist and are valid numbers
                        if (!lat || !lng) {
                            return false;
                        }
                        
                        const latNum = parseFloat(lat);
                        const lngNum = parseFloat(lng);
                        
                        // Check if parsed numbers are valid and within reasonable bounds
                        if (isNaN(latNum) || isNaN(lngNum)) {
                            return false;
                        }
                        
                        // Check if coordinates are within reasonable geographic bounds
                        if (latNum < -90 || latNum > 90 || lngNum < -180 || lngNum > 180) {
                            console.warn('Property has invalid coordinates:', p.id, latNum, lngNum);
                            return false;
                        }
                        
                        return true;
                    } catch (error) {
                        console.warn('Error filtering property:', p, error);
                        return false;
                    }
                });
                console.log('Properties with coordinates:', propertiesWithCoords.length);
                
                if (propertiesWithCoords.length > 0) {
                    const lats = propertiesWithCoords.map(p => parseFloat(p.latitude));
                    const lngs = propertiesWithCoords.map(p => parseFloat(p.longitude));
                    const minLat = Math.min(...lats);
                    const maxLat = Math.max(...lats);
                    const minLng = Math.min(...lngs);
                    const maxLng = Math.max(...lngs);
                    console.log('Geographic bounds:', { minLat, maxLat, minLng, maxLng });
                    console.log('Latitude range:', maxLat - minLat);
                    console.log('Longitude range:', maxLng - minLng);
                }
                
                // Create bounds object and markers array
                const bounds = new google.maps.LatLngBounds();
                // Clear global markers array and use it
                markers.length = 0;
                
                // Create info window
                const infoWindow = new google.maps.InfoWindow();
                
                // Add properties as markers
                console.log('🔍 Starting to create markers for all properties...');
                console.log(`📊 Total properties to process: ${propertiesWithCoords.length}`);
                let markerCount = 0;
                
                propertiesWithCoords.forEach((property, index) => {
                    try {
                        console.log(`📍 Creating marker ${index + 1}/${propertiesWithCoords.length}:`);
                        console.log(`   ID: ${property.id || 'N/A'}`);
                        console.log(`   Title: ${property.title || 'Untitled'}`);
                        console.log(`   Location: ${property.location || 'Unknown'}`);
                        
                        // Safely parse coordinates with null checks
                        const lat = property.latitude ? parseFloat(property.latitude) : null;
                        const lng = property.longitude ? parseFloat(property.longitude) : null;
                        
                        if (!lat || !lng || isNaN(lat) || isNaN(lng)) {
                            console.warn(`Skipping property ${property.id} - invalid coordinates:`, lat, lng);
                            return;
                        }
                        
                        const position = { lat, lng };
                        console.log(`   Coordinates: ${position.lat}, ${position.lng}`);
                        console.log(`   Price: ${property.price || 'N/A'}`);
                        
                        // Get company color with null handling
                        const companyColor = getCompanyColor(property.management_company);
                        
                        // Create marker with safe defaults
                        const marker = new google.maps.Marker({
                            position: position,
                            title: property.title || 'Property',
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 8,
                                fillColor: companyColor.fill,
                                fillOpacity: 1,
                                strokeColor: companyColor.stroke,
                                strokeWeight: 2
                            }
                        });
                        
                        // Add price label using a custom overlay
                        const priceLabel = new google.maps.OverlayView();
                        priceLabel.onAdd = function() {
                            const div = document.createElement('div');
                            div.className = 'price-label';
                            
                            // Format price with comprehensive null handling
                            let priceText = 'Price N/A';
                            
                            try {
                                // Check if property exists and has price data
                                if (property && property.price && property.price !== 'N/A' && property.price !== '' && property.price !== null && property.price !== undefined) {
                                    // If price is a number, format it as currency
                                    const priceNum = parseFloat(property.price);
                                    if (!isNaN(priceNum) && priceNum > 0) {
                                        priceText = `£${priceNum.toLocaleString()}`;
                                    } else if (typeof property.price === 'string' && property.price.trim() !== '') {
                                        // If it's already a formatted string, use it
                                        priceText = property.price.trim();
                                    }
                                } else if (property && property.formatted_price && property.formatted_price !== 'N/A' && property.formatted_price !== '' && property.formatted_price !== null && property.formatted_price !== undefined) {
                                    priceText = property.formatted_price;
                                }
                            } catch (error) {
                                console.warn('Error formatting price for property:', property.id, error);
                                priceText = 'Price N/A';
                            }
                            
                            div.textContent = priceText;
                            div.style.position = 'absolute';
                            div.style.zIndex = '1000';
                            this.div_ = div;
                            
                            try {
                                const panes = this.getPanes();
                                if (panes && panes.overlayImage) {
                                    panes.overlayImage.appendChild(div);
                                }
                            } catch (error) {
                                console.warn('Error adding price label to map:', error);
                            }
                        };
                        
                        priceLabel.draw = function() {
                            try {
                                const projection = this.getProjection();
                                if (!projection || !marker || !this.div_) {
                                    return;
                                }
                                
                                const position = projection.fromLatLngToDivPixel(marker.getPosition());
                                if (!position) {
                                    return;
                                }
                                
                                const div = this.div_;
                                if (div && div.offsetWidth && div.offsetHeight) {
                                    div.style.left = (position.x - div.offsetWidth / 2) + 'px';
                                    div.style.top = (position.y - div.offsetHeight - 20) + 'px';
                                }
                            } catch (error) {
                                console.warn('Error drawing price label:', error);
                            }
                        };
                        
                        priceLabel.setMap(map);
                        
                        // Create info window content with null handling
                        const infoContent = createInfoWindowContent(property);
                        
                        // Add click event with null checks
                        marker.addListener('click', function() {
                            try {
                                if (infoWindow && infoContent) {
                                    infoWindow.setContent(infoContent);
                                    infoWindow.open(map, marker);
                                }
                            } catch (error) {
                                console.warn('Error opening info window:', error);
                            }
                        });
                        
                        // Add to bounds with null checks
                        try {
                            if (bounds && position && position.lat && position.lng) {
                                bounds.extend(position);
                            }
                            if (markers && Array.isArray(markers)) {
                                markers.push(marker);
                            }
                        } catch (error) {
                            console.warn('Error adding marker to bounds or markers array:', error);
                        }
                        
                        markerCount++;
                        console.log(`   ✅ Marker ${markerCount} created and added to map`);
                    } catch (error) {
                        console.error(`❌ Error creating marker for property ${index}:`, error, property);
                    }
                });
                
                // Create marker clusterer with null handling
                try {
                    if (typeof MarkerClusterer !== 'undefined' && map && markers && markers.length > 0) {
                        markerClusterer = new MarkerClusterer(map, markers, {
                            imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
                            gridSize: 50,
                            maxZoom: 15,
                            styles: [
                                {
                                    textColor: 'white',
                                    url: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m1.png',
                                    height: 53,
                                    width: 53
                                },
                                {
                                    textColor: 'white',
                                    url: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m2.png',
                                    height: 56,
                                    width: 56
                                },
                                {
                                    textColor: 'white',
                                    url: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m3.png',
                                    height: 66,
                                    width: 66
                                }
                            ]
                        });
                        console.log('✅ Marker clusterer created');
                    } else {
                        console.log('⚠️ MarkerClusterer not available or no markers, showing individual markers');
                        // Fallback: add markers directly to map with null checks
                        if (markers && Array.isArray(markers) && map) {
                            markers.forEach(marker => {
                                try {
                                    if (marker && typeof marker.setMap === 'function') {
                                        marker.setMap(map);
                                    }
                                } catch (error) {
                                    console.warn('Error adding marker to map:', error);
                                }
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error creating marker clusterer:', error);
                    // Fallback: add markers directly to map
                    if (markers && Array.isArray(markers) && map) {
                        markers.forEach(marker => {
                            try {
                                if (marker && typeof marker.setMap === 'function') {
                                    marker.setMap(map);
                                }
                            } catch (error) {
                                console.warn('Error adding marker to map:', error);
                            }
                        });
                    }
                }
                
                console.log('Properties added to map:', propertiesWithCoords.length);
                console.log('📊 Final Marker Summary:');
                console.log(`   Total properties with coordinates: ${propertiesWithCoords.length}`);
                console.log(`   Markers created: ${markerCount}`);
                console.log(`   Global markers array length: ${markers.length}`);
                console.log(`   Bounds extended: ${bounds.isEmpty() ? 'No' : 'Yes'}`);
                
                if (markers.length > 0) {
                    console.log('   ✅ Global markers array is populated correctly');
                } else {
                    console.log('   ❌ Global markers array is empty - this is the problem!');
                }
                
                // Fit map to show all properties or default to London with null handling
                try {
                    if (propertiesWithCoords.length > 0 && bounds && !bounds.isEmpty()) {
                        console.log('Fitting map to bounds:', bounds);
                        
                        // Check if bounds are too large
                        const ne = bounds.getNorthEast();
                        const sw = bounds.getSouthWest();
                        
                        if (ne && sw) {
                            const latRange = ne.lat() - sw.lat();
                            const lngRange = ne.lng() - sw.lng();
                            
                            console.log('Bounds range - Lat:', latRange, 'Lng:', lngRange);
                            
                            // If bounds are too large or properties are spread out, focus on London
                            if (latRange > 10 || lngRange > 10) {
                                console.log('Bounds too large, focusing on London');
                                map.setCenter({ lat: 51.505, lng: -0.09 }); // London center
                                map.setZoom(10); // Good zoom for London area
                                showWarning('Properties are spread across a large area. Map is focused on London. Use filters to see specific locations more clearly.');
                            } else {
                                console.log('Fitting to bounds with padding');
                                map.fitBounds(bounds, 50); // 50px padding
                            }
                        } else {
                            console.log('Invalid bounds, focusing on London');
                            map.setCenter({ lat: 51.505, lng: -0.09 }); // London center
                            map.setZoom(10); // Good zoom for London area
                        }
                    } else {
                        // No properties with coordinates, ensure map is focused on London
                        console.log('No properties with coordinates, focusing on London');
                        map.setCenter({ lat: 51.505, lng: -0.09 }); // London center
                        map.setZoom(10); // Good zoom for London area
                    }
                } catch (error) {
                    console.error('Error fitting map bounds:', error);
                    // Fallback to London
                    map.setCenter({ lat: 51.505, lng: -0.09 }); // London center
                    map.setZoom(10); // Good zoom for London area
                }
                
                // Change cursor on hover for markers
                map.addListener('idle', function() {
                    map.getDiv().style.cursor = 'default';
                });
                
                // Clear timeout since map loaded successfully
                clearTimeout(loadingTimeout);
                console.log('Map initialization complete!');
                
            } catch (error) {
                console.error('Map error:', error);
                clearTimeout(loadingTimeout);
                showSimpleFallback('Map failed to load: ' + error.message);
            }
        };
        
        // Prevent duplicate execution
        if (window.mapScriptLoaded) {
            console.log('⚠️ Map script already loaded, skipping duplicate execution');
            // Exit early without return statement
        } else {
            window.mapScriptLoaded = true;
        
        console.log('🧪 Basic script test - if you see this, JavaScript is working');
        console.log('🚀 Map script loaded!');
        console.log('🕒 Script loaded at:', new Date().toISOString());
        console.log('🔄 Cache busting timestamp:', Date.now());
        console.log('🆔 Unique script ID:', '{{ uniqid() }}');
        console.log('🔧 Script version:', '3.0.0');
        console.log('🎲 Random number:', {{ rand(10000, 99999) }});
        
        // Simple test - properties will be loaded later
        console.log('🧪 Basic script loaded successfully');
        console.log('📊 Properties will be loaded when DOM is ready');
        
        // Global variables for map functionality (with duplicate check)
        if (typeof window.mapInitialized === 'undefined') {
            window.mapInitialized = true;
            
            let map = null;
            let markers = [];
            let markerClusterer = null;
            let infoWindow = null;
            let allProperties = [];
            let clusteringEnabled = true;
        } else {
            console.log('⚠️ Map variables already initialized, using existing ones');
            var map = window.map || null;
            var markers = window.markers || [];
            var markerClusterer = window.markerClusterer || null;
            var infoWindow = window.infoWindow || null;
            var allProperties = window.allProperties || [];
            var clusteringEnabled = window.clusteringEnabled || true;
        }
        
        console.log('📋 Variables initialized:', { map, markers: markers.length, infoWindow });
        
        function getCompanyColor(company) {
            if (!company || company === 'N/A' || company === '') {
                return { fill: '#6b7280', stroke: '#ffffff' }; // Gray for unknown
            }
            
            // Define color scheme for different companies - matching the PHP model
            const companyColors = {
                'iFlatShare': { fill: '#3b82f6', stroke: '#ffffff' }, // Blue
                'AK&PROPERTIES': { fill: '#ef4444', stroke: '#ffffff' }, // Red
                'Banksia Limited': { fill: '#10b981', stroke: '#ffffff' }, // Green
                'Built Asset Management Limited': { fill: '#f59e0b', stroke: '#ffffff' }, // Amber
                'Capital Living': { fill: '#8b5cf6', stroke: '#ffffff' }, // Purple
                'JD Corp Management': { fill: '#ec4899', stroke: '#ffffff' }, // Pink
                'North Kensington Property Consultants': { fill: '#06b6d4', stroke: '#ffffff' }, // Cyan
                'Pisoria Ltd': { fill: '#84cc16', stroke: '#ffffff' }, // Lime
                'UK London Flat': { fill: '#f97316', stroke: '#ffffff' }, // Orange
                'COME TO LONDON LIMITED': { fill: '#6366f1', stroke: '#ffffff' }, // Indigo
            };
            
            // Check for exact matches first
            if (companyColors[company]) {
                return companyColors[company];
            }
            
            // Check for partial matches
            for (const [key, color] of Object.entries(companyColors)) {
                if (company.toLowerCase().includes(key.toLowerCase()) || key.toLowerCase().includes(company.toLowerCase())) {
                    return companyColors[key];
                }
            }
            
            // Generate a consistent color based on company name hash
            const hash = company.split('').reduce((a, b) => {
                a = ((a << 5) - a) + b.charCodeAt(0);
                return a & a;
            }, 0);
            
            const colors = [
                { fill: '#3b82f6', stroke: '#ffffff' }, // Blue
                { fill: '#ef4444', stroke: '#ffffff' }, // Red
                { fill: '#10b981', stroke: '#ffffff' }, // Green
                { fill: '#f59e0b', stroke: '#ffffff' }, // Amber
                { fill: '#8b5cf6', stroke: '#ffffff' }, // Purple
                { fill: '#ec4899', stroke: '#ffffff' }, // Pink
                { fill: '#06b6d4', stroke: '#ffffff' }, // Cyan
                { fill: '#84cc16', stroke: '#ffffff' }, // Lime
                { fill: '#f97316', stroke: '#ffffff' }, // Orange
                { fill: '#6366f1', stroke: '#ffffff' }  // Indigo
            ];
            
            return colors[Math.abs(hash) % colors.length];
        }

        function createInfoWindowContent(property) {
            try {
                // Try to get first photo from high_quality_photos_array, fallback to all_photos_array, then first_photo_url
                let firstPhoto = '';
                if (property.high_quality_photos_array && property.high_quality_photos_array.length > 0) {
                    firstPhoto = property.high_quality_photos_array[0];
                } else if (property.all_photos_array && property.all_photos_array.length > 0) {
                    firstPhoto = property.all_photos_array[0];
                } else if (property.first_photo_url && property.first_photo_url !== 'N/A') {
                    firstPhoto = property.first_photo_url;
                }
                
                const photo = firstPhoto 
                    ? `<img src="${firstPhoto}" alt="${property.title || 'Property'}" style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px; margin-bottom: 8px; cursor: pointer;" onclick="openImageModal('${firstPhoto}', '${property.title || 'Property'}')">` 
                    : '';
                
                const companyColor = getCompanyColor(property.management_company);
                
                // Helper function to show field only if it has a meaningful value
                const showField = (value, label, icon = '') => {
                    if (value && value !== 'N/A' && value !== '' && value !== 'null' && value !== 'undefined') {
                        return `<div style="color: #666; margin-bottom: 4px; font-size: 12px;">
                            ${icon ? `<i class="fas fa-${icon}" style="color: #6b7280; margin-right: 4px;"></i>` : ''}
                            <strong>${label}:</strong> ${value}
                        </div>`;
                    }
                    return '';
                };
                
                return `
                <div style="padding: 8px; max-width: 300px;">
                    ${photo}
                    <h3 style="font-weight: bold; font-size: 16px; margin-bottom: 8px;">${property.title || 'Property'}</h3>
                    
                    ${showField(property.location, 'Location', 'map-marker-alt')}
                    
                    <div style="font-size: 18px; font-weight: bold; color: #2563eb; margin-bottom: 8px;">
                        ${property.formatted_price || 'Price not specified'}
                    </div>
                    
                    ${property.property_type ? `<span style="background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">${property.property_type}</span>` : ''}
                    
                    ${property.management_company && property.management_company !== 'N/A' ? `
                        <div style="margin-top: 8px; margin-bottom: 8px;">
                            <span style="background: ${companyColor.fill}; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                <i class="fas fa-building" style="margin-right: 4px;"></i>${property.management_company}
                            </span>
                        </div>
                    ` : ''}
                    
                    ${showField(property.available_date, 'Available', 'calendar')}
                    ${showField(property.amenities, 'Amenities', 'star')}
                    ${showField(property.furnishings, 'Furnishings', 'couch')}
                    ${showField(property.bills_included, 'Bills Included', 'bolt')}
                    ${showField(property.balcony_roof_terrace, 'Outdoor Space', 'tree')}
                    ${showField(property.garden_patio, 'Garden/Patio', 'leaf')}
                    ${showField(property.parking, 'Parking', 'car')}
                    
                    <div style="margin-top: 12px;">
                        <a href="/properties/${property.id}" style="background: #1f2937; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 14px; display: inline-block;">
                            <i class="fas fa-info-circle" style="margin-right: 4px;"></i>View Details
                        </a>
                    </div>
                </div>
            `;
            } catch (error) {
                console.error('Error creating info window content:', error, property);
                return `
                    <div style="padding: 8px; max-width: 300px;">
                        <h3 style="font-weight: bold; font-size: 16px; margin-bottom: 8px;">Property Information</h3>
                        <p style="color: #666;">Unable to display property details due to an error.</p>
                        <div style="margin-top: 12px;">
                            <a href="/properties/${property.id || '#'}" style="background: #1f2937; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 14px; display: inline-block;">
                                <i class="fas fa-info-circle" style="margin-right: 4px;"></i>View Details
                            </a>
                        </div>
                    </div>
                `;
            }
        }
        
        function showProgress(message) {
            const mapContainer = document.getElementById('map');
            const progressDiv = document.createElement('div');
            progressDiv.id = 'progressIndicator';
            progressDiv.style.cssText = `
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 20px;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 500;
                z-index: 1001;
                text-align: center;
                min-width: 300px;
            `;
            progressDiv.innerHTML = `
                <div style="margin-bottom: 16px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #60a5fa;"></i>
                </div>
                <div>${message}</div>
                <div id="progressText" style="margin-top: 8px; font-size: 14px; color: #d1d5db;"></div>
            `;
            mapContainer.appendChild(progressDiv);
        }
        
        function updateProgress(text) {
            const progressText = document.getElementById('progressText');
            if (progressText) {
                progressText.textContent = text;
            }
        }
        
        function hideProgress() {
            const progressDiv = document.getElementById('progressIndicator');
            if (progressDiv) {
                progressDiv.remove();
            }
        }
        
        function showWarning(message) {
            const mapContainer = document.getElementById('map');
            const warningDiv = document.createElement('div');
            warningDiv.style.cssText = `
                position: absolute;
                top: 20px;
                left: 20px;
                background: #fef3c7;
                border: 1px solid #f59e0b;
                color: #92400e;
                padding: 12px 16px;
                border-radius: 8px;
                font-size: 14px;
                    font-weight: 500;
                z-index: 1001;
                max-width: 400px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            `;
            warningDiv.innerHTML = `
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 8px; color: #f59e0b;"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" style="margin-left: 12px; background: none; border: none; color: #92400e; cursor: pointer; font-size: 16px;">×</button>
                </div>
            `;
            mapContainer.appendChild(warningDiv);
            
            // Auto-remove warning after 8 seconds
            setTimeout(() => {
                if (warningDiv.parentElement) {
                    warningDiv.remove();
                }
            }, 8000);
        }
        
        function showSimpleFallback(message) {
            const mapContainer = document.getElementById('map');
            let properties = [];
            try {
                const propertiesData = document.getElementById('properties-data');
                if (propertiesData && propertiesData.dataset.properties) {
                    properties = JSON.parse(propertiesData.dataset.properties);
                }
            } catch (error) {
                console.error('Error getting properties for fallback:', error);
                properties = [];
            }
            const propertiesWithCoords = properties.filter(p => p.latitude && p.longitude);
            
            if (propertiesWithCoords.length > 0) {
                mapContainer.innerHTML = `
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
                        <div style="text-align: center; margin-bottom: 24px;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #f59e0b; margin-bottom: 16px;"></i>
                            <h3 style="font-size: 20px; font-weight: 600; color: #374151; margin-bottom: 8px;">Interactive Map Unavailable</h3>
                            <p style="color: #6b7280; margin-bottom: 4px;">${message}</p>
                            <p style="color: #9ca3af; font-size: 14px;">Showing ${propertiesWithCoords.length} properties with coordinates:</p>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; max-height: 400px; overflow-y: auto;">
                            ${propertiesWithCoords.map(property => `
                                <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; transition: box-shadow 0.2s;">
                                    <div style="font-weight: 600; color: #111827; margin-bottom: 8px;">${property.title}</div>
                                    <div style="color: #6b7280; margin-bottom: 8px; font-size: 14px;">
                                        <i class="fas fa-map-marker-alt" style="color: #ef4444; margin-right: 4px;"></i>
                                        ${property.location || 'Location not specified'}
                                    </div>
                                    <div style="font-size: 16px; font-weight: bold; color: #2563eb; margin-bottom: 8px;">${property.formatted_price}</div>
                                    <div style="color: #9ca3af; font-size: 12px; margin-bottom: 12px;">
                                        Coordinates: ${property.latitude}, ${property.longitude}
                                    </div>
                                    <a href="/properties/${property.id}" style="color: #2563eb; font-size: 14px; font-weight: 500; text-decoration: none;">
                                        <i class="fas fa-external-link-alt" style="margin-right: 4px;"></i>View Details
                                    </a>
                                </div>
                            `).join('')}
                        </div>
                        <div style="text-align: center; margin-top: 24px;">
                            <button onclick="location.reload()" style="background: #2563b; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
                                <i class="fas fa-redo" style="margin-right: 8px;"></i>Retry Map
                            </button>
                        </div>
                    </div>
                `;
            } else {
                mapContainer.innerHTML = `
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 8px;">
                        <div style="text-align: center; padding: 32px;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #f59e0b; margin-bottom: 16px;"></i>
                            <h3 style="font-size: 20px; font-weight: 600; color: #374151; margin-bottom: 8px;">Map Error</h3>
                            <p style="color: #6b7280; margin-bottom: 16px;">${message}</p>
                            <button onclick="location.reload()" style="background: #2563eb; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
                                <i class="fas fa-redo" style="margin-right: 8px;"></i>Reload Page
                            </button>
                        </div>
                    </div>
                `;
            }
        }
        
        // Image modal functions
        function openImageModal(imageSrc, imageAlt) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            
            modalImg.src = imageSrc;
            modalImg.alt = imageAlt;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id === 'imageModal') {
                closeImageModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
        
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
        
        // Button event listeners
         document.addEventListener('DOMContentLoaded', function() {
             setTimeout(() => {
                 const fitBoundsBtn = document.getElementById('fitBounds');
                 const focusLondonBtn = document.getElementById('focusLondon');
                 
                 if (fitBoundsBtn) {
                     fitBoundsBtn.addEventListener('click', function() {
                         console.log('🔘 Fit All button clicked!');
                         console.log(`   Map exists: ${map ? 'Yes' : 'No'}`);
                         console.log(`   Markers array exists: ${markers ? 'Yes' : 'No'}`);
                         console.log(`   Markers array length: ${markers ? markers.length : 'N/A'}`);
                         
                         if (map && markers && markers.length > 0) {
                             console.log('✅ All conditions met, fitting bounds...');
                             const bounds = new google.maps.LatLngBounds();
                             markers.forEach((marker, index) => {
                                 const pos = marker.getPosition();
                                 console.log(`   Marker ${index + 1}: ${pos.lat()}, ${pos.lng()}`);
                                 bounds.extend(pos);
                             });
                             
                             // Check if bounds are too large
                             const ne = bounds.getNorthEast();
                             const sw = bounds.getSouthWest();
                             const latRange = ne.lat() - sw.lat();
                             const lngRange = ne.lng() - sw.lng();
                             
                             if (latRange > 10 || lngRange > 10) {
                                 // Focus on London instead of spread-out properties
                                 map.setCenter({ lat: 51.505, lng: -0.09 }); // London center
                                 map.setZoom(10); // Good zoom for London area
                                 showWarning('Properties are spread across a large area. Map is focused on London. Use filters to see specific locations more clearly.');
                             } else {
                                 map.fitBounds(bounds, 50);
                             }
                             
                             // If using clustering, refresh the clusterer
                             if (markerClusterer) {
                                 markerClusterer.repaint();
                             }
                         }
                     });
                 }
                 
                 if (focusLondonBtn) {
                     focusLondonBtn.addEventListener('click', function() {
                         console.log('🔘 Focus London button clicked!');
                         if (map) {
                             map.setCenter({ lat: 51.505, lng: -0.09 }); // London center
                             map.setZoom(10); // Good zoom for London area
                             console.log('✅ Map focused on London');
                         }
                     });
                 }
                 
                 // Initialize filters state for mobile
                 if (window.innerWidth < 768) {
                     const filtersContent = document.getElementById('filtersContent');
                     if (filtersContent) {
                         filtersContent.classList.remove('show');
                     }
                 }
                 
                 // Handle orientation change for mobile
                 window.addEventListener('orientationchange', function() {
                     setTimeout(function() {
                         if (map) {
                             google.maps.event.trigger(map, 'resize');
                         }
                     }, 500);
                 });
                 
                 // Handle window resize for responsive behavior
                 window.addEventListener('resize', function() {
                     if (map) {
                         google.maps.event.trigger(map, 'resize');
                     }
                 });
                 
                 // Search functionality
                 const mapSearch = document.getElementById('mapSearch');
                 const searchResults = document.getElementById('searchResults');
                 
                 if (mapSearch) {
                     mapSearch.addEventListener('input', function() {
                         const query = this.value.toLowerCase().trim();
                         if (query.length < 2) {
                             searchResults.classList.add('hidden');
                             return;
                         }
                         
                                                 // Search through properties with null handling
                        const results = allProperties.filter(property => {
                            if (!property || typeof property !== 'object') {
                                return false;
                            }
                            
                            try {
                                const title = property.title || '';
                                const location = property.location || '';
                                const description = property.description || '';
                                
                                return (title.toLowerCase().includes(query)) ||
                                       (location.toLowerCase().includes(query)) ||
                                       (description.toLowerCase().includes(query));
                            } catch (error) {
                                console.warn('Error filtering property in search:', property, error);
                                return false;
                            }
                        });
                         
                                                 if (results.length > 0) {
                            searchResults.innerHTML = results.slice(0, 5).map(property => {
                                try {
                                    const lat = property.latitude || '';
                                    const lng = property.longitude || '';
                                    const title = property.title || 'Untitled Property';
                                    const location = property.location || 'Location not specified';
                                    const price = property.formatted_price || 'Price N/A';
                                    
                                    return `
                                        <div class="search-result p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" 
                                             data-lat="${lat}" data-lng="${lng}" data-title="${title.replace(/"/g, '&quot;')}">
                                            <div class="font-medium text-sm">${title}</div>
                                            <div class="text-xs text-gray-600">${location}</div>
                                            <div class="text-xs text-blue-600">${price}</div>
                                        </div>
                                    `;
                                } catch (error) {
                                    console.warn('Error creating search result HTML:', property, error);
                                    return '';
                                }
                            }).filter(html => html !== '').join('');
                             searchResults.classList.remove('hidden');
                             
                                                         // Add click handlers to search results with null handling
                            searchResults.querySelectorAll('.search-result').forEach(result => {
                                result.addEventListener('click', function() {
                                    try {
                                        const latStr = this.dataset.lat || '';
                                        const lngStr = this.dataset.lng || '';
                                        const title = this.dataset.title || '';
                                        
                                        if (!latStr || !lngStr) {
                                            console.warn('Invalid coordinates in search result:', latStr, lngStr);
                                            return;
                                        }
                                        
                                        const lat = parseFloat(latStr);
                                        const lng = parseFloat(lngStr);
                                        
                                        if (!isNaN(lat) && !isNaN(lng) && map) {
                                            map.setCenter({ lat: lat, lng: lng });
                                            map.setZoom(15);
                                            
                                            // Find and click the corresponding marker
                                            if (markers && Array.isArray(markers)) {
                                                const marker = markers.find(m => {
                                                    try {
                                                        if (!m || typeof m.getPosition !== 'function') {
                                                            return false;
                                                        }
                                                        const pos = m.getPosition();
                                                        if (!pos) return false;
                                                        return Math.abs(pos.lat() - lat) < 0.0001 && Math.abs(pos.lng() - lng) < 0.0001;
                                                    } catch (error) {
                                                        console.warn('Error checking marker position:', error);
                                                        return false;
                                                    }
                                                });
                                                
                                                if (marker) {
                                                    google.maps.event.trigger(marker, 'click');
                                                }
                                            }
                                            
                                            searchResults.classList.add('hidden');
                                            if (mapSearch) {
                                                mapSearch.value = title;
                                            }
                                        }
                                    } catch (error) {
                                        console.warn('Error handling search result click:', error);
                                    }
                                });
                            });
                         } else {
                             searchResults.innerHTML = '<div class="p-2 text-sm text-gray-500">No properties found</div>';
                             searchResults.classList.remove('hidden');
                         }
                     });
                     
                     // Hide search results when clicking outside
                     document.addEventListener('click', function(e) {
                         if (!mapSearch.contains(e.target) && !searchResults.contains(e.target)) {
                             searchResults.classList.add('hidden');
                         }
                     });
                 }
                 
                                 // Clustering toggle functionality with null handling
                const toggleClusteringBtn = document.getElementById('toggleClustering');
                if (toggleClusteringBtn) {
                    toggleClusteringBtn.addEventListener('click', function() {
                        try {
                            clusteringEnabled = !clusteringEnabled;
                            
                            if (clusteringEnabled) {
                                // Enable clustering
                                if (markerClusterer && map) {
                                    markerClusterer.setMap(map);
                                } else if (typeof MarkerClusterer !== 'undefined' && map && markers && markers.length > 0) {
                                    markerClusterer = new MarkerClusterer(map, markers, {
                                        imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
                                        gridSize: 50,
                                        maxZoom: 15
                                    });
                                }
                                this.innerHTML = '<i class="fas fa-layer-group mr-1 sm:mr-2"></i><span class="hidden sm:inline">Disable Clustering</span><span class="sm:hidden">Uncluster</span>';
                            } else {
                                // Disable clustering
                                if (markerClusterer) {
                                    markerClusterer.setMap(null);
                                }
                                if (markers && Array.isArray(markers) && map) {
                                    markers.forEach(marker => {
                                        try {
                                            if (marker && typeof marker.setMap === 'function') {
                                                marker.setMap(map);
                                            }
                                        } catch (error) {
                                            console.warn('Error adding marker to map:', error);
                                        }
                                    });
                                }
                                this.innerHTML = '<i class="fas fa-layer-group mr-1 sm:mr-2"></i><span class="hidden sm:inline">Enable Clustering</span><span class="sm:hidden">Cluster</span>';
                            }
                        } catch (error) {
                            console.error('Error toggling clustering:', error);
                        }
                    });
                }
             }, 500);
         });
        } // End of else block for duplicate execution prevention
    </script>
</body>
</html>
