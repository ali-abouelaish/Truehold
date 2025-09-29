@extends('layouts.admin')

@section('title', 'Create Rental Code')

{{-- Debug info --}}
@if(config('app.debug'))
    <div class="alert alert-info">
        <strong>DEBUG:</strong> Agent users count: {{ isset($agentUsers) ? $agentUsers->count() : 'NOT SET' }}
    </div>
@endif

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-plus-circle text-primary me-2"></i>Create New Rental Code
                    </h2>
                    <p class="text-muted mb-0">Add a new rental code application to the system</p>
                </div>
                <div>
                    <a href="{{ route('rental-codes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Please review the form. The following required fields are missing or invalid:
            <ul class="mt-2 mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('rental-codes.store') }}" method="POST" id="rentalCodeForm">
        @csrf
        
        <!-- Progress Indicator -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="progress-steps">
                            <div class="step active" data-step="1">
                                <div class="step-circle">1</div>
                                <div class="step-label">Rental Info</div>
                            </div>
                            <div class="step" data-step="2">
                                <div class="step-circle">2</div>
                                <div class="step-label">Client Details</div>
                            </div>
                            <div class="step" data-step="3">
                                <div class="step-circle">3</div>
                                <div class="step-label">Agent Info</div>
                            </div>
                            <div class="step" data-step="4">
                                <div class="step-circle">4</div>
                                <div class="step-label">Review</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 1: Rental Information -->
        <div class="row step-content" id="step-1">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-key text-primary me-2"></i>Rental Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                                                 <div class="form-group mb-3">
                                     <label for="rental_code" class="form-label">
                                         Rental Code <span class="text-danger">*</span>
                                     </label>
                                     <div class="input-group">
                                         <span class="input-group-text"><i class="fas fa-key"></i></span>
                                         <input type="text" class="form-control @error('rental_code') is-invalid @enderror" 
                                                id="rental_code" name="rental_code" 
                                                value="{{ old('rental_code') }}" 
                                                placeholder="Auto-generated" readonly>
                                         <button type="button" class="btn btn-outline-secondary" id="generate-code">
                                             <i class="fas fa-sync-alt"></i> Generate
                                         </button>
                                     </div>
                                     @error('rental_code')
                                         <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                     <small class="form-text text-muted">Rental code will be auto-generated in format: CC0001, CC0002, etc.</small>
                                 </div>
                                
                                <div class="form-group mb-3">
                                    <label for="rental_date" class="form-label">
                                        Rental Date <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        <input type="date" class="form-control @error('rental_date') is-invalid @enderror" 
                                               id="rental_date" name="rental_date" 
                                               value="{{ old('rental_date') }}" required>
                                    </div>
                                    @error('rental_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="consultation_fee" class="form-label">
                                        Consultation Fee <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" step="0.01" class="form-control @error('consultation_fee') is-invalid @enderror" 
                                               id="consultation_fee" name="consultation_fee" 
                                               value="{{ old('consultation_fee') }}" 
                                               placeholder="250.00" required>
                                    </div>
                                    @error('consultation_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="payment_method" class="form-label">
                                        Payment Method <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                                                                 <select class="form-select @error('payment_method') is-invalid @enderror" 
                                                 id="payment_method" name="payment_method" required>
                                             <option value="">Select payment method</option>
                                             <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                             <option value="Transfer" {{ old('payment_method') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                                         </select>
                                    </div>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="property" class="form-label">Property</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                        <input type="text" class="form-control @error('property') is-invalid @enderror" 
                                               id="property" name="property" 
                                               value="{{ old('property') }}" 
                                               placeholder="Property address or description">
                                    </div>
                                    @error('property')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="licensor" class="form-label">Licensor</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                        <input type="text" class="form-control @error('licensor') is-invalid @enderror" 
                                               id="licensor" name="licensor" 
                                               value="{{ old('licensor') }}" 
                                               placeholder="Property owner or licensor">
                                    </div>
                                    @error('licensor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                        <i class="fas fa-arrow-left me-1"></i> Previous
                    </button>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)" style="display: block !important; visibility: visible !important; opacity: 1 !important;">
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                            <i class="fas fa-save me-1"></i> Create Rental Code
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Client Information -->
        <div class="row step-content" id="step-2" style="display: none;">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user text-primary me-2"></i>Client Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Client Selection Type -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Client Selection</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="client_selection_type" 
                                                       id="existing_client" value="existing" 
                                                       {{ old('client_selection_type', 'existing') === 'existing' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="existing_client">
                                                    <i class="fas fa-user-check text-primary me-2"></i>Select Existing Client
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="client_selection_type" 
                                                       id="new_client" value="new" 
                                                       {{ old('client_selection_type') === 'new' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="new_client">
                                                    <i class="fas fa-user-plus text-success me-2"></i>Create New Client
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Existing Client Selection -->
                        <div id="existing-client-section" class="client-section">
                            <div class="form-group mb-3">
                                <label for="existing_client_id" class="form-label">
                                    Select Client <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('existing_client_id') is-invalid @enderror" 
                                        id="existing_client_id" name="existing_client_id">
                                    <option value="">Choose a client...</option>
                                    @foreach($existingClients as $client)
                                        <option value="{{ $client->id }}" {{ old('existing_client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->full_name }} 
                                            @if($client->phone_number) - {{ $client->phone_number }}@endif
                                            @if($client->registration_status) ({{ ucfirst($client->registration_status) }})@endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('existing_client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- New Client Form -->
                        <div id="new-client-section" class="client-section" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="client_full_name" class="form-label">
                                            Full Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control @error('client_full_name') is-invalid @enderror" 
                                                   id="client_full_name" name="client_full_name" 
                                                   value="{{ old('client_full_name') }}" 
                                                   placeholder="Enter full name">
                                        </div>
                                        @error('client_full_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                
                                <div class="form-group mb-3">
                                    <label for="client_date_of_birth" class="form-label">
                                        Date of Birth <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
                                        <input type="date" class="form-control @error('client_date_of_birth') is-invalid @enderror" 
                                               id="client_date_of_birth" name="client_date_of_birth" 
                                               value="{{ old('client_date_of_birth') }}" required>
                                    </div>
                                    @error('client_date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="client_phone_number" class="form-label">
                                        Phone Number <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control @error('client_phone_number') is-invalid @enderror" 
                                               id="client_phone_number" name="client_phone_number" 
                                               value="{{ old('client_phone_number') }}" 
                                               placeholder="Enter phone number" required>
                                    </div>
                                    @error('client_phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="client_email" class="form-label">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control @error('client_email') is-invalid @enderror" 
                                               id="client_email" name="client_email" 
                                               value="{{ old('client_email') }}" 
                                               placeholder="Enter email address" required>
                                    </div>
                                    @error('client_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="client_nationality" class="form-label">
                                        Nationality <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-flag"></i></span>
                                        <input type="text" class="form-control @error('client_nationality') is-invalid @enderror" 
                                               id="client_nationality" name="client_nationality" 
                                               value="{{ old('client_nationality') }}" 
                                               placeholder="Enter nationality" required>
                                    </div>
                                    @error('client_nationality')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="client_current_address" class="form-label">
                                        Current Address <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <textarea class="form-control @error('client_current_address') is-invalid @enderror" 
                                                  id="client_current_address" name="client_current_address" 
                                                  rows="3" placeholder="Enter current address" required>{{ old('client_current_address') }}</textarea>
                                    </div>
                                    @error('client_current_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="client_company_university_name" class="form-label">Company/University Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                        <input type="text" class="form-control @error('client_company_university_name') is-invalid @enderror" 
                                               id="client_company_university_name" name="client_company_university_name" 
                                               value="{{ old('client_company_university_name') }}" 
                                               placeholder="Enter company or university name">
                                    </div>
                                    @error('client_company_university_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="client_company_university_address" class="form-label">Company/University Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <textarea class="form-control @error('client_company_university_address') is-invalid @enderror" 
                                                  id="client_company_university_address" name="client_company_university_address" 
                                                  rows="3" placeholder="Enter company or university address">{{ old('client_company_university_address') }}</textarea>
                                    </div>
                                    @error('client_company_university_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="client_position_role" class="form-label">Position/Role</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                        <input type="text" class="form-control @error('client_position_role') is-invalid @enderror" 
                                               id="client_position_role" name="client_position_role" 
                                               value="{{ old('client_position_role') }}" 
                                               placeholder="Enter position or role">
                                    </div>
                                    @error('client_position_role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Agent Information -->
        <div class="row step-content" id="step-3" style="display: none;">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-tie text-primary me-2"></i>Agent Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- DEBUG: Step 3 Content Test -->
                        <div class="alert alert-success mb-3">
                            <strong>DEBUG:</strong> Step 3 (Agent Information) is loading correctly!
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                                                 <div class="form-group mb-3">
                                     <label for="rent_by_agent" class="form-label">
                                         Rent By Agent <span class="text-danger">*</span>
                                     </label>
                                     <div class="input-group">
                                         <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                         <select class="form-select @error('rent_by_agent') is-invalid @enderror" 
        id="rent_by_agent" name="rent_by_agent" required>
    <option value="">Select agent</option>

    @if(config('app.debug'))
        <option value="" disabled>DEBUG: {{ $agentUsers->count() }} agents found</option>
    @endif

    @forelse($agentUsers as $user)
        <option value="{{ $user->id }}" 
                {{ old('rent_by_agent') == $user->id ? 'selected' : '' }}>
            {{ $user->name }}
            @if($user->agent && $user->agent->company_name)
                ({{ $user->agent->company_name }})
            @endif
        </option>
    @empty
        <option value="" disabled>No agents found</option>
    @endforelse
</select>

                                     </div>
                                     @error('rent_by_agent')
                                         <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>
                            </div>
                            
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="marketing_agent" class="form-label">
                                        Marketing Agent
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-bullhorn"></i></span>
                                        <select class="form-select @error('marketing_agent') is-invalid @enderror" 
                                                id="marketing_agent" name="marketing_agent">
                                            <option value="">Select marketing agent</option>
                                            @forelse($marketingUsers as $user)
                                                <option value="{{ $user->id }}" 
                                                        {{ old('marketing_agent') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @empty
                                                <option value="" disabled>No marketing users found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    @error('marketing_agent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="client_count" class="form-label">
                                        Number of Clients <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                                        <select class="form-select @error('client_count') is-invalid @enderror" 
                                                id="client_count" name="client_count" required>
                                            @for($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}" {{ old('client_count', 1) == $i ? 'selected' : '' }}>
                                                    {{ $i }} {{ $i == 1 ? 'Client' : 'Clients' }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    @error('client_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Multiple clients increase marketing commission to £40
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="notes" class="form-label">Additional Notes</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                  id="notes" name="notes" 
                                                  rows="4" placeholder="Enter any additional notes or comments">{{ old('notes') }}</textarea>
                                    </div>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Review and Submit -->
        <div class="row step-content" id="step-4" style="display: none;">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-check-circle text-primary me-2"></i>Review Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Rental Information</h6>
                                <div class="review-item">
                                    <strong>Rental Code:</strong> <span id="review-rental-code"></span>
                                </div>
                                <div class="review-item">
                                    <strong>Rental Date:</strong> <span id="review-rental-date"></span>
                                </div>
                                <div class="review-item">
                                    <strong>Consultation Fee:</strong> <span id="review-consultation-fee"></span>
                                </div>
                                <div class="review-item">
                                    <strong>Payment Method:</strong> <span id="review-payment-method"></span>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Client Information</h6>
                                <div class="review-item">
                                    <strong>Name:</strong> <span id="review-client-name"></span>
                                </div>
                                <div class="review-item">
                                    <strong>Email:</strong> <span id="review-client-email"></span>
                                </div>
                                <div class="review-item">
                                    <strong>Phone:</strong> <span id="review-client-phone"></span>
                                </div>
                                <div class="review-item">
                                    <strong>Nationality:</strong> <span id="review-client-nationality"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Initial Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

<style>
.progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 15px;
    left: 60%;
    width: 80%;
    height: 2px;
    background-color: #e9ecef;
    z-index: 1;
}

.step.active:not(:last-child)::after {
    background-color: #007bff;
}

.step.completed:not(:last-child)::after {
    background-color: #28a745;
}

.step-circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 8px;
    position: relative;
    z-index: 2;
}

.step.active .step-circle {
    background-color: #007bff;
    color: white;
}

.step.completed .step-circle {
    background-color: #28a745;
    color: white;
}

.step-label {
    font-size: 12px;
    color: #6c757d;
    text-align: center;
}

.step.active .step-label {
    color: #007bff;
    font-weight: 600;
}

.step.completed .step-label {
    color: #28a745;
    font-weight: 600;
}

/* Force Step 3 visibility when it's the current step */
#step-3.step-active,
#step-3[style*="display: block"] {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

#step-3.step-active .card,
#step-3[style*="display: block"] .card {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

#step-3.step-active .card-body,
#step-3[style*="display: block"] .card-body {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

#step-3.step-active .form-group,
#step-3[style*="display: block"] .form-group {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

#step-3.step-active input,
#step-3.step-active select,
#step-3.step-active textarea,
#step-3[style*="display: block"] input,
#step-3[style*="display: block"] select,
#step-3[style*="display: block"] textarea {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.review-item {
    margin-bottom: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #f8f9fa;
}

.review-item:last-child {
    border-bottom: none;
}

.form-group {
    margin-bottom: 1.5rem;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

.form-control:focus,
.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e3e6f0;
}
</style>

<script>
let currentStep = 1;
const totalSteps = 4;

// Immediate initialization to ensure buttons are visible
function initializeNavigation() {
    console.log('Initializing navigation immediately...');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    if (nextBtn) {
        nextBtn.style.display = 'block';
        console.log('Next button made visible');
    }
    
    if (prevBtn) {
        prevBtn.style.display = 'none';
    }
    
    if (submitBtn) {
        submitBtn.style.display = 'none';
    }
}

// Run immediately
initializeNavigation();

// Initialize first step as active
document.getElementById('step-1').classList.add('step-active');

// Force button visibility immediately
setTimeout(function() {
    const nextBtn = document.getElementById('nextBtn');
    if (nextBtn) {
        nextBtn.style.display = 'block';
        nextBtn.style.visibility = 'visible';
        nextBtn.style.opacity = '1';
        nextBtn.style.position = 'relative';
        nextBtn.style.zIndex = '9999';
        console.log('Forced Next button visibility with timeout');
    } else {
        console.error('Next button still not found after timeout');
    }
}, 50);

// Debug function to show all buttons
function debugButtons() {
    console.log('=== BUTTON DEBUG ===');
    const allButtons = document.querySelectorAll('button');
    console.log('Total buttons found:', allButtons.length);
    
    allButtons.forEach((btn, index) => {
        console.log(`Button ${index}:`, {
            id: btn.id,
            text: btn.textContent.trim(),
            display: btn.style.display,
            visibility: btn.style.visibility,
            className: btn.className
        });
    });
    
    const nextBtn = document.getElementById('nextBtn');
    console.log('Next button specifically:', nextBtn);
    if (nextBtn) {
        console.log('Next button styles:', {
            display: nextBtn.style.display,
            visibility: nextBtn.style.visibility,
            opacity: nextBtn.style.opacity,
            position: nextBtn.style.position,
            zIndex: nextBtn.style.zIndex
        });
    }
}

// Run debug after a short delay
setTimeout(debugButtons, 100);

// Debug step content
setTimeout(function() {
    console.log('=== STEP CONTENT DEBUG ===');
    for (let i = 1; i <= 4; i++) {
        const stepElement = document.getElementById(`step-${i}`);
        console.log(`Step ${i}:`, stepElement);
        if (stepElement) {
            console.log(`Step ${i} content:`, stepElement.innerHTML.substring(0, 100) + '...');
        }
    }
}, 200);

function changeStep(direction) {
    const currentStepElement = document.getElementById(`step-${currentStep}`);
    const nextStep = currentStep + direction;
    
    console.log('Changing step:', { currentStep, nextStep, direction });
    
    if (nextStep < 1 || nextStep > totalSteps) return;
    
    // Hide current step
    currentStepElement.style.display = 'none';
    currentStepElement.classList.remove('step-active');
    
    // Update step indicators
    document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');
    if (direction > 0) {
        document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('completed');
    }
    
    currentStep = nextStep;
    
    // Show next step
    const nextStepElement = document.getElementById(`step-${currentStep}`);
    console.log('Next step element:', nextStepElement);
    
    if (nextStepElement) {
        nextStepElement.style.display = 'block';
        nextStepElement.classList.add('step-active');
        console.log('Step', currentStep, 'displayed');
        
        // Special debugging for step 3
        if (currentStep === 3) {
            console.log('=== STEP 3 DEBUG ===');
            console.log('Step 3 element:', nextStepElement);
            console.log('Step 3 classes:', nextStepElement.className);
            console.log('Step 3 style display:', nextStepElement.style.display);
            console.log('Step 3 innerHTML length:', nextStepElement.innerHTML.length);
            console.log('Step 3 children:', nextStepElement.children.length);
            console.log('Step 3 first child:', nextStepElement.children[0]);
            
            // Check for form fields
            const formFields = nextStepElement.querySelectorAll('input, select, textarea');
            console.log('Step 3 form fields found:', formFields.length);
            formFields.forEach((field, index) => {
                console.log(`Field ${index}:`, field.name, field.type, field.placeholder);
            });
            
            // Force visibility of all elements in step 3
            const allElements = nextStepElement.querySelectorAll('*');
            console.log('Total elements in Step 3:', allElements.length);
            
            allElements.forEach((element, index) => {
                if (element.style.display === 'none' || element.style.visibility === 'hidden') {
                    console.log(`Hidden element ${index}:`, element);
                    element.style.display = 'block';
                    element.style.visibility = 'visible';
                    element.style.opacity = '1';
                }
            });
            
            // Force card visibility
            const card = nextStepElement.querySelector('.card');
            if (card) {
                card.style.display = 'block';
                card.style.visibility = 'visible';
                card.style.opacity = '1';
                console.log('Card visibility forced');
            }
            
            // Force card body visibility
            const cardBody = nextStepElement.querySelector('.card-body');
            if (cardBody) {
                cardBody.style.display = 'block';
                cardBody.style.visibility = 'visible';
                cardBody.style.opacity = '1';
                console.log('Card body visibility forced');
            }
            
            // Check if step-active class is applied
            console.log('Step 3 has step-active class:', nextStepElement.classList.contains('step-active'));
        }
    } else {
        console.error('Step element not found:', `step-${currentStep}`);
    }
    
    document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');
    
    // Update navigation buttons
    updateNavigationButtons();
    
    // Update review section if on last step
    if (currentStep === totalSteps) {
        updateReviewSection();
    }
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    console.log('Updating navigation buttons...');
    console.log('Current step:', currentStep);
    console.log('Total steps:', totalSteps);
    console.log('Prev button:', prevBtn);
    console.log('Next button:', nextBtn);
    console.log('Submit button:', submitBtn);
    
    if (prevBtn) {
        prevBtn.style.display = currentStep > 1 ? 'block' : 'none';
        console.log('Prev button display:', prevBtn.style.display);
    } else {
        console.error('Previous button not found!');
    }
    
    if (nextBtn) {
        nextBtn.style.display = currentStep < totalSteps ? 'block' : 'none';
        console.log('Next button display:', nextBtn.style.display);
    } else {
        console.error('Next button not found!');
    }
    
    if (submitBtn) {
        submitBtn.style.display = currentStep === totalSteps ? 'block' : 'none';
        console.log('Submit button display:', submitBtn.style.display);
    } else {
        console.error('Submit button not found!');
    }
    
    // Force visibility if buttons exist but are hidden
    if (nextBtn && currentStep < totalSteps) {
        nextBtn.style.display = 'block';
        nextBtn.style.visibility = 'visible';
        console.log('Forced next button visibility');
    }
}

function updateReviewSection() {
    document.getElementById('review-rental-code').textContent = document.getElementById('rental_code').value;
    document.getElementById('review-rental-date').textContent = document.getElementById('rental_date').value;
    document.getElementById('review-consultation-fee').textContent = '£' + document.getElementById('consultation_fee').value;
    document.getElementById('review-payment-method').textContent = document.getElementById('payment_method').value;
    document.getElementById('review-client-name').textContent = document.getElementById('client_full_name').value;
    document.getElementById('review-client-email').textContent = document.getElementById('client_email').value;
    document.getElementById('review-client-phone').textContent = document.getElementById('client_phone_number').value;
    document.getElementById('review-client-nationality').textContent = document.getElementById('client_nationality').value;
}

// Form validation
function validateStep(step) {
    const requiredFields = {
        1: ['rental_code', 'rental_date', 'consultation_fee', 'payment_method'],
        2: ['client_full_name', 'client_date_of_birth', 'client_phone_number', 'client_email', 'client_nationality', 'client_current_address'],
        3: ['rent_by_agent']
    };
    
    const fields = requiredFields[step] || [];
    let isValid = true;
    
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Override next button click to validate
document.getElementById('nextBtn').addEventListener('click', function(e) {
    if (!validateStep(currentStep)) {
        e.preventDefault();
        return false;
    }
});

// Real-time validation
document.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
    field.addEventListener('blur', function() {
        if (this.value.trim()) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        }
    });
});

// Auto-generate rental code
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing navigation...');
    console.log('Current step:', currentStep);
    console.log('Total steps:', totalSteps);
    
    // Initialize navigation buttons
    updateNavigationButtons();
    
    // Debug navigation buttons
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    console.log('Next button found:', nextBtn);
    console.log('Prev button found:', prevBtn);
    console.log('Submit button found:', submitBtn);
    
    // Generate rental code button
    const generateBtn = document.getElementById('generate-code');
    const rentalCodeInput = document.getElementById('rental_code');
    
    if (generateBtn && rentalCodeInput) {
        generateBtn.addEventListener('click', function() {
            console.log('Generate button clicked');
            const url = '/test-rental-code';
            console.log('Fetching URL:', url);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    rentalCodeInput.value = data.code;
                    updateReviewSection();
                })
                .catch(error => {
                    console.error('Error generating rental code:', error);
                    console.error('Error details:', error.message);
                    alert('Error generating rental code: ' + error.message);
                });
        });
        
        // Auto-generate on page load
        if (!rentalCodeInput.value) {
            generateBtn.click();
        }
    }
    
    // Client selection toggle
    const existingClientRadio = document.getElementById('existing_client');
    const newClientRadio = document.getElementById('new_client');
    const existingClientSection = document.getElementById('existing-client-section');
    const newClientSection = document.getElementById('new-client-section');
    
    function toggleClientSections() {
        if (existingClientRadio.checked) {
            existingClientSection.style.display = 'block';
            newClientSection.style.display = 'none';
        } else if (newClientRadio.checked) {
            existingClientSection.style.display = 'none';
            newClientSection.style.display = 'block';
        }
    }
    
    existingClientRadio.addEventListener('change', toggleClientSections);
    newClientRadio.addEventListener('change', toggleClientSections);
    
    // Initialize on page load
    toggleClientSections();
    
    // Ensure navigation buttons are properly initialized
    setTimeout(function() {
        console.log('Delayed navigation initialization...');
        updateNavigationButtons();
    }, 100);
});
</script>
@endsection