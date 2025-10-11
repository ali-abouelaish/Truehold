@extends('layouts.admin')

@section('title', 'Submit Cash Document')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Submit Cash Document</h1>
            <p class="text-gray-600 mt-2">Upload contact documents and cash receipt for client verification.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('cash-documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <!-- Client Selection -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-user mr-2"></i>Client Information
                </h2>
                
                <div class="mb-4">
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Select Client *</label>
                    <select name="client_id" id="client_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Choose a client...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->full_name }} - {{ $client->phone_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Contact Documents Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-id-card mr-2"></i>Contact Documents
                </h2>
                <p class="text-gray-600 mb-4">Upload up to 4 images of contact documents (ID, passport, etc.)</p>
                
                <div class="mb-4">
                    <label for="contact_images" class="block text-sm font-medium text-gray-700 mb-2">Contact Document Images *</label>
                    <input type="file" name="contact_images[]" id="contact_images" multiple accept="image/*" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <p class="text-xs text-gray-500 mt-1">Select 1-4 images. Max 5MB per image. Supported formats: JPEG, PNG, JPG, GIF</p>
                </div>
                
                <!-- Image Preview -->
                <div id="contact-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 hidden">
                    <!-- Preview images will be inserted here -->
                </div>
            </div>

            <!-- Client ID Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-id-badge mr-2"></i>Client ID Document
                </h2>
                <p class="text-gray-600 mb-4">Upload image of client's ID document (passport, driver's license, etc.)</p>
                
                <div class="mb-4">
                    <label for="client_id_image" class="block text-sm font-medium text-gray-700 mb-2">Client ID Image *</label>
                    <input type="file" name="client_id_image" id="client_id_image" accept="image/*" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <p class="text-xs text-gray-500 mt-1">Max 5MB. Supported formats: JPEG, PNG, JPG, GIF</p>
                </div>
                
                <!-- Client ID Preview -->
                <div id="client-id-preview" class="mt-4 hidden">
                    <img id="client-id-img" class="max-w-xs rounded-lg shadow" alt="Client ID preview">
                </div>
            </div>

            <!-- Cash Receipt Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-money-bill-wave mr-2"></i>Cash Receipt
                </h2>
                <p class="text-gray-600 mb-4">Upload image of cash envelope or transfer receipt</p>
                
                <div class="mb-4">
                    <label for="cash_receipt_image" class="block text-sm font-medium text-gray-700 mb-2">Cash Receipt Image *</label>
                    <input type="file" name="cash_receipt_image" id="cash_receipt_image" accept="image/*" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <p class="text-xs text-gray-500 mt-1">Max 5MB. Supported formats: JPEG, PNG, JPG, GIF</p>
                </div>
                
                <!-- Cash Receipt Preview -->
                <div id="cash-receipt-preview" class="mt-4 hidden">
                    <img id="cash-receipt-img" class="max-w-xs rounded-lg shadow" alt="Cash receipt preview">
                </div>
            </div>

            <!-- Notes Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-sticky-note mr-2"></i>Additional Notes
                </h2>
                
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Add any additional information about the cash collection...">{{ old('notes') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Maximum 1000 characters</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('rental-codes.cash-documents.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Document
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Contact images preview
document.getElementById('contact_images').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const preview = document.getElementById('contact-preview');
    
    if (files.length > 0) {
        preview.classList.remove('hidden');
        preview.innerHTML = '';
        
        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-32 object-cover rounded-lg shadow';
                    img.alt = `Contact document ${index + 1}`;
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    } else {
        preview.classList.add('hidden');
    }
});

// Client ID preview
document.getElementById('client_id_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('client-id-preview');
    const img = document.getElementById('client-id-img');
    
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
});

// Cash receipt preview
document.getElementById('cash_receipt_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('cash-receipt-preview');
    const img = document.getElementById('cash-receipt-img');
    
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const contactImages = document.getElementById('contact_images').files;
    const clientId = document.getElementById('client_id_image').files;
    const cashReceipt = document.getElementById('cash_receipt_image').files;
    
    if (contactImages.length < 1 || contactImages.length > 4) {
        e.preventDefault();
        alert('Please select between 1 and 4 contact document images.');
        return;
    }
    
    if (clientId.length === 0) {
        e.preventDefault();
        alert('Please select a client ID image.');
        return;
    }
    
    if (cashReceipt.length === 0) {
        e.preventDefault();
        alert('Please select a cash receipt image.');
        return;
    }
});
</script>
@endsection
