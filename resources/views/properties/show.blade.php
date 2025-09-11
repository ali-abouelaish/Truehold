<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>{{ $property->title ?: 'Property Details' }} - LET CONNECT</title>
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
        
        .property-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(229, 231, 235, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        
        .property-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
        }
        
        .feature-badge {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }
        
        .feature-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }
        
        .status-badge {
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .status-available {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        
        .status-rented {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }
        
        .status-unavailable {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }
        
        .status-on_hold {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        
        .price-display {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(59, 130, 246, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .price-display::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        
        .action-button {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #1f2937;
            padding: 1rem 2rem;
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
            padding: 1rem 2rem;
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
        
        .success-button {
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
            text-decoration: none;
        }
        
        .success-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            border-color: #86efac;
            color: #064e3b;
        }
        
        .image-gallery {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .main-image-container {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }
        
        .gallery-nav-button {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .gallery-nav-button:hover {
            background: rgba(0, 0, 0, 0.9);
            transform: scale(1.1);
        }
        
        .thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 0.75rem;
            margin-top: 1.5rem;
        }
        
        .thumbnail {
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid transparent;
        }
        
        .thumbnail:hover {
            transform: scale(1.05);
            border-color: #3b82f6;
        }
        
        .thumbnail.active {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }
        
        .info-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .info-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .info-value {
            color: #6b7280;
            line-height: 1.6;
        }
        
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
            margin: 2rem 0;
        }
        
        .floating-action {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
        }
        
        .modal-overlay {
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(20px);
        }
        
        .modal-content {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .description-text {
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.8;
            color: #374151;
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
        
        .location-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
        }
        
        .maps-button {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #1e3a8a;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15);
            text-decoration: none;
            display: block;
            text-align: center;
            border: 1px solid #bfdbfe;
        }
        
        .maps-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.2);
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-color: #93c5fd;
            color: #1e40af;
        }
        
        .responsive-grid {
            display: grid;
            gap: 2rem;
        }
        
        @media (min-width: 1024px) {
            .responsive-grid {
                grid-template-columns: 2fr 1fr;
            }
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
        
        /* Mobile-first responsive design */
        @media (max-width: 767px) {
            .property-card {
                border-radius: 16px;
                margin-bottom: 1rem;
            }
            
            .property-card .p-8 {
                padding: 1.5rem;
            }
            
            .price-display {
                padding: 1.5rem;
                border-radius: 16px;
            }
            
            .price-display .text-6xl {
                font-size: 2.5rem;
            }
            
            .feature-badge, .status-badge {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
            
            .action-button, .secondary-button, .success-button {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
                min-height: 44px;
                width: 100%;
                justify-content: center;
            }
            
            .grid.grid-cols-1.lg\\:grid-cols-3 {
                gap: 1.5rem;
            }
            
            .space-y-6 > * + * {
                margin-top: 1.5rem;
            }
        }
        
        /* Small screens */
        @media (max-width: 640px) {
            .property-card .p-8 {
                padding: 1rem;
            }
            
            .price-display {
                padding: 1rem;
            }
            
            .price-display .text-6xl {
                font-size: 2rem;
            }
            
            .feature-badge, .status-badge {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }
            
            .text-2xl {
                font-size: 1.25rem;
            }
            
            .text-3xl {
                font-size: 1.5rem;
            }
        }
        
        /* Very small screens */
        @media (max-width: 375px) {
            .property-card .p-8 {
                padding: 0.75rem;
            }
            
            .price-display {
                padding: 0.75rem;
            }
            
            .price-display .text-6xl {
                font-size: 1.75rem;
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
            
            .grid.gap-8 {
                gap: 1.5rem;
            }
        }
        
        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .property-image {
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    @include('layouts.properties-navigation')
    <div class="min-h-screen">


        <!-- Property Header Section -->
        <div class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-3 sm:space-y-0">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <a href="{{ route('properties.index') }}" 
                           class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                            <i class="fas fa-arrow-left text-lg sm:text-xl"></i>
                        </a>
                        <div class="hidden sm:block h-8 w-px bg-gray-300"></div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">
                            {{ Str::limit($property->title ?: 'Property Details', 50) }}
                        </h1>
                    </div>
                    @if($property->status)
                        <div class="status-badge status-{{ $property->status === 'available' ? 'available' : 'rented' }} text-sm sm:text-base">
                            <i class="fas fa-{{ $property->status === 'available' ? 'check-circle' : 'clock' }} mr-2"></i>
                            {{ ucfirst($property->status) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            @if(session('success'))
                <div class="success-message animate-fade-in">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-xl sm:text-2xl"></i>
                        <span class="text-base sm:text-lg font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            
            <!-- Property Overview Card -->
            <div class="property-card p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8 animate-slide-up">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                    <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <i class="fas fa-map-marker-alt text-red-500 text-xl sm:text-2xl"></i>
                            @if($property->latitude && $property->longitude && $property->latitude !== 'N/A' && $property->longitude !== 'N/A')
                                <a href="https://www.google.com/maps?q={{ $property->latitude }},{{ $property->longitude }}" 
                                   target="_blank" 
                                   class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300 hover:underline">
                                    {{ $property->location ?: 'Location not specified' }}
                                    <i class="fas fa-external-link-alt ml-2 sm:ml-3 text-base sm:text-lg opacity-75"></i>
                                </a>
                            @else
                                <span class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-700">{{ $property->location ?: 'Location not specified' }}</span>
                            @endif
                        </div>
                        
                        @if($property->property_type)
                            <div class="feature-badge inline-block text-sm sm:text-base">
                                <i class="fas fa-home mr-2"></i>
                                {{ $property->property_type }}
                            </div>
                        @endif
                        
                        @auth
                        <div class="pt-4">
                            <a href="{{ route('admin.properties.edit', $property) }}" class="action-button w-full sm:w-auto">
                                <i class="fas fa-edit"></i>
                                Edit Property
                            </a>
                        </div>
                        @endauth
                    </div>
                    
                    <div class="space-y-4 sm:space-y-6">
                        <div class="price-display">
                            <div class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-2 leading-none">
                                {{ $property->formatted_price }}
                            </div>
                        </div>
                        
                        @if($property->available_date && $property->available_date !== 'N/A')
                            <div class="bg-green-100 text-green-800 px-4 sm:px-6 py-3 sm:py-4 rounded-12 sm:rounded-16 font-semibold text-center shadow-lg text-sm sm:text-base">
                                <i class="fas fa-calendar mr-2"></i>
                                Available: {{ $property->available_date }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            <div class="responsive-grid">
                <!-- Main Content -->
                <div class="space-y-8">
                    <!-- Enhanced Image Gallery Carousel -->
                    @if($property->high_quality_photos_array && count($property->high_quality_photos_array) > 0)
                        <div class="image-gallery" data-photos="{{ json_encode($property->high_quality_photos_array ?? []) }}" data-original-photos="{{ json_encode($property->all_photos_array ?? []) }}">
                            <div class="flex items-center justify-between mb-8">
                                <h2 class="text-3xl font-bold text-gray-900 flex items-center space-x-3">
                                    <div class="bg-blue-100 p-3 rounded-full">
                                        <i class="fas fa-images text-blue-600 text-2xl"></i>
                                    </div>
                                    <span>Property Gallery</span>
                                </h2>
                                <div class="bg-white px-4 py-2 rounded-full shadow-lg">
                                    <span class="text-gray-700 font-semibold">
                                        <span id="currentImage">1</span> of {{ count($property->high_quality_photos_array) }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Enhanced Main Image Display -->
                            <div class="main-image-container mb-6">
                                <div class="aspect-w-16 aspect-h-9">
                                    <img id="mainImage" src="{{ $property->high_quality_photos_array[0] }}" 
                                         alt="Property photo" 
                                         class="w-full h-[500px] object-cover"
                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDQwMCA0MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMDAgMTUwQzE3NS4xNDcgMTUwIDE1NSAxNzAuMTQ3IDE1NSAxOTVDMTU1IDIxOS44NTMgMTc1LjE0NyAyNDAgMjAwIDI0MEMyMjQuODUzIDI0MCAyNDUgMjE5Ljg1MyAyNDUgMTk1QzI0NSAxNzAuMTQ3IDIyNC44NTMgMTUwIDIwMCAxNTBaIiBmaWxsPSIjOUI5QkEwIi8+CjxwYXRoIGQ9Ik0yMDAgMjgwQzE2NS40OSAyODAgMTM1IDI5MC40OSAxMzUgMzA1VjM1MEgyNjVWMzA1QzI2NSAyOTAuNDkgMjM0LjUxIDI4MCAyMDAgMjgwWiIgZmlsbD0iIzlCOUJBMCIvPgo8L3N2Zz4K'">
                                </div>
                                
                                <!-- Enhanced Navigation Arrows -->
                                <button onclick="previousImage()" class="gallery-nav-button absolute left-6 top-1/2 transform -translate-y-1/2">
                                    <i class="fas fa-chevron-left text-xl"></i>
                                </button>
                                <button onclick="nextImage()" class="gallery-nav-button absolute right-6 top-1/2 transform -translate-y-1/2">
                                    <i class="fas fa-chevron-right text-xl"></i>
                                </button>
                                
                                <!-- Enhanced Fullscreen Button -->
                                <button onclick="openFullscreen()" class="gallery-nav-button absolute top-6 right-6">
                                    <i class="fas fa-expand text-xl"></i>
                                </button>
                            </div>
                            
                            <!-- Enhanced Thumbnail Navigation -->
                            <div class="thumbnail-grid">
                                @foreach($property->all_photos_array as $index => $photoUrl)
                                    <div class="thumbnail {{ $index === 0 ? 'active' : '' }}" onclick="showImage({{ $index }})">
                                        <img src="{{ $photoUrl }}" 
                                             alt="Thumbnail {{ $index + 1 }}" 
                                             class="w-full h-20 object-cover"
                                             id="thumb{{ $index }}"
                                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik00MCAzMEMzNS4wMjkgMzAgMzEgMzQuMDI5IDMxIDM5QzMxIDQzLjk3MSAzNS4wMjkgNDggNDAgNDhDNDQuOTcxIDQ4IDQ5IDQzLjk3MSA0OSAzOUM0OSAzNC4wMjkgNDQuOTcxIDMwIDQwIDMwWiIgZmlsbD0iIzlCOUJBMCIvPgo8cGF0aCBkPSJNNDAgNTZDMzIuOTggNTYgMjcgNjAuOTggMjcgNjZWNzBINjNWNjZDNjMgNjAuOTggNTcuMDIgNTYgNDAgNTZaIiBmaWxsPSIjOUI5QkEwIi8+Cjwvc3ZnPgo='">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Enhanced Property Description -->
                    @if($property->description && $property->description !== 'N/A')
                        <div class="property-card p-10">
                            <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                                <div class="bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
                                </div>
                                <span>Description</span>
                            </h2>
                            <div class="prose max-w-none">
                                <div class="description-text text-lg">
                                    {!! nl2br(e($property->description)) !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Enhanced Detailed Property Information -->
                    <div class="property-card p-10">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center space-x-3">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-list-ul text-blue-600 text-2xl"></i>
                            </div>
                            <span>Property Details</span>
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Basic Details -->
                            <div class="space-y-6">
                                <h3 class="text-xl font-bold text-gray-900 border-b-2 border-blue-200 pb-3">Basic Information</h3>
                                
                                @if($property->amenities && $property->amenities !== 'N/A')
                                    <div class="info-section">
                                        <span class="info-label">Amenities</span>
                                        <p class="info-value">{{ $property->amenities }}</p>
                                    </div>
                                @endif
                                
                                @if($property->bills_included && $property->bills_included !== 'N/A')
                                    <div class="info-section">
                                        <span class="info-label">Bills Included</span>
                                        <p class="info-value">{{ $property->bills_included }}</p>
                                    </div>
                                @endif
                                
                                @if($property->deposit && $property->deposit !== 'N/A')
                                    <div class="info-section">
                                        <span class="info-label">Deposit</span>
                                        <p class="info-value">{{ $property->deposit }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Additional Details -->
                            <div class="space-y-6">
                                <h3 class="text-xl font-bold text-gray-900 border-b-2 border-blue-200 pb-3">Additional Features</h3>
                                
                                @if($property->minimum_term && $property->minimum_term !== 'N/A')
                                    <div class="info-section">
                                        <span class="info-label">Minimum Term</span>
                                        <p class="info-value">{{ $property->minimum_term }}</p>
                                    </div>
                                @endif
                                
                                @if($property->furnishings && $property->furnishings !== 'N/A')
                                    <div class="info-section">
                                        <span class="info-label">Furnishings</span>
                                        <p class="info-value">{{ $property->furnishings }}</p>
                                    </div>
                                @endif
                                
                                @if($property->garden_patio && $property->garden_patio !== 'N/A')
                                    <div class="info-section">
                                        <span class="info-label">Garden/Patio</span>
                                        <p class="info-value">{{ $property->garden_patio }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Sidebar -->
                <div class="space-y-8">
                    <!-- Contact Information -->
                    @if($property->contact_info && $property->contact_info !== 'N/A')
                        <div class="property-card p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                                <div class="bg-green-100 p-3 rounded-full">
                                    <i class="fas fa-phone text-green-600 text-xl"></i>
                                </div>
                                <span>Contact Information</span>
                            </h2>
                            <div class="bg-green-50 p-4 rounded-12 border border-green-200">
                                <p class="text-gray-700 font-medium">{{ $property->contact_info }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Management Company -->
                    @if($property->management_company && $property->management_company !== 'N/A' && auth()->check())
                        <div class="property-card p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                                <div class="bg-purple-100 p-3 rounded-full">
                                    <i class="fas fa-building text-purple-600 text-xl"></i>
                                </div>
                                <span>Management Company</span>
                            </h2>
                            <div class="bg-purple-50 p-4 rounded-12 border border-purple-200">
                                <p class="text-gray-700 font-medium">{{ $property->management_company }}</p>
                            </div>
                        </div>
                    @elseif($property->management_company && $property->management_company !== 'N/A')
                        <div class="property-card p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                                <div class="bg-purple-100 p-3 rounded-full">
                                    <i class="fas fa-building text-purple-600 text-xl"></i>
                                </div>
                                <span>Management Company</span>
                            </h2>
                            <div class="bg-gray-100 border border-gray-300 text-gray-600 px-6 py-4 rounded-lg text-center">
                                <i class="fas fa-lock text-2xl mb-3"></i>
                                <p class="mb-2">Management company information is available to registered users.</p>
                                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-800">Login to view company details</a>
                            </div>
                        </div>
                    @endif

                    <!-- Enhanced Location Information -->
                    @if($property->latitude && $property->longitude && $property->latitude !== 'N/A' && $property->longitude !== 'N/A')
                        <div class="property-card p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                                <div class="bg-orange-100 p-3 rounded-full">
                                    <i class="fas fa-map-marker-alt text-orange-600 text-xl"></i>
                                </div>
                                <span>Location</span>
                            </h2>
                            <div class="location-card mb-6">
                                <i class="fas fa-map-marker-alt text-5xl text-orange-500 mb-4"></i>
                                <p class="text-gray-700 font-semibold mb-2">Coordinates:</p>
                                <p class="text-lg text-gray-600 font-mono">{{ $property->latitude }}, {{ $property->longitude }}</p>
                            </div>
                            <a href="https://www.google.com/maps?q={{ $property->latitude }},{{ $property->longitude }}" 
                               target="_blank" 
                               class="maps-button">
                                <i class="fas fa-map-marked-alt mr-2"></i>Open in Google Maps
                            </a>
                        </div>
                    @endif

                    <!-- Mark Client as Interested -->
                    @auth
                    <div class="property-card p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <span>Mark Client as Interested</span>
                        </h2>
                        <form method="POST" action="{{ route('admin.properties.interests.add', $property) }}" class="grid grid-cols-1 gap-4">
                            @csrf
                            <div>
                                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Select Client (A→Z)</label>
                                <select id="client_id" name="client_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Choose a client</option>
                                    @foreach($clients ?? [] as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                                <textarea id="notes" name="notes" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Availability, preferences, etc."></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                                    <i class="fas fa-user-plus mr-2"></i>Add Interested Client
                                </button>
                            </div>
                        </form>
                    </div>
                    @endauth

                    <!-- Interested Clients -->
                    <div class="property-card p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                            <div class="bg-indigo-100 p-3 rounded-full">
                                <i class="fas fa-user-friends text-indigo-600 text-xl"></i>
                            </div>
                            <span>Interested Clients</span>
                        </h2>
                        <ul class="divide-y divide-gray-200">
                            @forelse($property->interestedClients ?? [] as $client)
                                <li class="py-3 flex items-start justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $client->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $client->email }} @if($client->phone) • {{ $client->phone }} @endif</p>
                                        @if($client->pivot && $client->pivot->created_at)
                                            <p class="text-xs text-gray-400 mt-1">Added {{ $client->pivot->created_at->diffForHumans() }}</p>
                                        @endif
                                    </div>
                                    @auth
                                    <form method="POST" action="{{ route('admin.properties.interests.remove', [$property, $client]) }}" onsubmit="return confirm('Remove this client from interested list?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-700">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </form>
                                    @endauth
                                </li>
                            @empty
                                <li class="py-4 text-center text-gray-500">
                                    <i class="fas fa-users text-2xl mb-2"></i>
                                    <p>No interested clients yet</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Enhanced Quick Actions -->
                    <div class="property-card p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                            <div class="bg-indigo-100 p-3 rounded-full">
                                <i class="fas fa-bolt text-indigo-600 text-xl"></i>
                            </div>
                            <span>Quick Actions</span>
                        </h2>
                        <div class="space-y-4">
                            @auth
                            <a href="{{ route('admin.properties.edit', $property) }}" 
                               class="action-button w-full justify-center">
                                <i class="fas fa-edit"></i>
                                Edit Property
                            </a>
                            @endauth
                            
                            <button onclick="shareProperty()" 
                                    class="success-button w-full justify-center">
                                <i class="fas fa-share"></i>
                                Share Property
                            </button>
                            
                            @if($property->url && auth()->check())
                            <a href="{{ $property->url }}" target="_blank" 
                               class="secondary-button w-full justify-center">
                                <i class="fas fa-external-link-alt"></i>
                                View Original Listing
                            </a>
                            @elseif($property->url)
                            <div class="bg-gray-100 border border-gray-300 text-gray-600 px-4 py-3 rounded-lg text-center">
                                <i class="fas fa-lock mr-2"></i>
                                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-800">Login</a> to view original listing
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Enhanced Fullscreen Image Modal -->
    <div id="fullscreenModal" class="modal-overlay fixed inset-0 hidden z-50 flex items-center justify-center p-4">
        <div class="modal-content relative max-w-7xl max-h-full">
            <img id="fullscreenImage" src="" alt="Property photo" class="max-w-full max-h-full object-contain rounded-20">
            <button onclick="closeFullscreen()" class="absolute top-6 right-6 text-white text-4xl hover:text-gray-300 transition-colors duration-300 bg-black bg-opacity-50 p-3 rounded-full">
                <i class="fas fa-times"></i>
            </button>
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 text-white text-xl bg-black bg-opacity-50 px-6 py-3 rounded-full">
                <span id="fullscreenCounter">1</span> of {{ count($property->all_photos_array ?? []) }}
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="floating-action">
        <button onclick="scrollToTop()" class="glass-card p-4 rounded-full hover:scale-110 transition-transform duration-300 shadow-2xl">
            <i class="fas fa-arrow-up text-white text-xl"></i>
        </button>
    </div>

    <script>
        // Global photos data from PHP
        let propertyPhotos = []; // High quality photos for main image
        let originalPhotos = []; // Original photos for thumbnails
        
        try {
            const photosElement = document.querySelector('[data-photos]');
            if (photosElement && photosElement.dataset.photos) {
                propertyPhotos = JSON.parse(photosElement.dataset.photos);
            }
            if (photosElement && photosElement.dataset.originalPhotos) {
                originalPhotos = JSON.parse(photosElement.dataset.originalPhotos);
            }
        } catch (error) {
            console.error('Error parsing photos data:', error);
            propertyPhotos = [];
            originalPhotos = [];
        }
        
        let currentImageIndex = 0;
        const totalImages = propertyPhotos.length;
        
        function showImage(index) {
            currentImageIndex = index;
            const mainImage = document.getElementById('mainImage');
            const currentImageSpan = document.getElementById('currentImage');
            
            if (mainImage && currentImageSpan) {
                if (propertyPhotos && propertyPhotos.length > 0 && propertyPhotos[index]) {
                    mainImage.src = propertyPhotos[index];
                    currentImageSpan.textContent = index + 1;
                
                    // Update thumbnail selection
                    document.querySelectorAll('.thumbnail').forEach((thumb, i) => {
                        if (i === index) {
                            thumb.classList.add('active');
                        } else {
                            thumb.classList.remove('active');
                        }
                    });
                }
            }
        }

        function nextImage() {
            currentImageIndex = (currentImageIndex + 1) % totalImages;
            showImage(currentImageIndex);
        }

        function previousImage() {
            currentImageIndex = (currentImageIndex - 1 + totalImages) % totalImages;
            showImage(currentImageIndex);
        }

        function openFullscreen() {
            const fullscreenModal = document.getElementById('fullscreenModal');
            const fullscreenImage = document.getElementById('fullscreenImage');
            const fullscreenCounter = document.getElementById('fullscreenCounter');
            
            if (fullscreenModal && fullscreenImage && fullscreenCounter) {
                fullscreenImage.src = document.getElementById('mainImage').src;
                fullscreenCounter.textContent = currentImageIndex + 1;
                fullscreenModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeFullscreen() {
            const fullscreenModal = document.getElementById('fullscreenModal');
            if (fullscreenModal) {
                fullscreenModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        function shareProperty() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $property->title }}',
                    text: 'Check out this property: {{ $property->title }}',
                    url: window.location.href
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    // Create a temporary success message
                    const successMsg = document.createElement('div');
                    successMsg.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
                    successMsg.innerHTML = '<i class="fas fa-check mr-2"></i>Property link copied to clipboard!';
                    document.body.appendChild(successMsg);
                    
                    setTimeout(() => {
                        successMsg.remove();
                    }, 3000);
                });
            }
        }

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Keyboard navigation for fullscreen
        document.addEventListener('keydown', function(e) {
            if (document.getElementById('fullscreenModal').classList.contains('hidden')) return;
            
            if (e.key === 'Escape') {
                closeFullscreen();
            } else if (e.key === 'ArrowLeft') {
                previousImage();
                if (document.getElementById('fullscreenImage')) {
                    document.getElementById('fullscreenImage').src = document.getElementById('mainImage').src;
                    document.getElementById('fullscreenCounter').textContent = currentImageIndex + 1;
                }
            } else if (e.key === 'ArrowRight') {
                nextImage();
                if (document.getElementById('fullscreenImage')) {
                    document.getElementById('fullscreenImage').src = document.getElementById('mainImage').src;
                    document.getElementById('fullscreenCounter').textContent = currentImageIndex + 1;
                }
            }
        });

        // Close fullscreen when clicking outside
        document.getElementById('fullscreenModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFullscreen();
            }
        });

        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';

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
