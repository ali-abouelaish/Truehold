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
                <div class="block bg-white rounded-2xl border border-gray-200 hover:shadow-xl transition overflow-hidden cursor-pointer" style="box-shadow: 0 8px 24px rgba(0,0,0,0.06);" onclick="apCardClick(event, '{{ route('ap.public.show', $property) }}')">
                    <div style="height: 3px; background: linear-gradient(90deg,#fbbf24,#fde68a);"></div>
                    @php($firstImage = ($property->images_url[0] ?? null))
                    @if($firstImage)
                        @php($src = preg_match('/^https?:/i', $firstImage) ? $firstImage : Storage::url($firstImage))
                        <img src="{{ $src }}" alt="{{ $property->property_name }}" class="w-full h-56 object-cover" style="filter: saturate(1.05) contrast(1.02);">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">No Image</div>
                    @endif
                    <div class="p-5">
                         <h2 class="text-xl font-semibold text-gray-900">{{ $property->property_name }}</h2>
                         @if(($property->type ?? 'full_flat') === 'house_share' && $property->room_label)
                             <div class="text-xs text-gray-500">Room: {{ $property->room_label }}</div>
                         @endif
                        <div class="mt-2 text-sm text-gray-600">{{ $property->area }} {{ $property->postcode ? ' • ' . $property->postcode : '' }}</div>
                        <div class="mt-2 text-xs space-x-1">
                            <span class="inline-block px-2 py-1 rounded-full border" style="background:#fff7ed; border-color:#fed7aa; color:#9a3412;">{{ $property->status_label }}</span>
                             @if(($property->type ?? 'full_flat') === 'house_share')
                                <span class="inline-block px-2 py-1 rounded-full border" style="background:#eff6ff; border-color:#bfdbfe; color:#1d4ed8;">House share</span>
                             @endif
                             @if($property->is_room)
                                <span class="inline-block px-2 py-1 rounded-full border" style="background:#eef2ff; border-color:#c7d2fe; color:#3730a3;">Room</span>
                             @endif
                             @if($property->couples_allowed)
                                <span class="inline-block px-2 py-1 rounded-full border" style="background:#ecfdf5; border-color:#bbf7d0; color:#166534;">Couples</span>
                             @endif
                         </div>
                        <div class="mt-4 flex items-center justify-between">
                            <div class="font-bold" style="color:#b45309;">£{{ number_format((int)($property->pcm ?? 0)) }} pcm</div>
                            <div class="text-sm text-gray-600">{{ $property->n_rooms }} bed • {{ $property->n_bathrooms }} bath</div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">{{ $property->availability_label }}</div>
                        <div class="mt-4 flex items-center gap-3">
                            <a href="{{ route('ap.public.show', $property) }}" class="text-sm text-blue-600 hover:underline" data-no-nav>View page</a>
                            <button type="button" class="text-sm text-gray-700 hover:text-gray-900" data-no-nav onclick="toggleApCard('ap-card-{{ $property->id }}', this, event)">
                                <span>Expand</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
                            </button>
                        </div>
                        <div id="ap-card-{{ $property->id }}" class="hidden mt-4 border-t border-gray-200 pt-4" data-no-nav>
                            <div class="grid grid-cols-2 gap-3 text-sm text-gray-700">
                                <div class="text-gray-500">Type</div>
                                <div>{{ ($property->type ?? 'full_flat') === 'house_share' ? 'House share' : 'Full flat' }}</div>
                                @if(($property->type ?? 'full_flat') === 'house_share' && $property->room_label)
                                <div class="text-gray-500">Room</div>
                                <div>{{ $property->room_label }}</div>
                                @endif
                                <div class="text-gray-500">Area</div>
                                <div>{{ $property->area ?? '—' }}</div>
                                <div class="text-gray-500">Postcode</div>
                                <div>{{ $property->postcode ?? '—' }}</div>
                                <div class="text-gray-500">Status</div>
                                <div>{{ $property->status_label }}</div>
                            </div>
                            <div class="mt-3 grid grid-cols-3 gap-2">
                                @forelse(($property->images_url ?? []) as $url)
                                    @php($img = preg_match('/^https?:/i', $url) ? $url : Storage::url($url))
                                    <img src="{{ $img }}" class="w-full h-24 object-cover rounded" style="box-shadow:0 4px 12px rgba(0,0,0,0.08);" alt="Image">
                                @empty
                                    <div class="col-span-3 text-xs text-gray-500">No additional images.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($properties->hasPages())
            <div class="mt-8">
                {{ $properties->links() }}
            </div>
            @endif
        @endif
    </div>
<script>
function apCardClick(e, url){
  if (e && e.target && e.target.closest('[data-no-nav]')) return;
  window.location = url;
}
function toggleApCard(id, btn, e){
  if (e) e.stopPropagation();
  var el = document.getElementById(id);
  if(!el) return;
  if(el.classList.contains('hidden')){
    el.classList.remove('hidden');
    if(btn) btn.querySelector('span').textContent='Collapse';
  } else {
    el.classList.add('hidden');
    if(btn) btn.querySelector('span').textContent='Expand';
  }
}
</script>
</body>
</html>


