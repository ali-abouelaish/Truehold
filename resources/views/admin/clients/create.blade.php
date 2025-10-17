@extends('layouts.admin')

@section('title', 'Create Client')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Client</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.clients.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="full_name">Full Name *</label>
                                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                           id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="date_of_birth">Date of Birth *</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="phone_number">Phone Number *</label>
                                    <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="email">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="nationality">Nationality *</label>
                                    <input type="text" class="form-control @error('nationality') is-invalid @enderror" 
                                           id="nationality" name="nationality" value="{{ old('nationality') }}" required>
                                    @error('nationality')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="current_landlord_name">Current Landlord/Agency Name *</label>
                                    <input type="text" class="form-control @error('current_landlord_name') is-invalid @enderror" 
                                           id="current_landlord_name" name="current_landlord_name" value="{{ old('current_landlord_name') }}" required
                                           placeholder="Enter landlord or agency name">
                                    <small class="form-text text-muted">Name of current landlord or letting agency</small>
                                    @error('current_landlord_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="current_landlord_contact_info">Current Landlord/Agency Contact Info *</label>
                                    <textarea class="form-control @error('current_landlord_contact_info') is-invalid @enderror" 
                                              id="current_landlord_contact_info" name="current_landlord_contact_info" rows="3" 
                                              placeholder="Phone, email, address, or other contact details..." required>{{ old('current_landlord_contact_info') }}</textarea>
                                    <small class="form-text text-muted">Contact details of current landlord or letting agency</small>
                                    @error('current_landlord_contact_info')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="current_address">Current Address *</label>
                                    <textarea class="form-control @error('current_address') is-invalid @enderror" 
                                              id="current_address" name="current_address" rows="3" required>{{ old('current_address') }}</textarea>
                                    @error('current_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="company_university_name">Company/University Name *</label>
                                    <input type="text" class="form-control @error('company_university_name') is-invalid @enderror" 
                                           id="company_university_name" name="company_university_name" value="{{ old('company_university_name') }}" required>
                                    @error('company_university_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="company_university_address">Company/University Address *</label>
                                    <textarea class="form-control @error('company_university_address') is-invalid @enderror" 
                                              id="company_university_address" name="company_university_address" rows="3" required>{{ old('company_university_address') }}</textarea>
                                    @error('company_university_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="position_role">Position/Role *</label>
                                    <input type="text" class="form-control @error('position_role') is-invalid @enderror" 
                                           id="position_role" name="position_role" value="{{ old('position_role') }}" required>
                                    @error('position_role')
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
                                    <label for="budget">Budget (£) *</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('budget') is-invalid @enderror" 
                                           id="budget" name="budget" value="{{ old('budget') }}" placeholder="e.g., 1500.00" required>
                                    <small class="form-text text-muted">Monthly rent budget</small>
                                    @error('budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="area_of_interest">Area of Interest *</label>
                                    <input type="text" class="form-control @error('area_of_interest') is-invalid @enderror" 
                                           id="area_of_interest" name="area_of_interest" value="{{ old('area_of_interest') }}" 
                                           placeholder="e.g., Central London, Camden, Shoreditch" required>
                                    <small class="form-text text-muted">Preferred areas to live</small>
                                    @error('area_of_interest')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="moving_date">Moving Date *</label>
                                    <input type="date" class="form-control @error('moving_date') is-invalid @enderror" 
                                           id="moving_date" name="moving_date" value="{{ old('moving_date') }}" required>
                                    <small class="form-text text-muted">When they need to move by</small>
                                    @error('moving_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="4" placeholder="Any additional notes about the client...">{{ old('notes') }}</textarea>
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
                                        <option value="unregistered" {{ old('registration_status', 'unregistered') == 'unregistered' ? 'selected' : '' }}>
                                            Unregistered
                                        </option>
                                        <option value="registered" {{ old('registration_status') == 'registered' ? 'selected' : '' }}>
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
                        
                        <!-- Agent Assignment Section -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="agent_user_id">Assigned Agent</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                        <input type="text" class="form-control" 
                                               id="agent_display" 
                                               value="{{ auth()->user()->agent ? auth()->user()->agent->clean_display_name : auth()->user()->name }}" 
                                               readonly>
                                        <input type="hidden" name="agent_user_id" 
                                               value="{{ auth()->user()->id }}">
                                    </div>
                                    @error('agent_user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="marketing_agent_id">Marketing Agent</label>
                                    <select class="form-control @error('marketing_agent_id') is-invalid @enderror" 
                                            id="marketing_agent_id" name="marketing_agent_id">
                                        <option value="">Select a marketing agent</option>
                                        @foreach($marketingUsers as $user)
                                            <option value="{{ $user->id }}" {{ old('marketing_agent_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Optional: Assign a marketing agent to this client</small>
                                    @error('marketing_agent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create Client</button>
                            <a href="{{ route('admin.clients') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
