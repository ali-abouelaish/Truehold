@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">AP Properties</li>
                    </ol>
                </div>
                <h4 class="page-title">AP Properties (Full Flats)</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">All AP Properties</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.ap-properties.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add AP Property
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Area</th>
                                    <th>Postcode</th>
                                    <th>PCM</th>
                                     <th>Rooms</th>
                                     <th>Bathrooms</th>
                                     <th>Tags</th>
                                    <th>Status</th>
                                    <th>Availability</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($properties as $property)
                                 <tr>
                                     <td>
                                         <a href="{{ route('admin.ap-properties.show', $property) }}" class="fw-bold">{{ $property->property_name }}</a>
                                         @if(($property->type ?? 'full_flat') === 'house_share' && $property->room_label)
                                             <div class="text-muted small">Room: {{ $property->room_label }}</div>
                                         @endif
                                     </td>
                                    <td>{{ $property->area ?? '—' }}</td>
                                    <td>{{ $property->postcode ?? '—' }}</td>
                                    <td>£{{ number_format((int)($property->pcm ?? 0)) }}</td>
                                    <td>{{ $property->n_rooms }}</td>
                                     <td>{{ $property->n_bathrooms }}</td>
                                    <td>
                                        @if(($property->type ?? 'full_flat') === 'house_share')
                                            <span class="badge bg-info me-1">House share</span>
                                        @endif
                                         @if($property->is_room)
                                             <span class="badge bg-primary me-1">Room</span>
                                         @endif
                                         @if($property->couples_allowed)
                                             <span class="badge bg-success me-1">Couples</span>
                                         @endif
                                     </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $property->status_label }}</span>
                                    </td>
                                    <td>{{ $property->availability_label }}</td>
                                    <td>
                                        <div class="btn-group">
                                             <button class="btn btn-sm btn-outline-primary" type="button" onclick="toggleDetails('property-{{ $property->id }}-details', this)">
                                                 <i class="fas fa-chevron-down"></i>
                                             </button>
                                            <a href="{{ route('admin.ap-properties.edit', $property) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.ap-properties.destroy', $property) }}" method="POST" onsubmit="return confirm('Delete this property?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                 </tr>
                                 <!-- Expandable details row -->
                                <tr class="d-none" id="property-{{ $property->id }}-details">
                                     <td colspan="10">
                                         <div class="p-3 border rounded ap-details" style="background: linear-gradient(180deg,#ffffff 0%,#fbfbfb 100%); border-color: #e5e7eb; color: #111827; border-left: 4px solid #fbbf24; box-shadow: 0 6px 16px rgba(0,0,0,0.06);">
                                             <div class="row g-3">
                                                 <div class="col-md-6">
                                                     <h6 class="mb-3" style="color: #111827; font-weight: 700;">Details</h6>
                                                     <div class="row mb-2">
                                                         <div class="col-5 text-muted small">Type</div>
                                                         <div class="col-7">{{ ($property->type ?? 'full_flat') === 'house_share' ? 'House share' : 'Full flat' }}</div>
                                                     </div>
                                                     @if(($property->type ?? 'full_flat') === 'house_share' && $property->room_label)
                                                     <div class="row mb-2">
                                                         <div class="col-5 text-muted small">Room</div>
                                                         <div class="col-7">{{ $property->room_label }}</div>
                                                     </div>
                                                     @endif
                                                     <div class="row mb-2">
                                                         <div class="col-5 text-muted small">Area</div>
                                                         <div class="col-7">{{ $property->area ?? '—' }}</div>
                                                     </div>
                                                     <div class="row mb-2">
                                                         <div class="col-5 text-muted small">Postcode</div>
                                                         <div class="col-7">{{ $property->postcode ?? '—' }}</div>
                                                     </div>
                                                     <div class="row mb-2">
                                                         <div class="col-5 text-muted small">PCM</div>
                                                         <div class="col-7">£{{ number_format((int)($property->pcm ?? 0)) }}</div>
                                                     </div>
                                                     <div class="row mb-2">
                                                         <div class="col-5 text-muted small">Rooms / Bathrooms</div>
                                                         <div class="col-7">{{ $property->n_rooms }} / {{ $property->n_bathrooms }}</div>
                                                     </div>
                                                     <div class="row mb-2">
                                                         <div class="col-5 text-muted small">Status</div>
                                                         <div class="col-7">{{ $property->status_label }}</div>
                                                     </div>
                                                     <div class="row">
                                                         <div class="col-5 text-muted small">Availability</div>
                                                         <div class="col-7">{{ $property->availability_label }}</div>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-6">
                                                     <h6 class="mb-3" style="color: #111827; font-weight: 700;">Images</h6>
                                                     <div class="row g-2">
                                                         @forelse(($property->images_url ?? []) as $url)
                                                             @php($src = preg_match('/^https?:/i', $url) ? $url : Storage::url($url))
                                                             <div class="col-6 col-md-4">
                                                                 <img src="{{ $src }}" class="img-fluid rounded border" style="border-color:#e5e7eb; box-shadow: 0 4px 10px rgba(0,0,0,0.08);" alt="Image" />
                                                             </div>
                                                         @empty
                                                             <div class="text-muted small">No images uploaded.</div>
                                                         @endforelse
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </td>
                                 </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-building fa-3x mb-3"></i>
                                            <h5>No AP Properties Found</h5>
                                            <p>Start by adding your first AP property.</p>
                                            <a href="{{ route('admin.ap-properties.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i> Add AP Property
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($properties->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $properties->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
 </div>
<style>
.ap-details .text-muted { color: #6b7280 !important; }
.ap-details .badge { background-color: #f9fafb; color: #111827; border: 1px solid #e5e7eb; }
</style>
<script>
function toggleDetails(id, btn) {
    var row = document.getElementById(id);
    if (!row) return;
    if (row.classList.contains('d-none')) {
        row.classList.remove('d-none');
        var icon = btn && btn.querySelector('i');
        if (icon) { icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); }
    } else {
        row.classList.add('d-none');
        var icon = btn && btn.querySelector('i');
        if (icon) { icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down'); }
    }
}
</script>
@endsection


