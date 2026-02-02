<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $property->title ?: 'Property' }} - Property Scraper</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    
    <style>
        .formal-header {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border-bottom: 2px solid #4b5563;
        }
        
        .formal-button {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border: 1px solid #4b5563;
            transition: all 0.2s ease;
        }
        
        .formal-button:hover {
            background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .form-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        
        .form-input {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 12px 16px;
            transition: all 0.2s ease;
        }
        
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="formal-header shadow-lg">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <a href="{{ route('properties.show', $property) }}" class="text-blue-400 hover:text-white mr-4 transition-colors">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </a>
                        <i class="fas fa-edit text-3xl text-blue-400 mr-4"></i>
                        <h1 class="text-3xl font-bold text-white">Edit Property</h1>
                    </div>
                    <div class="text-sm text-gray-300">
                        <span>Editing: {{ $property->title ?: 'Property' }}</span>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-6 lg:px-8 py-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('properties.update', $property) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="form-card p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-info-circle mr-3 text-blue-600"></i>Basic Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" id="title" name="title" value="{{ old('title', $property->title) }}" 
                                   class="form-input w-full" required>
                        </div>
                        
                        <div>
                            <label for="location" class="form-label">Location *</label>
                            <input type="text" id="location" name="location" value="{{ old('location', $property->location) }}" 
                                   class="form-input w-full" required>
                        </div>
                        
                        <div>
                            <label for="price" class="form-label">Price</label>
                            <input type="number" id="price" name="price" value="{{ old('price', $property->price) }}" 
                                   class="form-input w-full" step="0.01" min="0">
                        </div>
                        
                        <div>
                            <label for="property_type" class="form-label">Property Type</label>
                            <input type="text" id="property_type" name="property_type" value="{{ old('property_type', $property->property_type) }}" 
                                   class="form-input w-full">
                        </div>
                        
                        <div>
                            <label for="available_date" class="form-label">Available Date</label>
                            <input type="text" id="available_date" name="available_date" value="{{ old('available_date', $property->available_date) }}" 
                                   class="form-input w-full" placeholder="e.g., Available Now, 1st January 2024">
                        </div>
                        
                        <div>
                            <label for="management_company" class="form-label">Management Company</label>
                            <input type="text" id="management_company" name="management_company" value="{{ old('management_company', $property->management_company) }}" 
                                   class="form-input w-full">
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-card p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-align-left mr-3 text-blue-600"></i>Description
                    </h2>
                    
                                            <div>
                            <label for="description" class="form-label">Property Description</label>
                            <textarea id="description" name="description" rows="8" 
                                      class="form-input w-full" placeholder="Enter property description with line breaks for better formatting...">{{ old('description', $property->description) }}</textarea>
                            <p class="text-sm text-gray-500 mt-2">Use line breaks to separate different sections for better readability.</p>
                        </div>
                </div>

                <!-- Property Features -->
                <div class="form-card p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-home mr-3 text-blue-600"></i>Property Features
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="amenities" class="form-label">Amenities</label>
                            <textarea id="amenities" name="amenities" rows="3" 
                                      class="form-input w-full">{{ old('amenities', $property->amenities) }}</textarea>
                        </div>
                        
                        <div>
                            <label for="bills_included" class="form-label">Bills Included</label>
                            <input type="text" id="bills_included" name="bills_included" value="{{ old('bills_included', $property->bills_included) }}" 
                                   class="form-input w-full" placeholder="e.g., Council Tax, Water, Electricity">
                        </div>
                        
                        <div>
                            <label for="deposit" class="form-label">Deposit</label>
                            <input type="text" id="deposit" name="deposit" value="{{ old('deposit', $property->deposit) }}" 
                                   class="form-input w-full" placeholder="e.g., £500, 1 month rent">
                        </div>
                        
                        <div>
                            <label for="minimum_term" class="form-label">Minimum Term</label>
                            <input type="text" id="minimum_term" name="minimum_term" value="{{ old('minimum_term', $property->minimum_term) }}" 
                                   class="form-input w-full" placeholder="e.g., 6 months, 1 year">
                        </div>
                        
                        <div>
                            <label for="furnishings" class="form-label">Furnishings</label>
                            <input type="text" id="furnishings" name="furnishings" value="{{ old('furnishings', $property->furnishings) }}" 
                                   class="form-input w-full" placeholder="e.g., Furnished, Part-furnished, Unfurnished">
                        </div>
                        
                        <div>
                            <label for="garden_patio" class="form-label">Garden/Patio</label>
                            <input type="text" id="garden_patio" name="garden_patio" value="{{ old('garden_patio', $property->garden_patio) }}" 
                                   class="form-input w-full" placeholder="e.g., Private garden, Shared patio">
                        </div>
                    </div>
                </div>

                <!-- Contact & Location -->
                <div class="form-card p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-map-marker-alt mr-3 text-blue-600"></i>Contact & Location
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="contact_info" class="form-label">Contact Information</label>
                            <textarea id="contact_info" name="contact_info" rows="3" 
                                      class="form-input w-full">{{ old('contact_info', $property->contact_info) }}</textarea>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" id="latitude" name="latitude" value="{{ old('latitude', $property->latitude) }}" 
                                       class="form-input w-full" step="0.000001" min="-90" max="90">
                            </div>
                            
                            <div>
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" id="longitude" name="longitude" value="{{ old('longitude', $property->longitude) }}" 
                                       class="form-input w-full" step="0.000001" min="-180" max="180">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Property Status -->
                <div class="form-card p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-info-circle mr-3 text-blue-600"></i>Property Status
                    </h2>
                    
                    <div>
                        <label for="status" class="form-label">Current Status</label>
                        <select id="status" name="status" class="form-input w-full">
                            <option value="available" {{ old('status', $property->status) == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="rented" {{ old('status', $property->status) == 'rented' ? 'selected' : '' }}>Rented</option>
                            <option value="unavailable" {{ old('status', $property->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                            <option value="on_hold" {{ old('status', $property->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        </select>
                        <p class="text-sm text-gray-500 mt-2">Update the availability status of this property</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('properties.show', $property) }}" 
                       class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    
                    <button type="submit" class="formal-button text-white px-8 py-3 rounded-lg font-medium">
                        <i class="fas fa-save mr-2"></i>Update Property
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        // Auto-resize textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
        
        // Initialize textarea heights
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.style.height = textarea.scrollHeight + 'px';
            });
        });
    </script>
</body>
</html>
