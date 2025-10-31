@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.ap-properties.index') }}">AP Properties</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit AP Property</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <div class="fw-bold mb-1">Please fix the following:</div>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('admin.ap-properties.update', $property) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Property Name</label>
                                <input type="text" name="property_name" class="form-control" value="{{ old('property_name', $property->property_name) }}" required>
                                @error('property_name')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Area</label>
                                <input type="text" name="area" class="form-control" value="{{ old('area', $property->area) }}">
                                @error('area')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Postcode</label>
                                <input type="text" name="postcode" class="form-control" value="{{ old('postcode', $property->postcode) }}">
                                @error('postcode')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">PCM (£)</label>
                                <input type="number" name="pcm" class="form-control" value="{{ old('pcm', $property->pcm) }}" min="0">
                                @error('pcm')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Rooms</label>
                                <input type="number" name="n_rooms" class="form-control" value="{{ old('n_rooms', $property->n_rooms) }}" min="0">
                                @error('n_rooms')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Bathrooms</label>
                                <input type="number" name="n_bathrooms" class="form-control" value="{{ old('n_bathrooms', $property->n_bathrooms) }}" min="0">
                                @error('n_bathrooms')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Availability</label>
                                <input type="date" name="availability" class="form-control" value="{{ old('availability', optional($property->availability)->format('Y-m-d')) }}">
                                @error('availability')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                @php($currentStatus = old('status', $property->status ?? 'empty_available_now'))
                                <select name="status" class="form-select" required>
                                    <option value="empty_available_now" {{ $currentStatus==='empty_available_now' ? 'selected' : '' }}>AVAILABLE NOW</option>
                                    <option value="available_on_date" {{ $currentStatus==='available_on_date' ? 'selected' : '' }}>Available on DATE</option>
                                   
                                    <option value="booked" {{ $currentStatus==='booked' ? 'selected' : '' }}>Booked</option>
                                    <option value="renewal" {{ $currentStatus==='renewal' ? 'selected' : '' }}>Renewal</option>
                                </select>
                               
                                @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Type</label>
                                @php($currentType = old('type', $property->type ?? 'full_flat'))
                                <select name="type" class="form-select" required>
                                    <option value="full_flat" {{ $currentType==='full_flat' ? 'selected' : '' }}>Full flat</option>
                                    <option value="house_share" {{ $currentType==='house_share' ? 'selected' : '' }}>House share</option>
                                </select>
                                @error('type')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Room identifier (e.g., Room A, Room 2)</label>
                                <input type="text" name="room_label" class="form-control" value="{{ old('room_label', $property->room_label) }}" placeholder="Optional unless house share or single room">
                                @error('room_label')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Upload New Images (optional)</label>
                                <input type="file" name="images[]" class="form-control" multiple accept="image/*,.heic,.heif">
                                <div class="form-text">If you upload new images, they will replace the current set.</div>
                                @error('images.*')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 form-check mt-2">
                                <input type="checkbox" id="is_room" name="is_room" class="form-check-input" {{ old('is_room', $property->is_room) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_room">This listing is a single room</label>
                            </div>

                            <div class="col-md-6 form-check mt-2">
                                <input type="checkbox" id="couples_allowed" name="couples_allowed" class="form-check-input" {{ old('couples_allowed', $property->couples_allowed) ? 'checked' : '' }}>
                                <label class="form-check-label" for="couples_allowed">Couples allowed</label>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Current Images</label>
                                <div class="row g-2">
                                    @php($existing = $property->images_url ?? [])
                                    @forelse($existing as $i => $img)
                                        @php($src = preg_match('/^https?:/i', $img) ? $img : Storage::url($img))
                                        <div class="col-md-3">
                                            <div class="position-relative">
                                                <img src="{{ $src }}" class="img-fluid rounded border" alt="Property image">
                                                <form action="{{ route('admin.ap-properties.images.destroy', ['ap_property' => $property->id, 'index' => $i]) }}" method="POST" onsubmit="return confirm('Delete this image?');" class="mt-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-muted">No images uploaded.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <a href="{{ route('admin.ap-properties.index') }}" class="btn btn-light">Back</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function(){
  function loadHeicLib(cb){
    if (window.heic2any) return cb();
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js';
    s.onload = cb;
    s.onerror = function(){
      var s2 = document.createElement('script');
      s2.src = 'https://unpkg.com/heic2any@0.0.4/dist/heic2any.min.js';
      s2.onload = cb;
      document.head.appendChild(s2);
    };
    document.head.appendChild(s);
  }
  const input = document.querySelector('input[name="images[]"]');
  if (!input) return;
  input.addEventListener('change', function(){
    const files = Array.from(input.files || []);
    const needsConvert = files.some(function(f){
      var ext = (f.name.split('.').pop() || '').toLowerCase();
      return /heic|heif/.test((f.type || '').toLowerCase()) || ext === 'heic' || ext === 'heif';
    });
    if (!needsConvert) return;
    loadHeicLib(async function(){
      if (!window.heic2any) return;
      var notice = document.createElement('div');
      notice.className = 'text-muted small mt-2';
      notice.textContent = 'Converting HEIC images to JPEG…';
      input.parentElement.appendChild(notice);
      const dt = new DataTransfer();
      for (const file of files) {
        try {
          var ext = (file.name.split('.').pop() || '').toLowerCase();
          var isHeic = /heic|heif/.test((file.type || '').toLowerCase()) || ext === 'heic' || ext === 'heif';
          if (isHeic) {
            let out = await window.heic2any({ blob: file, toType: 'image/jpeg', quality: 0.88 });
            if (Array.isArray(out)) out = out[0];
            const converted = new File([out], file.name.replace(/\.(heic|heif)$/i, '.jpg'), { type: 'image/jpeg' });
            dt.items.add(converted);
          } else {
            dt.items.add(file);
          }
        } catch(e) {
          dt.items.add(file);
        }
      }
      input.files = dt.files;
      if (notice && notice.remove) notice.remove();
    });
  });
})();
</script>
@endsection


