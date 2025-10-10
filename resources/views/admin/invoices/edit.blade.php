@extends('layouts.admin')

@section('title', 'Edit Invoice ' . $invoice->invoice_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Invoice {{ $invoice->invoice_number }}</h1>
            <p class="text-gray-600 mt-2">Update invoice details</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.invoices.show', $invoice) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                View Invoice
            </a>
            <a href="{{ route('admin.invoices.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                Back to Invoices
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <strong>Success!</strong> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <strong>Please fix the following errors:</strong>
            <ul class="mt-2">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.invoices.update', $invoice) }}" class="space-y-8" id="invoice-form">
        @csrf
        @method('PUT')
        
        <!-- Receiver's Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Receiver's Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="client_name" class="block text-sm font-medium text-gray-700 mb-2">Receiver Name *</label>
                    <input type="text" id="client_name" name="client_name" value="{{ old('client_name', $invoice->client_name) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('client_name') border-red-500 @enderror" required>
                    @error('client_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="client_email" class="block text-sm font-medium text-gray-700 mb-2">Receiver Email</label>
                    <input type="email" id="client_email" name="client_email" value="{{ old('client_email', $invoice->client_email) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('client_email') border-red-500 @enderror">
                    @error('client_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="client_phone" class="block text-sm font-medium text-gray-700 mb-2">Receiver Phone</label>
                    <input type="text" id="client_phone" name="client_phone" value="{{ old('client_phone', $invoice->client_phone) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('client_phone') border-red-500 @enderror">
                    @error('client_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="client_address" class="block text-sm font-medium text-gray-700 mb-2">Receiver Address *</label>
                    <textarea id="client_address" name="client_address" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('client_address') border-red-500 @enderror" required>{{ old('client_address', $invoice->client_address) }}</textarea>
                    @error('client_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Invoice Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-2">Invoice Date *</label>
                    <input type="date" id="invoice_date" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('invoice_date') border-red-500 @enderror" required>
                    @error('invoice_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date *</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('due_date') border-red-500 @enderror" required>
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Payment Terms *</label>
                    <select id="payment_terms" name="payment_terms" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('payment_terms') border-red-500 @enderror" required>
                        <option value="30 days" {{ old('payment_terms', $invoice->payment_terms) == '30 days' ? 'selected' : '' }}>30 days</option>
                        <option value="15 days" {{ old('payment_terms', $invoice->payment_terms) == '15 days' ? 'selected' : '' }}>15 days</option>
                        <option value="7 days" {{ old('payment_terms', $invoice->payment_terms) == '7 days' ? 'selected' : '' }}>7 days</option>
                        <option value="Due on receipt" {{ old('payment_terms', $invoice->payment_terms) == 'Due on receipt' ? 'selected' : '' }}>Due on receipt</option>
                        <option value="Net 60" {{ old('payment_terms', $invoice->payment_terms) == 'Net 60' ? 'selected' : '' }}>Net 60</option>
                    </select>
                    @error('payment_terms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
            </div>
        </div>

        <!-- Items -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Invoice Items</h3>
                <button type="button" id="add-item" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    Add Item
                </button>
            </div>
            
            <div id="items-container">
                @foreach($invoice->items as $index => $item)
                <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-4 items-end mb-4">
                    <div class="md:col-span-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <input type="text" name="items[{{ $index }}][description]" value="{{ $item['description'] }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                        <input type="number" name="items[{{ $index }}][quantity]" step="0.01" min="0.01" value="{{ $item['quantity'] }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rate (£) *</label>
                        <input type="number" name="items[{{ $index }}][rate]" step="0.01" min="0" value="{{ $item['rate'] }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                    </div>
                    <div class="md:col-span-1">
                        <button type="button" class="remove-item bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded-lg transition duration-200 w-full">
                            Remove
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Tax and Additional Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Additional Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                    <input type="number" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $invoice->tax_rate) }}" step="0.01" min="0" max="100" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tax_rate') border-red-500 @enderror">
                    @error('tax_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea id="notes" name="notes" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $invoice->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="terms" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                    <textarea id="terms" name="terms" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('terms') border-red-500 @enderror">{{ old('terms', $invoice->terms) }}</textarea>
                    @error('terms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.invoices.show', $invoice) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200"
                    id="update-button">
                Update Invoice
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ count($invoice->items) }};
    
    // Form submission handling
    const form = document.getElementById('invoice-form');
    const updateButton = document.getElementById('update-button');
    
    form.addEventListener('submit', function(e) {
        console.log('Form submission started...');
        
        // Show loading state
        updateButton.disabled = true;
        updateButton.innerHTML = 'Updating...';
        updateButton.classList.add('opacity-50', 'cursor-not-allowed');
        
        // Validate required fields
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        let emptyFields = [];
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                emptyFields.push(field.name || field.id);
                isValid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            updateButton.disabled = false;
            updateButton.innerHTML = 'Update Invoice';
            updateButton.classList.remove('opacity-50', 'cursor-not-allowed');
            
            console.error('Validation failed. Empty fields:', emptyFields);
            alert('Please fill in all required fields: ' + emptyFields.join(', '));
            return false;
        }
        
        // If validation passes, allow form submission
        console.log('Form validation passed, submitting...');
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        
        // Log form data
        const formData = new FormData(form);
        console.log('Form data being submitted:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        // Add a small delay to show the loading state
        setTimeout(() => {
            // Form will submit naturally
        }, 100);
    });
    
    // Add item functionality
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const newItem = document.querySelector('.item-row').cloneNode(true);
        
        // Update input names and clear values
        newItem.querySelectorAll('input').forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, `[${itemIndex}]`);
            }
            if (input.type !== 'hidden' && !input.readOnly) {
                input.value = '';
            }
        });
        
        container.appendChild(newItem);
        itemIndex++;
        
        // Add event listeners to new item
        addItemEventListeners(newItem);
    });
    
    // Remove item functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            const itemRow = e.target.closest('.item-row');
            if (document.querySelectorAll('.item-row').length > 1) {
                itemRow.remove();
            }
        }
    });
    
    // Calculate amount functionality
    function addItemEventListeners(itemRow) {
        const quantityInput = itemRow.querySelector('input[name*="[quantity]"]');
        const rateInput = itemRow.querySelector('input[name*="[rate]"]');
        const amountInput = itemRow.querySelector('input[readonly]');
        
        function calculateAmount() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;
            const amount = quantity * rate;
            amountInput.value = '£' + amount.toFixed(2);
        }
        
        quantityInput.addEventListener('input', calculateAmount);
        rateInput.addEventListener('input', calculateAmount);
        
        // Calculate initial amount
        calculateAmount();
    }
    
    // Add event listeners to initial items
    document.querySelectorAll('.item-row').forEach(addItemEventListeners);
});
</script>
@endsection
