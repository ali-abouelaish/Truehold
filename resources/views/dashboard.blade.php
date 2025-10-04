<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard - Property Scraper</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/truehold-logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/truehold-logo.jpg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border-bottom: 2px solid #4b5563;
        }
        
        .dashboard-card {
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
        <header class="dashboard-header shadow-lg">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <i class="fas fa-building text-3xl text-blue-400 mr-4"></i>
                        <h1 class="text-3xl font-bold text-white">Agent Dashboard</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-300">
                            Welcome, {{ Auth::user()->name }}
                        </span>
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
                    <a href="{{ route('dashboard') }}" class="nav-link active block">
                        <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                    </a>
                    <a href="{{ route('properties.index') }}" class="nav-link block">
                        <i class="fas fa-home mr-3"></i>All Properties
                    </a>
                    <a href="{{ route('properties.map') }}" class="nav-link block">
                        <i class="fas fa-map-marked-alt mr-3"></i>Property Map
                    </a>
                    <a href="{{ route('properties.manage') }}" class="nav-link block">
                        <i class="fas fa-edit mr-3"></i>Manage Properties
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-8">
                <!-- Dashboard Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="dashboard-card p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-home text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Properties</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalProperties }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-card p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Available</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $availableProperties }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-card p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-key text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Rented</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $rentedProperties }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-card p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <i class="fas fa-times-circle text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Unavailable</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $unavailableProperties }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Properties -->
                <div class="dashboard-card p-6 mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-clock mr-3 text-blue-600"></i>Recent Properties
                    </h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentProperties as $property)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $property->title }}</div>
                                        <div class="text-sm text-gray-500">{{ $property->property_type }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $property->location }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $property->formatted_price }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full status-{{ strtolower($property->status ?? 'available') }}">
                                            {{ ucfirst($property->status ?? 'Available') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('properties.show', $property) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('properties.edit', $property) }}" class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="dashboard-card p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-bolt mr-3 text-blue-600"></i>Quick Actions
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('properties.create') }}" class="block w-full bg-blue-600 text-white text-center py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>Add New Property
                            </a>
                            <a href="{{ route('properties.manage') }}" class="block w-full bg-green-600 text-white text-center py-3 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-edit mr-2"></i>Manage Properties
                            </a>
                            <a href="{{ route('properties.map') }}" class="block w-full bg-purple-600 text-white text-center py-3 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-map mr-2"></i>View Map
                            </a>
                        </div>
                    </div>
                    
                    <div class="dashboard-card p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-chart-bar mr-3 text-blue-600"></i>Status Overview
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Available</span>
                                <span class="text-sm font-semibold text-green-600">{{ $availableProperties }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Rented</span>
                                <span class="text-sm font-semibold text-yellow-600">{{ $rentedProperties }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Unavailable</span>
                                <span class="text-sm font-semibold text-red-600">{{ $unavailableProperties }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">On Hold</span>
                                <span class="text-sm font-semibold text-blue-600">{{ $onHoldProperties }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
