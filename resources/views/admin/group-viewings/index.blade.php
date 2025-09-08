@extends('layouts.admin')

@section('page-title', 'Group Viewings')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-gray-900">Group Viewings</h2>
    <a href="{{ route('admin.group-viewings.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
        <i class="fas fa-plus mr-2"></i>Create Group Viewing
    </a>
    </div>

@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendees</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($groupViewings as $gv)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $gv->property->title ?? 'Property #'.$gv->property_id }}</div>
                            <div class="text-sm text-gray-500">{{ $gv->property->location ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $gv->scheduled_at->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $gv->attendees->count() }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.group-viewings.create', ['property_id' => $gv->property_id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">New for this property</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-gray-500">No group viewings yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4">
        {{ $groupViewings->links() }}
    </div>
    </div>

    <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Rooms with Interested Clients</h3>
            <span class="text-sm text-gray-600">{{ isset($propertiesWithInterests) ? $propertiesWithInterests->count() : 0 }} properties</span>
        </div>
        <div class="divide-y divide-gray-200">
            @isset($propertiesWithInterests)
            @forelse($propertiesWithInterests as $prop)
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-base font-semibold text-gray-900">{{ $prop->title ?? 'Property #'.$prop->id }}</div>
                            <div class="text-sm text-gray-500">{{ $prop->location }}</div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                <i class="fas fa-user-friends mr-2"></i>{{ $prop->interests_count }} interested
                            </span>
                            <button type="button" class="text-blue-600 hover:text-blue-800 text-sm" onclick="toggleDetails('prop-{{ $prop->id }}')">
                                Details
                            </button>
                            <a href="{{ route('admin.group-viewings.create', ['property_id' => $prop->id]) }}" class="text-green-600 hover:text-green-800 text-sm">Create Viewing</a>
                        </div>
                    </div>
                    <div id="prop-{{ $prop->id }}" class="mt-4 hidden">
                        <ul class="divide-y divide-gray-200">
                            @foreach($prop->interests as $interest)
                                <li class="py-3 flex items-start justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $interest->client->full_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $interest->client->email }} @if($interest->client->phone_number) • {{ $interest->client->phone_number }} @endif</p>
                                        @if($interest->notes)
                                            <p class="text-sm text-gray-500 mt-1">Notes: {{ $interest->notes }}</p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-1">Added {{ $interest->created_at->diffForHumans() }}</p>
                                    </div>
                                    <a href="{{ route('admin.group-viewings.create', ['property_id' => $prop->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">Include</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">No interested clients found.</div>
            @endforelse
            @endisset
        </div>
    </div>
</div>
<script>
function toggleDetails(id) {
    var el = document.getElementById(id);
    if (!el) return;
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
    } else {
        el.classList.add('hidden');
    }
}
</script>
@endsection
