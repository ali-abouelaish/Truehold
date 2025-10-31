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
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">Create AP Property</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.ap-properties.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Property Name</label>
                                <input type="text" name="property_name" class="form-control" value="{{ old('property_name') }}" required>
                                @error('property_name')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Area</label>
                                <input type="text" name="area" class="form-control" value="{{ old('area') }}">
                                @error('area')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Postcode</label>
                                <input type="text" name="postcode" class="form-control" value="{{ old('postcode') }}">
                                @error('postcode')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">PCM (£)</label>
                                <input type="number" name="pcm" class="form-control" value="{{ old('pcm') }}" min="0">
                                @error('pcm')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Rooms</label>
                                <input type="number" name="n_rooms" class="form-control" value="{{ old('n_rooms', 0) }}" min="0">
                                @error('n_rooms')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Bathrooms</label>
                                <input type="number" name="n_bathrooms" class="form-control" value="{{ old('n_bathrooms', 0) }}" min="0">
                                @error('n_bathrooms')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Availability</label>
                                <input type="date" name="availability" class="form-control" value="{{ old('availability') }}">
                                @error('availability')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="empty_available_now" {{ old('status')==='empty_available_now' ? 'selected' : '' }}>EMPTY AVAILABLE NOW</option>
                                    <option value="available_on_date" {{ old('status')==='available_on_date' ? 'selected' : '' }}>Available on DATE</option>
                                    <option value="booked" {{ old('status')==='booked' ? 'selected' : '' }}>Booked</option>
                                    <option value="renewal" {{ old('status')==='renewal' ? 'selected' : '' }}>Renewal</option>
                                </select>
                                <div class="form-text">If "Available on DATE" is chosen, set the Availability date above.</div>
                                @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Upload Images</label>
                                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                                @error('images.*')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 form-check mt-2">
                                <input type="checkbox" id="is_house_share" name="is_house_share" class="form-check-input" {{ old('is_house_share') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_house_share">This is a house share</label>
                                <div class="form-text">For house shares, you can create multiple entries with the same name but different price and images.</div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <a href="{{ route('admin.ap-properties.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Property</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


