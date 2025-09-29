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
        

        <!-- Rental Information -->
        <div class="row mb-4">
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


        <!-- Client Information -->
        <div class="row mb-4">
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

        <!-- Agent Information -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-tie text-primary me-2"></i>Agent Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rent_by_agent" class="form-label">Rent By Agent *</label>
                                         <select class="form-select @error('rent_by_agent') is-invalid @enderror" 
        id="rent_by_agent" name="rent_by_agent" required>
    <option value="">Select agent</option>
    @forelse($agentUsers as $user)
                                        <option value="{{ $user->id }}" {{ old('rent_by_agent') == $user->id ? 'selected' : '' }}>
            {{ $user->name }}
            @if($user->agent && $user->agent->company_name)
                ({{ $user->agent->company_name }})
            @endif
        </option>
    @empty
        <option value="" disabled>No agents found</option>
    @endforelse
</select>
                                     @error('rent_by_agent')
                                         <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>
                            </div>
                            <div class="col-md-6">
                            <div class="mb-3">
                                <label for="marketing_agent" class="form-label">Marketing Agent</label>
                                        <select class="form-select @error('marketing_agent') is-invalid @enderror" 
                                                id="marketing_agent" name="marketing_agent">
                                            <option value="">Select marketing agent</option>
                                            @forelse($marketingUsers as $user)
                                        <option value="{{ $user->id }}" {{ old('marketing_agent') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @empty
                                                <option value="" disabled>No marketing users found</option>
                                            @endforelse
                                        </select>
                                    @error('marketing_agent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                    </div>
                    <div class="row">
                            <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_count" class="form-label">Number of Clients *</label>
                                        <select class="form-select @error('client_count') is-invalid @enderror" 
                                                id="client_count" name="client_count" required>
                                            @for($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}" {{ old('client_count', 1) == $i ? 'selected' : '' }}>
                                                    {{ $i }} {{ $i == 1 ? 'Client' : 'Clients' }}
                                                </option>
                                            @endfor
                                        </select>
                                    @error('client_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Multiple clients increase marketing commission to £40
                                    </small>
                                </div>
                            </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                    <label for="notes" class="form-label">Additional Notes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" placeholder="Enter any additional notes">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status and Submit -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-check-circle text-primary me-2"></i>Status & Submit
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
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
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save me-2"></i>Create Rental Code
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

<style>
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
// Client selection toggle
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Initialize
    toggleClientSections();
    
    // Generate rental code button
    const generateBtn = document.getElementById('generate-code');
    const rentalCodeInput = document.getElementById('rental_code');
    
    if (generateBtn && rentalCodeInput) {
        generateBtn.addEventListener('click', function() {
            fetch('/test-rental-code', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
                .then(data => {
                if (data.code) {
                    rentalCodeInput.value = data.code;
                }
                })
                .catch(error => {
                    console.error('Error generating rental code:', error);
                });
        });
    }
});
</script>
@endsection