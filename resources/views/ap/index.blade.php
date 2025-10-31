<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AP Flats - TRUEHOLD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900">Available Flats</h1>
            <p class="text-gray-600">Browse full flats available to rent</p>
        </div>

        @if($properties->isEmpty())
            <div class="text-center text-gray-600">No flats available at the moment.</div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($properties as $property)
                <a href="{{ route('ap.public.show', $property) }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition overflow-hidden">
                    @php($firstImage = ($property->images_url[0] ?? null))
                    @if($firstImage)
                        @php($src = preg_match('/^https?:/i', $firstImage) ? $firstImage : Storage::url($firstImage))
                        <img src="{{ $src }}" alt="{{ $property->property_name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">No Image</div>
                    @endif
                    <div class="p-4">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $property->property_name }}</h2>
                        <div class="mt-2 text-sm text-gray-600">{{ $property->area }} {{ $property->postcode ? ' • ' . $property->postcode : '' }}</div>
                        <div class="mt-2 text-xs">
                            <span class="inline-block px-2 py-1 rounded-full bg-gray-100 text-gray-700 border">{{ $property->status_label }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-primary-600 font-bold">£{{ number_format((int)($property->pcm ?? 0)) }} pcm</div>
                            <div class="text-sm text-gray-600">{{ $property->n_rooms }} bed • {{ $property->n_bathrooms }} bath</div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">{{ $property->availability_label }}</div>
                    </div>
                </a>
                @endforeach
            </div>

            @if($properties->hasPages())
            <div class="mt-8">
                {{ $properties->links() }}
            </div>
            @endif
        @endif
    </div>
</body>
</html>


