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
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $property->property_name }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex gap-2">
                        <a href="{{ route('admin.ap-properties.edit', $property) }}" class="btn btn-secondary"><i class="fas fa-edit me-1"></i>Edit</a>
                        <form action="{{ route('admin.ap-properties.destroy', $property) }}" method="POST" onsubmit="return confirm('Delete this property?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Delete</button>
                        </form>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 border rounded">
                                <h5 class="mb-3">Summary</h5>
                                <p class="mb-1"><strong>Area:</strong> {{ $property->area ?? '—' }}</p>
                                <p class="mb-1"><strong>Postcode:</strong> {{ $property->postcode ?? '—' }}</p>
                                <p class="mb-1"><strong>PCM:</strong> £{{ number_format((int)($property->pcm ?? 0)) }}</p>
                                <p class="mb-1"><strong>Type:</strong> {{ ($property->type ?? 'full_flat') === 'house_share' ? 'House share' : 'Full flat' }}</p>
                                @if(($property->type ?? 'full_flat') === 'house_share' && $property->room_label)
                                <p class="mb-1"><strong>Room:</strong> {{ $property->room_label }}</p>
                                @endif
                                <p class="mb-1"><strong>Rooms:</strong> {{ $property->n_rooms }}</p>
                                <p class="mb-1"><strong>Bathrooms:</strong> {{ $property->n_bathrooms }}</p>
                                <p class="mb-1"><strong>Room:</strong> {{ $property->is_room ? 'Yes' : 'No' }}</p>
                                <p class="mb-1"><strong>Couples Allowed:</strong> {{ $property->couples_allowed ? 'Yes' : 'No' }}</p>
                                <p class="mb-1"><strong>Status:</strong> {{ $property->status_label }}</p>
                                <p class="mb-0"><strong>Availability:</strong> {{ $property->availability_label }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded">
                                <h5 class="mb-3">Images</h5>
                                <div class="row g-2">
                                    @forelse(($property->images_url ?? []) as $url)
                                    @php($src = preg_match('/^https?:/i', $url) ? $url : Storage::url($url))
                                    <div class="col-6">
                                        <img src="{{ $src }}" class="img-fluid rounded" alt="Property image"/>
                                    </div>
                                    @empty
                                    <div class="text-muted">No images added.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


