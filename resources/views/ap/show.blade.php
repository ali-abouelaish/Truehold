<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property->property_name }} - TRUEHOLD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .image-grid{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:0.5rem}
        .image-grid .main{grid-column:span 12;}
        @media(min-width:768px){.image-grid .main{grid-column:span 8}.image-grid .side{grid-column:span 4}}
    </style>
    </head>
<body class="font-sans antialiased bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <a href="{{ route('ap.public.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back to listings</a>

        <div class="mt-4 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ $property->property_name }}</h1>
                <div class="mt-1 text-gray-600">{{ $property->area }} {{ $property->postcode ? ' • ' . $property->postcode : '' }}</div>
                <div class="mt-3 flex items-center gap-4 text-gray-800">
                    <div class="text-xl font-bold">£{{ number_format((int)($property->pcm ?? 0)) }} pcm</div>
                    <div>{{ $property->n_rooms }} bed • {{ $property->n_bathrooms }} bath</div>
                    <div class="text-sm text-gray-600">{{ $property->availability_label }}</div>
                    <div class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700 border">{{ $property->status_label }}</div>
                    @if($property->is_house_share)
                        <div class="text-xs px-2 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">House share</div>
                    @endif
                </div>
            </div>

            <div class="p-6 pt-0">
                @php($images = $property->images_url ?? [])
                @if(count($images))
                    <div class="image-grid">
                        <div class="main">
                            @php($mainSrc = preg_match('/^https?:/i', $images[0]) ? $images[0] : Storage::url($images[0]))
                            <img src="{{ $mainSrc }}" alt="Main image" class="w-full h-80 object-cover rounded-lg">
                        </div>
                        <div class="side grid grid-cols-2 gap-2">
                            @foreach(array_slice($images, 1, 6) as $url)
                                @php($src = preg_match('/^https?:/i', $url) ? $url : Storage::url($url))
                                <img src="{{ $src }}" alt="Property image" class="w-full h-40 object-cover rounded-lg">
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">No images available</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>


