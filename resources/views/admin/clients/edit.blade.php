@extends('layouts.admin')

@section('title', 'Edit Client')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Client: {{ $client->full_name }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.clients.update', $client) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="full_name">Full Name *</label>
                                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                           id="full_name" name="full_name" value="{{ old('full_name', $client->full_name) }}" required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $client->date_of_birth) }}">
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" name="phone_number" value="{{ old('phone_number', $client->phone_number) }}">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $client->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="nationality">Nationality</label>
                                    <input type="text" class="form-control @error('nationality') is-invalid @enderror" 
                                           id="nationality" name="nationality" value="{{ old('nationality', $client->nationality) }}">
                                    @error('nationality')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="current_address">Current Address</label>
                                    <textarea class="form-control @error('current_address') is-invalid @enderror" 
                                              id="current_address" name="current_address" rows="3">{{ old('current_address', $client->current_address) }}</textarea>
                                    @error('current_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="company_university_name">Company/University Name</label>
                                    <input type="text" class="form-control @error('company_university_name') is-invalid @enderror" 
                                           id="company_university_name" name="company_university_name" value="{{ old('company_university_name', $client->company_university_name) }}">
                                    @error('company_university_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="company_university_address">Company/University Address</label>
                                    <textarea class="form-control @error('company_university_address') is-invalid @enderror" 
                                              id="company_university_address" name="company_university_address" rows="3">{{ old('company_university_address', $client->company_university_address) }}</textarea>
                                    @error('company_university_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="position_role">Position/Role</label>
                                    <input type="text" class="form-control @error('position_role') is-invalid @enderror" 
                                           id="position_role" name="position_role" value="{{ old('position_role', $client->position_role) }}">
                                    @error('position_role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="agent_user_id">Assigned Agent</label>
                                    <select class="form-control @error('agent_user_id') is-invalid @enderror" 
                                            id="agent_user_id" name="agent_user_id">
                                        <option value="">Select an agent</option>
                                        @foreach($agentUsers as $user)
                                            <option value="{{ $user->id }}" {{ old('agent_user_id', optional($client->agent)->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}@if($user->agent && $user->agent->company_name) ({{ $user->agent->company_name }})@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('agent_user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Client Details Section -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle mr-2"></i>Client Requirements & Preferences
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="budget">Budget (£)</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('budget') is-invalid @enderror" 
                                           id="budget" name="budget" value="{{ old('budget', $client->budget) }}" placeholder="e.g., 1500.00">
                                    <small class="form-text text-muted">Monthly rent budget</small>
                                    @error('budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="area_of_interest">Area of Interest</label>
                                    <input type="text" class="form-control @error('area_of_interest') is-invalid @enderror" 
                                           id="area_of_interest" name="area_of_interest" value="{{ old('area_of_interest', $client->area_of_interest) }}" 
                                           placeholder="e.g., Central London, Camden, Shoreditch">
                                    <small class="form-text text-muted">Preferred areas to live</small>
                                    @error('area_of_interest')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="moving_date">Moving Date</label>
                                    <input type="date" class="form-control @error('moving_date') is-invalid @enderror" 
                                           id="moving_date" name="moving_date" value="{{ old('moving_date', $client->moving_date) }}">
                                    <small class="form-text text-muted">When they need to move by</small>
                                    @error('moving_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="4" placeholder="Any additional notes about the client...">{{ old('notes', $client->notes) }}</textarea>
                                    <small class="form-text text-muted">Special requirements, preferences, or important notes</small>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Registration Status Section -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user-check mr-2"></i>Registration Status
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="registration_status">Registration Status</label>
                                    <select class="form-control @error('registration_status') is-invalid @enderror" 
                                            id="registration_status" name="registration_status">
                                        <option value="unregistered" {{ old('registration_status', $client->registration_status) == 'unregistered' ? 'selected' : '' }}>
                                            Unregistered
                                        </option>
                                        <option value="registered" {{ old('registration_status', $client->registration_status) == 'registered' ? 'selected' : '' }}>
                                            Registered
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">Client registration status</small>
                                    @error('registration_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Client</button>
                            <a href="{{ route('admin.clients') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
