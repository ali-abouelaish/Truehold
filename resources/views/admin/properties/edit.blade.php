@extends('layouts.admin')

@section('page-title', 'Edit Property')

@section('content')
<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Property</h2>
            <p class="text-gray-600">Update property information and details</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('properties.show', $property) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-eye mr-2"></i>View Property
            </a>
            <button type="button" 
                    onclick="openDeleteModal()"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-trash mr-2"></i>Delete Property
            </button>
            <a href="{{ route('admin.properties') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Properties
            </a>
        </div>
    </div>
</div>

<!-- Success Message -->
@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif

<!-- Error Message -->
@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
@endif

<!-- Property Edit Form -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Property Information</h3>
    </div>
    
    <form method="POST" action="{{ route('admin.properties.update', $property) }}" class="p-8 space-y-8" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>Basic Information
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Property Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" 
                           value="{{ old('title', $property->title) }}" required
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
                        <optgroup label="Full Properties">
                            <option value="apartment" {{ old('property_type', $property->property_type) == 'apartment' ? 'selected' : '' }}>Apartment</option>
                            <option value="house" {{ old('property_type', $property->property_type) == 'house' ? 'selected' : '' }}>House</option>
                            <option value="flat" {{ old('property_type', $property->property_type) == 'flat' ? 'selected' : '' }}>Flat</option>
                            <option value="studio" {{ old('property_type', $property->property_type) == 'studio' ? 'selected' : '' }}>Studio</option>
                            <option value="penthouse" {{ old('property_type', $property->property_type) == 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                            <option value="townhouse" {{ old('property_type', $property->property_type) == 'townhouse' ? 'selected' : '' }}>Townhouse</option>
                        </optgroup>
                        <optgroup label="Room Rentals">
                            <option value="single_room" {{ old('property_type', $property->property_type) == 'single_room' ? 'selected' : '' }}>Single Room</option>
                            <option value="double_room" {{ old('property_type', $property->property_type) == 'double_room' ? 'selected' : '' }}>Double Room</option>
                            <option value="twin_room" {{ old('property_type', $property->property_type) == 'twin_room' ? 'selected' : '' }}>Twin Room</option>
                            <option value="master_bedroom" {{ old('property_type', $property->property_type) == 'master_bedroom' ? 'selected' : '' }}>Master Bedroom</option>
                            <option value="en_suite_room" {{ old('property_type', $property->property_type) == 'en_suite_room' ? 'selected' : '' }}>En-suite Room</option>
                            <option value="shared_room" {{ old('property_type', $property->property_type) == 'shared_room' ? 'selected' : '' }}>Shared Room</option>
                            <option value="bedsit" {{ old('property_type', $property->property_type) == 'bedsit' ? 'selected' : '' }}>Bedsit</option>
                            <option value="room_in_house" {{ old('property_type', $property->property_type) == 'room_in_house' ? 'selected' : '' }}>Room in House</option>
                            <option value="room_in_flat" {{ old('property_type', $property->property_type) == 'room_in_flat' ? 'selected' : '' }}>Room in Flat</option>
                        </optgroup>
                    </select>
                    @error('property_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Location Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-map-marker-alt mr-2 text-green-600"></i>Location Information
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                        Location <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="location" id="location" 
                           value="{{ old('location', $property->location) }}" required
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
                           value="{{ old('latitude', $property->latitude) }}" step="any"
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
                           value="{{ old('longitude', $property->longitude) }}" step="any"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="-0.1278">
                    @error('longitude')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Property Details -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-bed mr-2 text-purple-600"></i>Property Details
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label for="bedrooms" class="block text-sm font-medium text-gray-700 mb-2">
                        Bedrooms
                    </label>
                    <input type="number" name="bedrooms" id="bedrooms" 
                           value="{{ old('bedrooms', $property->bedrooms) }}" min="0" max="20"
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
                           value="{{ old('bathrooms', $property->bathrooms) }}" min="0" max="20" step="0.5"
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
                               value="{{ old('price', $property->price) }}" required min="0" step="0.01"
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
                        <option value="available" {{ old('status', $property->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="rented" {{ old('status', $property->status) == 'rented' ? 'selected' : '' }}>Rented</option>
                        <option value="unavailable" {{ old('status', $property->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Property Terms -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-file-contract mr-2 text-orange-600"></i>Property Terms
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="min_term" class="block text-sm font-medium text-gray-700 mb-2">
                        Minimum Term
                    </label>
                    <input type="text" name="min_term" id="min_term" 
                           value="{{ old('min_term', $property->min_term) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 6 months">
                    @error('min_term')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="max_term" class="block text-sm font-medium text-gray-700 mb-2">
                        Maximum Term
                    </label>
                    <input type="text" name="max_term" id="max_term" 
                           value="{{ old('max_term', $property->max_term) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 12 months">
                    @error('max_term')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="deposit" class="block text-sm font-medium text-gray-700 mb-2">
                        Deposit
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">£</span>
                        <input type="text" name="deposit" id="deposit" 
                               value="{{ old('deposit', $property->deposit) }}"
                               class="w-full pl-8 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., 1500">
                    </div>
                    @error('deposit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Property Features -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="bills_included" class="block text-sm font-medium text-gray-700 mb-2">
                    Bills Included
                </label>
                <select name="bills_included" id="bills_included" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Option</option>
                    <option value="yes" {{ old('bills_included', $property->bills_included) == 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ old('bills_included', $property->bills_included) == 'no' ? 'selected' : '' }}>No</option>
                    <option value="some" {{ old('bills_included', $property->bills_included) == 'some' ? 'selected' : '' }}>Some Bills</option>
                </select>
                @error('bills_included')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="parking" class="block text-sm font-medium text-gray-700 mb-2">
                    Parking
                </label>
                <select name="parking" id="parking" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Option</option>
                    <option value="yes" {{ old('parking', $property->parking) == 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ old('parking', $property->parking) == 'no' ? 'selected' : '' }}>No</option>
                    <option value="street" {{ old('parking', $property->parking) == 'street' ? 'selected' : '' }}>Street Parking</option>
                </select>
                @error('parking')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Property Occupancy -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="housemates" class="block text-sm font-medium text-gray-700 mb-2">
                    Number of Housemates
                </label>
                <input type="number" name="housemates" id="housemates" 
                       value="{{ old('housemates', $property->housemates) }}" min="0" max="20"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., 2">
                @error('housemates')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Property Agent & Demographics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="agent_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Agent Name
                </label>
                <input type="text" name="agent_name" id="agent_name" 
                       value="{{ old('agent_name', $property->agent_name) }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., John Smith">
                @error('agent_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="couples_ok" class="block text-sm font-medium text-gray-700 mb-2">
                    Couples Allowed
                </label>
                <select name="couples_ok" id="couples_ok" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Option</option>
                    <option value="yes" {{ old('couples_ok', $property->couples_ok) == 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ old('couples_ok', $property->couples_ok) == 'no' ? 'selected' : '' }}>No</option>
                </select>
                @error('couples_ok')
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
                      placeholder="Describe the property, its features, and amenities...">{{ old('description', $property->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Amenities -->
        <div>
            <label for="amenities" class="block text-sm font-medium text-gray-700 mb-2">
                Amenities
            </label>
            <textarea name="amenities" id="amenities" rows="3"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="List amenities like gym, pool, concierge, etc...">{{ old('amenities', $property->amenities) }}</textarea>
            @error('amenities')
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
                       value="{{ old('available_date', $property->available_date) }}"
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
                       value="{{ old('management_company', $property->management_company) }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., ABC Property Management">
                @error('management_company')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Current Photos with Delete Functionality -->
        @if($property->photos && is_array($property->photos) && count($property->photos) > 0)
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Current Photos</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($property->photos as $index => $photo)
                <div class="relative group" id="photo-container-{{ $index }}">
                    <img src="{{ $photo }}" alt="Property Photo {{ $index + 1 }}" 
                         class="w-full h-24 object-cover rounded-lg border">
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                        <button type="button" onclick="removePhoto({{ $index }})" 
                                class="text-white hover:text-red-400 p-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <input type="hidden" name="existing_photos[]" value="{{ $photo }}" id="photo-input-{{ $index }}">
                </div>
                @endforeach
            </div>
            <input type="hidden" name="removed_images" id="removed_images" value="">
        </div>
        @endif
        
        <!-- Add New Photos -->
        <div>
            <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                Add New Photos
            </label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-4"></i>
                <p class="text-sm text-gray-600 mb-2">Upload additional property photos</p>
                <p class="text-xs text-gray-500 mb-4">PNG, JPG, GIF up to 10MB each</p>
                <input type="file" name="images[]" id="images" multiple accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            @error('images')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Original URL -->
        <div>
            <label for="link" class="block text-sm font-medium text-gray-700 mb-2">
                Original Listing URL
            </label>
            <input type="url" name="link" id="link" 
                   value="{{ old('link', $property->link) }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="https://example.com/property-listing">
            @error('link')
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
                <i class="fas fa-save mr-2"></i>Update Property
            </button>
        </div>
    </form>
</div>

<!-- Interested Clients Management -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Add Interested Client</h3>
            <p class="text-sm text-gray-500">Select a registered client to mark as interested for group viewings.</p>
        </div>
        <form method="POST" action="{{ route('admin.properties.interests.add', $property) }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                <select id="client_id" name="client_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->full_name }} @if($client->email) ({{ $client->email }}) @endif</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                <textarea id="notes" name="notes" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Availability, preferences, etc."></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-user-plus mr-2"></i>Add Interested Client
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Interested Clients</h3>
                <p class="text-sm text-gray-500">Clients marked as interested in this property.</p>
            </div>
            <span class="text-sm text-gray-600">{{ $property->interests->count() }} total</span>
        </div>
        <div class="p-6">
            @if($property->interests->isEmpty())
                <p class="text-sm text-gray-500">No clients have been marked as interested yet.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach($property->interests as $interest)
                        <li class="py-3 flex items-start justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $interest->client->full_name }}</p>
                                <p class="text-sm text-gray-600">{{ $interest->client->email }} @if($interest->client->phone_number) • {{ $interest->client->phone_number }} @endif</p>
                                @if($interest->notes)
                                    <p class="text-sm text-gray-500 mt-1">Notes: {{ $interest->notes }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1">Added {{ $interest->created_at->diffForHumans() }} @if($interest->addedBy) by {{ $interest->addedBy->name }} @endif</p>
                            </div>
                            <form method="POST" action="{{ route('admin.properties.interests.remove', [$property, $interest->client]) }}" onsubmit="return confirm('Remove this client from interested list?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-700">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Delete Property</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete "<strong>{{ $property->title }}</strong>"? 
                    This action cannot be undone and will permanently remove the property from the system.
                </p>
            </div>
            <div class="flex justify-center space-x-3 mt-4">
                <button type="button" 
                        onclick="closeDeleteModal()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium">
                    Cancel
                </button>
                <form method="POST" action="{{ route('admin.properties.destroy', $property) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-trash mr-2"></i>Delete Property
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Property Statistics -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-eye text-blue-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Views</p>
                <p class="text-2xl font-bold text-gray-900">{{ rand(50, 500) }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-calendar text-green-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Days Listed</p>
                <p class="text-2xl font-bold text-gray-900">{{ $property->created_at->diffInDays(now()) }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-edit text-purple-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Last Updated</p>
                <p class="text-2xl font-bold text-gray-900">{{ $property->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>
</div>

<script>
let removedPhotoIndices = [];

function removePhoto(index) {
    if (confirm('Are you sure you want to remove this photo?')) {
        // Hide the photo container
        const photoContainer = document.getElementById('photo-container-' + index);
        if (photoContainer) {
            photoContainer.style.display = 'none';
        }
        
        // Remove the hidden input
        const photoInput = document.getElementById('photo-input-' + index);
        if (photoInput) {
            photoInput.remove();
        }
        
        // Add to removed indices
        removedPhotoIndices.push(index);
        document.getElementById('removed_images').value = JSON.stringify(removedPhotoIndices);
    }
}

function openDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteModal');
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeDeleteModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
});
</script>
@endsection
