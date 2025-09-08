@extends('layouts.admin')

@section('page-title', 'Create Group Viewing')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Create Group Viewing</h2>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <form method="POST" action="{{ route('admin.group-viewings.store') }}" class="p-6 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                <select name="property_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select a property</option>
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}" {{ ($property && $property->id === $p->id) ? 'selected' : '' }}>
                            {{ $p->title ?? 'Property #'.$p->id }} @if($p->location) - {{ $p->location }} @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date & Time</label>
                <input type="datetime-local" name="scheduled_at" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Location (optional)</label>
                <input type="text" name="location" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Meet at property address or office">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <input type="text" name="notes" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Parking, access codes, agent name, etc.">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Attendees (registered clients)</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-64 overflow-y-auto border rounded-lg p-3">
                @php
                    $preselected = collect();
                    if ($property && $property->interests) {
                        $preselected = $property->interests->pluck('client_id');
                    }
                @endphp
                @foreach($clients as $client)
                    <label class="flex items-center space-x-3 text-sm">
                        <input type="checkbox" name="attendees[]" value="{{ $client->id }}" class="rounded text-blue-600" {{ $preselected->contains($client->id) ? 'checked' : '' }}>
                        <span>{{ $client->full_name }} @if($client->email) ({{ $client->email }}) @endif</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <a href="{{ route('admin.group-viewings.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                <i class="fas fa-save mr-2"></i>Create Group Viewing
            </button>
        </div>
    </form>
</div>
@endsection


