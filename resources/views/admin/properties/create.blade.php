@extends('layouts.admin')

@section('page-title', 'Create New Property')

@section('content')
<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Add New Property to Google Sheets</h2>
            <p class="text-gray-600">Add a new property listing directly to the Google Sheets Properties worksheet</p>
        </div>
        <a href="{{ route('properties.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Properties
        </a>
    </div>
</div>

<!-- Property Creation Form -->
<div class="bg-white rounded-lg shadow-lg border border-gray-200">
    <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
            <i class="fas fa-home mr-3 text-blue-600"></i>Property Information
        </h3>
        <p class="text-sm text-gray-600 mt-1">Fill in the details below to create a new property listing</p>
    </div>
    
    <form method="POST" action="{{ route('properties.store') }}" class="p-8 space-y-8">
        @csrf
        
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
                    <optgroup label="Full Properties">
                        <option value="apartment" {{ old('property_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                        <option value="house" {{ old('property_type') == 'house' ? 'selected' : '' }}>House</option>
                        <option value="flat" {{ old('property_type') == 'flat' ? 'selected' : '' }}>Flat</option>
                        <option value="studio" {{ old('property_type') == 'studio' ? 'selected' : '' }}>Studio</option>
                        <option value="penthouse" {{ old('property_type') == 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                        <option value="townhouse" {{ old('property_type') == 'townhouse' ? 'selected' : '' }}>Townhouse</option>
                    </optgroup>
                    <optgroup label="Room Rentals">
                        <option value="single_room" {{ old('property_type') == 'single_room' ? 'selected' : '' }}>Single Room</option>
                        <option value="double_room" {{ old('property_type') == 'double_room' ? 'selected' : '' }}>Double Room</option>
                        <option value="twin_room" {{ old('property_type') == 'twin_room' ? 'selected' : '' }}>Twin Room</option>
                        <option value="master_bedroom" {{ old('property_type') == 'master_bedroom' ? 'selected' : '' }}>Master Bedroom</option>
                        <option value="en_suite_room" {{ old('property_type') == 'en_suite_room' ? 'selected' : '' }}>En-suite Room</option>
                        <option value="shared_room" {{ old('property_type') == 'shared_room' ? 'selected' : '' }}>Shared Room</option>
                        <option value="bedsit" {{ old('property_type') == 'bedsit' ? 'selected' : '' }}>Bedsit</option>
                        <option value="room_in_house" {{ old('property_type') == 'room_in_house' ? 'selected' : '' }}>Room in House</option>
                        <option value="room_in_flat" {{ old('property_type') == 'room_in_flat' ? 'selected' : '' }}>Room in Flat</option>
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
                       value="{{ old('location') }}" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., Devons Road, London">
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
                       value="{{ old('min_term') }}"
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
                       value="{{ old('max_term') }}"
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
                           value="{{ old('deposit') }}"
                           class="w-full pl-8 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 1500">
                </div>
                @error('deposit')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                    <option value="yes" {{ old('bills_included') == 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ old('bills_included') == 'no' ? 'selected' : '' }}>No</option>
                    <option value="some" {{ old('bills_included') == 'some' ? 'selected' : '' }}>Some Bills</option>
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
                    <option value="yes" {{ old('parking') == 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ old('parking') == 'no' ? 'selected' : '' }}>No</option>
                    <option value="street" {{ old('parking') == 'street' ? 'selected' : '' }}>Street Parking</option>
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
                       value="{{ old('housemates') }}" min="0" max="20"
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
                       value="{{ old('agent_name') }}"
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
                    <option value="yes" {{ old('couples_ok') == 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ old('couples_ok') == 'no' ? 'selected' : '' }}>No</option>
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
                      placeholder="Describe the property, its features, and amenities...">{{ old('description') }}</textarea>
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
                      placeholder="List amenities like gym, pool, concierge, etc...">{{ old('amenities') }}</textarea>
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
                       value="{{ old('available_date') }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('available_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Original URL -->
        <div>
            <label for="url" class="block text-sm font-medium text-gray-700 mb-2">
                Original Listing URL
            </label>
            <input type="url" name="url" id="url" 
                   value="{{ old('url') }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="https://example.com/property-listing">
            @error('url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Additional Google Sheets Fields -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-cog mr-2 text-gray-600"></i>Additional Information
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="furnishings" class="block text-sm font-medium text-gray-700 mb-2">
                        Furnishings
                    </label>
                    <select name="furnishings" id="furnishings" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Option</option>
                        <option value="furnished" {{ old('furnishings') == 'furnished' ? 'selected' : '' }}>Furnished</option>
                        <option value="unfurnished" {{ old('furnishings') == 'unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                        <option value="partially_furnished" {{ old('furnishings') == 'partially_furnished' ? 'selected' : '' }}>Partially Furnished</option>
                    </select>
                </div>
                
                <div>
                    <label for="garden" class="block text-sm font-medium text-gray-700 mb-2">
                        Garden/Patio
                    </label>
                    <select name="garden" id="garden" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Option</option>
                        <option value="yes" {{ old('garden') == 'yes' ? 'selected' : '' }}>Yes</option>
                        <option value="no" {{ old('garden') == 'no' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                
                <div>
                    <label for="broadband" class="block text-sm font-medium text-gray-700 mb-2">
                        Broadband Included
                    </label>
                    <select name="broadband" id="broadband" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Option</option>
                        <option value="yes" {{ old('broadband') == 'yes' ? 'selected' : '' }}>Yes</option>
                        <option value="no" {{ old('broadband') == 'no' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                
                <div>
                    <label for="total_rooms" class="block text-sm font-medium text-gray-700 mb-2">
                        Total Rooms
                    </label>
                    <input type="text" name="total_rooms" id="total_rooms" 
                           value="{{ old('total_rooms') }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 4">
                </div>
                
                <div>
                    <label for="management_company" class="block text-sm font-medium text-gray-700 mb-2">
                        Management Company
                    </label>
                    <input type="text" name="management_company" id="management_company" 
                           value="{{ old('management_company') }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Company Name">
                </div>
                
                <div>
                    <label for="contact_info" class="block text-sm font-medium text-gray-700 mb-2">
                        Contact Information
                    </label>
                    <input type="text" name="contact_info" id="contact_info" 
                           value="{{ old('contact_info') }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Phone or Email">
                </div>
                
                <div>
                    <label for="pets_ok" class="block text-sm font-medium text-gray-700 mb-2">
                        Pets Allowed
                    </label>
                    <select name="pets_ok" id="pets_ok" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Option</option>
                        <option value="yes" {{ old('pets_ok') == 'yes' ? 'selected' : '' }}>Yes</option>
                        <option value="no" {{ old('pets_ok') == 'no' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                
                <div>
                    <label for="smoking_ok" class="block text-sm font-medium text-gray-700 mb-2">
                        Smoking Allowed
                    </label>
                    <select name="smoking_ok" id="smoking_ok" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Option</option>
                        <option value="yes" {{ old('smoking_ok') == 'yes' ? 'selected' : '' }}>Yes</option>
                        <option value="no" {{ old('smoking_ok') == 'no' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                
                <div>
                    <label for="first_photo_url" class="block text-sm font-medium text-gray-700 mb-2">
                        First Photo URL
                    </label>
                    <input type="url" name="first_photo_url" id="first_photo_url" 
                           value="{{ old('first_photo_url') }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="https://example.com/photo.jpg">
                </div>
                
                <div>
                    <label for="all_photos" class="block text-sm font-medium text-gray-700 mb-2">
                        All Photos (comma-separated URLs)
                    </label>
                    <textarea name="all_photos" id="all_photos" rows="2"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="https://example.com/photo1.jpg, https://example.com/photo2.jpg">{{ old('all_photos') }}</textarea>
                </div>
                
                <div>
                    <label for="paying" class="block text-sm font-medium text-gray-700 mb-2">
                        Paying
                    </label>
                    <select name="paying" id="paying" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Option</option>
                        <option value="yes" {{ old('paying') == 'yes' ? 'selected' : '' }}>Yes</option>
                        <option value="no" {{ old('paying') == 'no' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('properties.index') }}" 
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
                <li>• For room rentals, specify room amenities and shared areas</li>
            </ul>
        </div>
    </div>
</div>

@endsection
