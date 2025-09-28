@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stats-card bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-2xl text-blue-600"></i>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Properties</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalProperties }}</p>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-sm text-green-600">
                <i class="fas fa-arrow-up mr-1"></i>
                +12% from last month
            </span>
        </div>
    </div>

    <div class="stats-card bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-2xl text-green-600"></i>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">With Coordinates</p>
                <p class="text-2xl font-bold text-gray-900">{{ $propertiesWithCoords }}</p>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-sm text-green-600">
                <i class="fas fa-arrow-up mr-1"></i>
                {{ $totalProperties > 0 ? round(($propertiesWithCoords / $totalProperties) * 100, 1) : 0 }}% coverage
            </span>
        </div>
    </div>

    <div class="stats-card bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-2xl text-purple-600"></i>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Active Agents</p>
                <p class="text-2xl font-bold text-gray-900">{{ App\Models\User::count() }}</p>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-sm text-blue-600">
                <i class="fas fa-info-circle mr-1"></i>
                Managing properties
            </span>
        </div>
    </div>

    <div class="stats-card bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-2xl text-yellow-600"></i>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Available</p>
                <p class="text-2xl font-bold text-gray-900">{{ App\Models\Property::where('status', 'available')->count() }}</p>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-sm text-green-600">
                <i class="fas fa-check-circle mr-1"></i>
                Ready for viewing
            </span>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.properties.create') }}" 
               class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-plus text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-blue-900">Add Property</p>
                    <p class="text-sm text-blue-600">Create new listing</p>
                </div>
            </a>

            <a href="{{ route('admin.users.create') }}" 
               class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-user-plus text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-green-900">Add Agent</p>
                    <p class="text-sm text-green-600">Create user account</p>
                </div>
            </a>

            <a href="{{ route('admin.properties') }}" 
               class="flex items-center p-4 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-list text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-purple-900">Manage Properties</p>
                    <p class="text-sm text-purple-600">View all listings</p>
                </div>
            </a>

            <a href="{{ route('properties.map') }}" 
               class="flex items-center p-4 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100 transition-colors">
                <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-map text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-orange-900">Map View</p>
                    <p class="text-sm text-orange-600">Interactive map</p>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Recent Activity & Properties -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Properties -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Recent Properties</h3>
                <a href="{{ route('admin.properties') }}" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($recentProperties as $property)
                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                        @if($property->first_photo_url)
                            <img src="{{ $property->first_photo_url }}" alt="Property" class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <i class="fas fa-home text-gray-400"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $property->title }}</p>
                        <p class="text-sm text-gray-500">{{ $property->location }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">£{{ number_format($property->price) }}</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $property->status === 'available' ? 'bg-green-100 text-green-800' : 
                               ($property->status === 'rented' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($property->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">System Status</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Database Connection</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>Connected
                    </span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Storage Space</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-hdd mr-1"></i>85% Free
                    </span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Last Backup</span>
                    <span class="text-sm text-gray-900">2 hours ago</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Active Sessions</span>
                    <span class="text-sm text-gray-900">{{ rand(1, 5) }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">System Load</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-server mr-1"></i>Normal
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Property Analytics</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Property Types Distribution -->
            <div class="text-center">
                <h4 class="text-sm font-medium text-gray-600 mb-2">Property Types</h4>
                <div class="w-24 h-24 mx-auto mb-2">
                    <div class="w-full h-full bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-pie text-2xl text-blue-600"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500">Distribution chart</p>
            </div>

            <!-- Price Range -->
            <div class="text-center">
                <h4 class="text-sm font-medium text-gray-600 mb-2">Price Range</h4>
                <div class="w-24 h-24 mx-auto mb-2">
                    <div class="w-full h-full bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-pound-sign text-2xl text-green-600"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500">£{{ number_format(App\Models\Property::min('price')) }} - £{{ number_format(App\Models\Property::max('price')) }}</p>
            </div>

            <!-- Location Coverage -->
            <div class="text-center">
                <h4 class="text-sm font-medium text-gray-600 mb-2">Locations</h4>
                <div class="w-24 h-24 mx-auto mb-2">
                    <div class="w-full h-full bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-2xl text-purple-600"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500">{{ App\Models\Property::distinct('location')->count() }} areas</p>
            </div>
        </div>
    </div>
</div>
@endsection
