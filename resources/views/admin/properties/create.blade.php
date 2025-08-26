@extends('layouts.admin')

@section('page-title', 'Create New Property')

@section('content')
<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create New Property</h2>
            <p class="text-gray-600">Add a new property listing to your portfolio</p>
        </div>
        <a href="{{ route('admin.properties') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Properties
        </a>
    </div>
</div>

<!-- Property Creation Form -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Property Information</h3>
    </div>
    
    <form method="POST" action="{{ route('admin.properties.store') }}" class="p-6 space-y-6" enctype="multipart/form-data">
        @csrf
        
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Property Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" 
                       value="{{ old('title') }}" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., Modern 2-Bedroom Apartment in City Center">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="property_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Property Type
                </label>
                <select name="property_type" id="property_type" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Property Type</option>
                    <option value="apartment" {{ old('property_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                    <option value="house" {{ old('property_type') == 'house' ? 'selected' : '' }}>House</option>
                    <option value="flat" {{ old('property_type') == 'flat' ? 'selected' : '' }}>Flat</option>
                    <option value="studio" {{ old('property_type') == 'studio' ? 'selected' : '' }}>Studio</option>
                    <option value="penthouse" {{ old('property_type') == 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                    <option value="townhouse" {{ old('property_type') == 'townhouse' ? 'selected' : '' }}>Townhouse</option>
                </select>
                @error('property_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Location Information -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                    Location <span class="text-red-500">*</span>
                </label>
                <input type="text" name="location" id="location" 
                       value="{{ old('location') }}" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., London, Manchester, Birmingham">
                @error('location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                    Latitude
                </label>
                <input type="number" name="latitude" id="latitude" 
                       value="{{ old('latitude') }}" step="any"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="51.5074">
                @error('latitude')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                    Longitude
                </label>
                <input type="number" name="longitude" id="longitude" 
                       value="{{ old('longitude') }}" step="any"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="-0.1278">
                @error('longitude')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Property Details -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label for="bedrooms" class="block text-sm font-medium text-gray-700 mb-2">
                    Bedrooms
                </label>
                <input type="number" name="bedrooms" id="bedrooms" 
                       value="{{ old('bedrooms') }}" min="0" max="20"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('bedrooms')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="bathrooms" class="block text-sm font-medium text-gray-700 mb-2">
                    Bathrooms
                </label>
                <input type="number" name="bathrooms" id="bathrooms" 
                       value="{{ old('bathrooms') }}" min="0" max="20" step="0.5"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('bathrooms')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                    Monthly Rent <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">£</span>
                    <input type="number" name="price" id="price" 
                           value="{{ old('price') }}" required min="0" step="0.01"
                           class="w-full pl-8 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="1500">
                </div>
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status
                </label>
                <select name="status" id="status" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="rented" {{ old('status') == 'rented' ? 'selected' : '' }}>Rented</option>
                    <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Description
            </label>
            <textarea name="description" id="description" rows="4"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Describe the property, its features, and amenities...">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Additional Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="available_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Available Date
                </label>
                <input type="date" name="available_date" id="available_date" 
                       value="{{ old('available_date') }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('available_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="management_company" class="block text-sm font-medium text-gray-700 mb-2">
                    Management Company
                </label>
                <input type="text" name="management_company" id="management_company" 
                       value="{{ old('management_company') }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., ABC Property Management">
                @error('management_company')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Photos -->
        <div>
            <label for="photos" class="block text-sm font-medium text-gray-700 mb-2">
                Property Photos
            </label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-4"></i>
                <p class="text-sm text-gray-600 mb-2">Upload property photos</p>
                <p class="text-xs text-gray-500 mb-4">PNG, JPG, GIF up to 10MB each</p>
                <input type="file" name="photos[]" id="photos" multiple accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            @error('photos')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Original URL -->
        <div>
            <label for="original_url" class="block text-sm font-medium text-gray-700 mb-2">
                Original Listing URL
            </label>
            <input type="url" name="original_url" id="original_url" 
                   value="{{ old('original_url') }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="https://example.com/property-listing">
            @error('original_url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.properties') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                <i class="fas fa-save mr-2"></i>Create Property
            </button>
        </div>
    </form>
</div>

<!-- Help Section -->
<div class="mt-8 bg-blue-50 rounded-lg border border-blue-200 p-6">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
        <div>
            <h4 class="text-sm font-medium text-blue-900 mb-2">Tips for creating great property listings</h4>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>• Use clear, descriptive titles that highlight key features</li>
                <li>• Include accurate location information for better map integration</li>
                <li>• Upload high-quality photos to showcase the property</li>
                <li>• Provide detailed descriptions including amenities and nearby facilities</li>
                <li>• Set the correct availability date and status</li>
            </ul>
        </div>
    </div>
</div>
@endsection
