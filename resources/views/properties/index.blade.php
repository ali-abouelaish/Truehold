<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>TRUEHOLD - Find Your Perfect Home</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/truehold-logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/truehold-logo.jpg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Dark Mode Properties Index */
        html, body {
            font-family: 'Inter', sans-serif;
            background-color: #343E4E !important;
            color: #d1d5db !important;
            min-height: 100vh;
        }
        
        html {
            background-color: #343E4E !important;
        }
        
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        .gradient-header {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid #374151;
        }
        
        .gradient-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(31, 41, 55, 0.9) 0%, rgba(55, 65, 81, 0.9) 100%);
            z-index: 1;
        }
        
        .header-content {
            position: relative;
            z-index: 2;
        }
        
        .glass-card {
            background: rgba(31, 41, 55, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(75, 85, 99, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            color: #d1d5db;
        }
        
        .glass-card:hover {
            background: rgba(55, 65, 81, 0.98);
            border-color: rgba(251, 191, 36, 0.4);
            color: #f9fafb;
            transform: scale(1.05);
        }
        
        .filters-section {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border-bottom: 1px solid rgba(75, 85, 99, 0.5);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .property-card {
            background: #1f2937;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4), 0 5px 20px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(75, 85, 99, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            height: 100%; /* Equal height for all cards */
            display: flex;
            flex-direction: column;
        }
        
        .property-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 30px 100px rgba(0, 0, 0, 0.5), 0 10px 40px rgba(0, 0, 0, 0.3);
            border-color: #fbbf24;
        }
        
        .property-image {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .property-card:hover .property-image {
            transform: scale(1.08);
        }
        
        /* Equal height cards with proper content distribution */
        .property-card .p-8 {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        /* Enhanced shadows for better depth */
        .property-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.1) 0%, rgba(0, 0, 0, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 20px;
            pointer-events: none;
        }
        
        .property-card:hover::before {
            opacity: 1;
        }
        
        /* Ensure grid items are equal height */
        .grid > .block {
            display: flex;
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
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #1f2937;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
            transition: all 0.3s ease;
        }
        
        .price-tag:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(251, 191, 36, 0.4);
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
            background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
            color: #d1d5db;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #6b7280;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            background: linear-gradient(135deg, #4b5563 0%, #6b7280 100%);
            border-color: #fbbf24;
            color: #f9fafb;
        }
        
        .secondary-button {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            color: #9ca3af;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #6b7280;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        
        .secondary-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            background: linear-gradient(135deg, #4b5563 0%, #6b7280 100%);
            border-color: #fbbf24;
            color: #f9fafb;
        }
        
        /* Dark mode overrides for all elements */
        .bg-white { background-color: #1f2937 !important; }
        .bg-gray-50 { background-color: #111827 !important; }
        .bg-gray-100 { background-color: #1f2937 !important; }
        .bg-gray-200 { background-color: #374151 !important; }
        .bg-gray-300 { background-color: #4b5563 !important; }
        .bg-gray-400 { background-color: #6b7280 !important; }
        .bg-gray-500 { background-color: #9ca3af !important; }
        .bg-gray-600 { background-color: #d1d5db !important; }
        .bg-gray-700 { background-color: #f3f4f6 !important; }
        .bg-gray-800 { background-color: #f9fafb !important; }
        .bg-gray-900 { background-color: #ffffff !important; }
        
        .text-gray-900 { color: #f9fafb !important; }
        .text-gray-800 { color: #f3f4f6 !important; }
        .text-gray-700 { color: #d1d5db !important; }
        .text-gray-600 { color: #9ca3af !important; }
        .text-gray-500 { color: #6b7280 !important; }
        .text-gray-400 { color: #4b5563 !important; }
        .text-gray-300 { color: #374151 !important; }
        .text-gray-200 { color: #1f2937 !important; }
        .text-gray-100 { color: #111827 !important; }
        
        .border-gray-200 { border-color: #374151 !important; }
        .border-gray-300 { border-color: #4b5563 !important; }
        .border-gray-400 { border-color: #6b7280 !important; }
        .border-gray-500 { border-color: #9ca3af !important; }
        
        /* Form elements */
        input, select, textarea {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
        }
        
        input::placeholder, textarea::placeholder, select::placeholder {
            color: #9ca3af !important;
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: #fbbf24 !important;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1) !important;
        }
        
        /* Cards and containers */
        .card, .glass-card {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
            color: #d1d5db !important;
        }
        
        /* Buttons */
        .btn, .button {
            background: linear-gradient(135deg, #374151, #4b5563) !important;
            border: 1px solid #6b7280 !important;
            color: #d1d5db !important;
        }
        
        .btn:hover, .button:hover {
            background: linear-gradient(135deg, #4b5563, #6b7280) !important;
            border-color: #fbbf24 !important;
            color: #f9fafb !important;
        }
        
        /* Links */
        a {
            color: #d1d5db !important;
        }
        
        a:hover {
            color: #fbbf24 !important;
        }
        
        /* Headers */
        h1, h2, h3, h4, h5, h6 {
            color: #f9fafb !important;
        }
        
        /* Text elements */
        p, div, span {
            color: #d1d5db !important;
        }
        
        small {
            color: #9ca3af !important;
        }
        
        strong {
            color: #ffffff !important;
        }
        
        /* Navigation */
        nav {
            background-color: #1f2937 !important;
            border-bottom-color: #374151 !important;
        }
        
        /* Footer */
        footer {
            background-color: #343E4E !important;
            border-top-color: #374151 !important;
        }
        
        /* Ensure all backgrounds are dark */
        * {
            background-color: inherit;
        }
        
        .bg-white, .bg-gray-50, .bg-gray-100 {
            background-color: #1f2937 !important;
        }
        
        .bg-gray-200 {
            background-color: #374151 !important;
        }
        
        .bg-gray-300 {
            background-color: #4b5563 !important;
        }
        
        /* Main content areas */
        .container, .max-w-7xl, .max-w-6xl, .max-w-5xl, .max-w-4xl {
            background-color: #343E4E !important;
        }
        
        /* Sections and divs */
        section, div, main, article, aside {
            background-color: transparent !important;
        }
        
        /* Override any remaining light backgrounds */
        [class*="bg-"]:not([class*="bg-gray-9"]):not([class*="bg-gray-8"]):not([class*="bg-gray-7"]) {
            background-color: #1f2937 !important;
        }
        
        .search-button {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: #111827;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
            border: 2px solid #f59e0b;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .search-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(251, 191, 36, 0.4);
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-color: #d97706;
            color: #111827;
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
        
        /* Filter toggle button */
        .filter-toggle {
            display: block;
            width: 100%;
            padding: 16px 20px;
            background: linear-gradient(135deg, #374151, #4b5563);
            border: 2px solid #6b7280;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            color: #d1d5db;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .filter-toggle:hover {
            background: linear-gradient(135deg, #4b5563, #6b7280);
            border-color: #fbbf24;
            color: #f9fafb;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        .filter-toggle:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .filters-content {
            display: none;
            opacity: 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .filters-content.show {
            display: block;
            opacity: 1;
            max-height: 2000px;
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
                <!-- Filter toggle button -->
                <button type="button" class="filter-toggle w-full mb-4" onclick="toggleFilters()">
                    <i class="fas fa-filter mr-2"></i>
                    <span id="filterToggleText">Show Filters</span>
                    <i class="fas fa-chevron-down ml-2" id="filterToggleIcon"></i>
                </button>
                
                <div class="filters-content" id="filtersContent">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 space-y-4 sm:space-y-0">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 flex items-center space-x-2 sm:space-x-3">
                            <div class="bg-blue-100 p-2 sm:p-3 rounded-full">
                                <i class="fas fa-filter text-blue-600 text-lg sm:text-xl"></i>
                            </div>
                            <span>Search Filters</span>
                        </h2>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
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
                                <label class="filter-label text-sm sm:text-base">Agent Name</label>
                                <select name="agent_name" class="filter-input w-full text-sm sm:text-base">
                                    <option value="">All Agents</option>
                                    @foreach($agentNames as $agent)
                                        <option value="{{ $agent }}" {{ request('agent_name') == $agent ? 'selected' : '' }}>
                                            {{ $agent }}@if(isset($agentsWithPaying[$agent]) && $agentsWithPaying[$agent]) ⚡@endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endauth
                            
                        </div>
                        
                        <!-- Second row of filters -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
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
                            
                            <div>
                                <label class="filter-label text-sm sm:text-base">Couples Allowed</label>
                                <select name="couples_allowed" class="filter-input w-full text-sm sm:text-base">
                                    <option value="">All Properties</option>
                                    <option value="yes" {{ request('couples_allowed') == 'yes' ? 'selected' : '' }}>Couples Welcome</option>
                                    <option value="no" {{ request('couples_allowed') == 'no' ? 'selected' : '' }}>Singles Only</option>
                                </select>
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
            @if(request('location') || request('property_type') || request('min_price') || request('max_price') || request('available_date') || request('agent_name') || request('couples_allowed'))
                <div class="mb-6 sm:mb-8 p-4 sm:p-6 bg-blue-50 border border-blue-200 rounded-16 sm:rounded-20 animate-fade-in">
                    <h3 class="text-lg sm:text-xl font-bold text-blue-800 mb-3 sm:mb-4 flex items-center space-x-2">
                        <i class="fas fa-filter text-blue-600"></i>
                        <span>Active Filters:</span>
                    </h3>
                    <div class="flex flex-wrap gap-2 sm:gap-3">
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
                        @if(request('agent_name'))
                            <span class="filter-badge text-xs sm:text-sm">
                                <i class="fas fa-user-tie"></i>Agent: {{ request('agent_name') }}
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
                        @if(request('couples_allowed'))
                            <span class="filter-badge text-xs sm:text-sm">
                                <i class="fas fa-heart"></i>Couples: {{ request('couples_allowed') == 'yes' ? 'Welcome' : 'Singles Only' }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Enhanced Results Summary -->
            <div class="results-summary mb-6 sm:mb-8 animate-fade-in" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); border: 2px solid #fbbf24; border-radius: 16px; padding: 24px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-2 sm:space-y-0">
                    <div style="color: #fbbf24; font-size: 14px;">
                        <span style="font-weight: bold; font-size: 20px; color: #ffffff; text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);">{{ $properties->total() }}</span> properties found
                        @if(request('location') || request('property_type') || request('min_price') || request('max_price') || request('available_date'))
                            <span style="color: #fbbf24; margin-left: 8px;">(filtered)</span>
                        @endif
                    </div>
                    <div style="font-size: 12px; color: #1a1a1a; background: linear-gradient(135deg, #fbbf24, #f59e0b); padding: 8px 16px; border-radius: 20px; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4); border: 2px solid #f59e0b;">
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
                                    @if(($property->high_quality_photos_array && count($property->high_quality_photos_array) > 0) || $property->photo_count > 0)
                                        <div class="photo-badge absolute top-2 sm:top-4 right-2 sm:right-4">
                                            <i class="fas fa-camera mr-1 sm:mr-2"></i>
                                            @if($property->high_quality_photos_array && count($property->high_quality_photos_array) > 0)
                                                {{ count($property->high_quality_photos_array) }}
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
            const filterSelects = document.querySelectorAll('select[name="location"], select[name="property_type"], select[name="agent_name"], select[name="couples_allowed"]');
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
            
            sessionStorage.setItem('propertyFilters', JSON.stringify(filters));
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
