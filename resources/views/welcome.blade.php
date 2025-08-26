<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Scraper - Find Your Perfect Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .hero-header {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border-bottom: 2px solid #4b5563;
        }
        
        .hero-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        
        .hero-button {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border: 1px solid #4b5563;
            transition: all 0.2s ease;
        }
        
        .hero-button:hover {
            background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
            </style>
    </head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="hero-header shadow-lg">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <i class="fas fa-building text-3xl text-blue-400 mr-4"></i>
                        <h1 class="text-3xl font-bold text-white">Property Scraper</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.dashboard') }}" class="text-blue-400 hover:text-white transition-colors">
                                <i class="fas fa-cog mr-2"></i>Admin Dashboard
                            </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 py-16">
            <!-- Hero Section -->
            <div class="text-center mb-16">
                <h1 class="text-5xl font-bold text-gray-900 mb-6">
                    Find Your Perfect Home
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Discover amazing properties across the UK with our comprehensive property search platform. 
                    Browse listings, view maps, and find your ideal home.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('properties.index') }}" class="hero-button text-white px-8 py-4 rounded-lg text-lg font-medium">
                        <i class="fas fa-search mr-2"></i>Browse Properties
                    </a>
                    <a href="{{ route('properties.map') }}" class="bg-blue-600 text-white px-8 py-4 rounded-lg text-lg font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-map-marked-alt mr-2"></i>View Map
                    </a>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                <div class="hero-card p-8 text-center">
                    <i class="fas fa-home text-4xl text-blue-600 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Extensive Listings</h3>
                    <p class="text-gray-600">Browse through thousands of carefully curated property listings across the UK.</p>
                </div>
                
                <div class="hero-card p-8 text-center">
                    <i class="fas fa-map-marked-alt text-4xl text-blue-600 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Interactive Maps</h3>
                    <p class="text-gray-600">Explore properties on an interactive map to find the perfect location.</p>
                </div>
                
                <div class="hero-card p-8 text-center">
                    <i class="fas fa-user-shield text-4xl text-blue-600 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Agent Portal</h3>
                    <p class="text-gray-600">Professional agents can manage properties and update availability status.</p>
                </div>
        </div>

            <!-- Quick Stats -->
            <div class="hero-card p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Property Statistics</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <div class="text-3xl font-bold text-blue-600 mb-2">45+</div>
                        <div class="text-gray-600">Properties Available</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-green-600 mb-2">Multiple</div>
                        <div class="text-gray-600">Locations</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-purple-600 mb-2">24/7</div>
                        <div class="text-gray-600">Access</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-orange-600 mb-2">Real-time</div>
                        <div class="text-gray-600">Updates</div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-8 mt-16">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <p>&copy; 2025 Property Scraper. All rights reserved.</p>
            </div>
        </footer>
    </div>
    </body>
</html>
