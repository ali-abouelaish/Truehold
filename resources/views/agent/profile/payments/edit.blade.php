@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Payment Record</h1>
            <p class="mt-2 text-sm text-gray-600">Update the payment record details.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('agent.profile.payments.update', $payment) }}" method="POST" class="bg-white shadow-lg rounded-lg p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                    <input type="date" 
                           id="date" 
                           name="date" 
                           value="{{ old('date', $payment->date->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date') border-red-500 @enderror"
                           required>
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                    <select id="type" 
                            name="type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror"
                            required>
                        <option value="">Select Type</option>
                        <option value="bonus" {{ old('type', $payment->type) == 'bonus' ? 'selected' : '' }}>Bonus</option>
                        <option value="letting_deal" {{ old('type', $payment->type) == 'letting_deal' ? 'selected' : '' }}>Letting Deal</option>
                        <option value="renewal" {{ old('type', $payment->type) == 'renewal' ? 'selected' : '' }}>Renewal</option>
                        <option value="marketing" {{ old('type', $payment->type) == 'marketing' ? 'selected' : '' }}>Marketing</option>
                        <option value="referral" {{ old('type', $payment->type) == 'referral' ? 'selected' : '' }}>Referral</option>
                        <option value="other" {{ old('type', $payment->type) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="landlord" class="block text-sm font-medium text-gray-700 mb-2">Landlord</label>
                    <input type="text" 
                           id="landlord" 
                           name="landlord" 
                           value="{{ old('landlord', $payment->landlord) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('landlord') border-red-500 @enderror"
                           placeholder="Enter landlord name">
                    @error('landlord')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="property" class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                    <input type="text" 
                           id="property" 
                           name="property" 
                           value="{{ old('property', $payment->property) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('property') border-red-500 @enderror"
                           placeholder="Enter property address">
                    @error('property')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="client" class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                    <input type="text" 
                           id="client" 
                           name="client" 
                           value="{{ old('client', $payment->client) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('client') border-red-500 @enderror"
                           placeholder="Enter client name">
                    @error('client')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="full_commission" class="block text-sm font-medium text-gray-700 mb-2">Full Commission (£) *</label>
                    <input type="number" 
                           id="full_commission" 
                           name="full_commission" 
                           value="{{ old('full_commission', $payment->full_commission) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('full_commission') border-red-500 @enderror"
                           placeholder="0.00"
                           required>
                    @error('full_commission')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="agent_commission" class="block text-sm font-medium text-gray-700 mb-2">Agent Commission (£) *</label>
                    <input type="number" 
                           id="agent_commission" 
                           name="agent_commission" 
                           value="{{ old('agent_commission', $payment->agent_commission) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('agent_commission') border-red-500 @enderror"
                           placeholder="0.00"
                           required>
                    @error('agent_commission')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="invoice_sent_to_management" 
                           name="invoice_sent_to_management" 
                           value="1"
                           {{ old('invoice_sent_to_management', $payment->invoice_sent_to_management) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="invoice_sent_to_management" class="ml-2 block text-sm text-gray-900">
                        Invoice sent to management
                    </label>
                </div>
            </div>

            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                          placeholder="Enter any additional notes">{{ old('notes', $payment->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('agent.profile.payments') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    Update Payment Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
