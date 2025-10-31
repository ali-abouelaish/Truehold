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
                                        @if($property->is_house_share)
                                            <span class="badge bg-info ms-2">House share</span>
                                        @endif
                                    </td>
                                    <td>{{ $property->area ?? '—' }}</td>
                                    <td>{{ $property->postcode ?? '—' }}</td>
                                    <td>£{{ number_format((int)($property->pcm ?? 0)) }}</td>
                                    <td>{{ $property->n_rooms }}</td>
                                    <td>{{ $property->n_bathrooms }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $property->status_label }}</span>
                                    </td>
                                    <td>{{ $property->availability_label }}</td>
                                    <td>
                                        <div class="btn-group">
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
@endsection


