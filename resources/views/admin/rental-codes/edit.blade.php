@extends('layouts.admin')

@section('title', 'Edit Rental Code')

<style>
/* Mobile responsive improvements for rental codes */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .input-group-text {
        font-size: 0.875rem;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .btn-group .btn {
        width: auto;
        margin-bottom: 0;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .nav-tabs .nav-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding: 0.5rem;
    }
    
    .card {
        margin-bottom: 1rem;
    }
    
    .h3 {
        font-size: 1.25rem;
    }
    
    .input-group {
        flex-wrap: wrap;
    }
    
    .input-group-text {
        min-width: 2.5rem;
    }
}
</style>

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h2 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-edit text-warning me-2"></i>Edit Rental Code
                    </h2>
                    <p class="text-muted mb-0">Update rental code: <strong>{{ $rentalCode->rental_code }}</strong></p>
                </div>
                <div class="btn-group d-flex flex-column flex-md-row">
                    <a href="{{ route('rental-codes.show', $rentalCode) }}" class="btn transition-colors"
                       style="background: linear-gradient(135deg, #1e40af, #3b82f6); border: 1px solid #3b82f6; color: #ffffff; text-decoration: none;"
                       onmouseover="this.style.background='linear-gradient(135deg, #3b82f6, #2563eb)'; this.style.borderColor='#2563eb';"
                       onmouseout="this.style.background='linear-gradient(135deg, #1e40af, #3b82f6)'; this.style.borderColor='#3b82f6';">
                        <i class="fas fa-eye me-1"></i> View Details
                    </a>
                    <a href="{{ route('rental-codes.index') }}" class="btn transition-colors"
                       style="background: linear-gradient(135deg, #374151, #4b5563); border: 1px solid #6b7280; color: #d1d5db; text-decoration: none;"
                       onmouseover="this.style.background='linear-gradient(135deg, #4b5563, #6b7280)'; this.style.borderColor='#fbbf24'; this.style.color='#f9fafb';"
                       onmouseout="this.style.background='linear-gradient(135deg, #374151, #4b5563)'; this.style.borderColor='#6b7280'; this.style.color='#d1d5db';">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Status Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-{{ $rentalCode->status === 'paid' ? 'success' : ($rentalCode->status === 'approved' ? 'info' : 'warning') }} d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-{{ $rentalCode->status === 'paid' ? 'check-circle' : ($rentalCode->status === 'approved' ? 'thumbs-up' : 'clock') }} fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-0">Current Status: {{ ucfirst($rentalCode->status) }}</h5>
                        <small>Last updated: {{ $rentalCode->updated_at->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
                <div class="text-end">
                    <div class="h4 mb-0">£{{ number_format($rentalCode->consultation_fee, 2) }}</div>
                    <small>Consultation Fee</small>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('rental-codes.update', $rentalCode) }}" method="POST" enctype="multipart/form-data" id="editRentalCodeForm">
        @csrf
        @method('PUT')
        
        <!-- Tab Navigation -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-fill" id="editTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="rental-tab" data-bs-toggle="tab" data-bs-target="#rental" type="button" role="tab">
                                    <i class="fas fa-key me-2"></i>Rental Info
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="client-tab" data-bs-toggle="tab" data-bs-target="#client" type="button" role="tab">
                                    <i class="fas fa-user me-2"></i>Client Details
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="agent-tab" data-bs-toggle="tab" data-bs-target="#agent" type="button" role="tab">
                                    <i class="fas fa-user-tie me-2"></i>Agent Info
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="status-tab" data-bs-toggle="tab" data-bs-target="#status" type="button" role="tab">
                                    <i class="fas fa-cog me-2"></i>Status & Notes
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="editTabContent">
            <!-- Rental Information Tab -->
            <div class="tab-pane fade show active" id="rental" role="tabpanel">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-key me-2"></i>Rental Information
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
                                                       value="{{ old('rental_code', $rentalCode->rental_code) }}" required>
                                            </div>
                                            @error('rental_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label for="rental_date" class="form-label">
                                                Rental Date <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                <input type="date" class="form-control @error('rental_date') is-invalid @enderror" 
                                                       id="rental_date" name="rental_date" 
                                                       value="{{ old('rental_date', $rentalCode->rental_date) }}" required>
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
                                                       value="{{ old('consultation_fee', $rentalCode->consultation_fee) }}" required>
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
                                                    <option value="Cash" {{ old('payment_method', $rentalCode->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                                    <option value="Transfer" {{ old('payment_method', $rentalCode->payment_method) == 'Transfer' ? 'selected' : '' }}>Transfer</option>
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
                                                       value="{{ old('property', $rentalCode->property) }}">
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
                                                       value="{{ old('licensor', $rentalCode->licensor) }}">
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
            </div>

            <!-- Client Information Tab -->
            <div class="tab-pane fade" id="client" role="tabpanel">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user me-2"></i>Client Information
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
                                                <option value="{{ $client->id }}" {{ old('existing_client_id', $rentalCode->client_id) == $client->id ? 'selected' : '' }}>
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
                                                           value="{{ old('client_full_name', $rentalCode->client ? $rentalCode->client->full_name : $rentalCode->client_full_name) }}">
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
                                                       value="{{ old('client_date_of_birth', $rentalCode->client ? $rentalCode->client->date_of_birth?->format('Y-m-d') : $rentalCode->client_date_of_birth) }}" required>
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
                                                       value="{{ old('client_phone_number', $rentalCode->client ? $rentalCode->client->phone_number : $rentalCode->client_phone_number) }}" required>
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
                                                       value="{{ old('client_email', $rentalCode->client ? $rentalCode->client->email : $rentalCode->client_email) }}" required>
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
                                                       value="{{ old('client_nationality', $rentalCode->client ? $rentalCode->client->nationality : $rentalCode->client_nationality) }}" required>
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
                                                          rows="3" required>{{ old('client_current_address', $rentalCode->client ? $rentalCode->client->current_address : $rentalCode->client_current_address) }}</textarea>
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
                                                       value="{{ old('client_company_university_name', $rentalCode->client ? $rentalCode->client->company_university_name : $rentalCode->client_company_university_name) }}">
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
                                                          rows="3">{{ old('client_company_university_address', $rentalCode->client ? $rentalCode->client->company_university_address : $rentalCode->client_company_university_address) }}</textarea>
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
                                                       value="{{ old('client_position_role', $rentalCode->client ? $rentalCode->client->position_role : $rentalCode->client_position_role) }}">
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
            </div>

            <!-- Agent Information Tab -->
            <div class="tab-pane fade" id="agent" role="tabpanel">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user-tie me-2"></i>Agent Information
                                </h5>
                            </div>
                            <div class="card-body">
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
                                                    @foreach($agentUsers as $user)
                                                        <option value="{{ $user->id }}" 
                                                                {{ old('rent_by_agent', $rentalCode->rent_by_agent) == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }}
                                                            @if($user->agent && $user->agent->company_name)
                                                                ({{ $user->agent->company_name }})
                                                            @endif
                                                        </option>
                                                    @endforeach
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
                                                    @foreach($marketingUsers as $user)
                                                        <option value="{{ $user->id }}" 
                                                                {{ old('marketing_agent', $rentalCode->marketing_agent) == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }}
                                                        </option>
                                                    @endforeach
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
                                                        <option value="{{ $i }}" {{ old('client_count', $rentalCode->client_count ?? 1) == $i ? 'selected' : '' }}>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Notes Tab -->
            <div class="tab-pane fade" id="status" role="tabpanel">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-cog me-2"></i>Status & Additional Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if(auth()->user()->role === 'admin')
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="status" class="form-label">
                                                Status <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-flag"></i></span>
                                                <select class="form-select @error('status') is-invalid @enderror" 
                                                        id="status" name="status" required>
                                                    <option value="pending" {{ old('status', $rentalCode->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="approved" {{ old('status', $rentalCode->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="paid" {{ old('status', $rentalCode->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                                </select>
                                            </div>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Status (Read Only)</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-flag"></i></span>
                                                <input type="text" class="form-control" value="{{ ucfirst($rentalCode->status) }}" readonly>
                                            </div>
                                            <small class="text-muted">Only administrators can change the status</small>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Record Information</label>
                                            <div class="info-display">
                                                <div class="info-item">
                                                    <strong>Created:</strong> {{ $rentalCode->created_at->format('d/m/Y H:i') }}
                                                </div>
                                                <div class="info-item">
                                                    <strong>Last Updated:</strong> {{ $rentalCode->updated_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
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
                                                          rows="4" placeholder="Enter any additional notes or comments">{{ old('notes', $rentalCode->notes) }}</textarea>
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
            </div>
        </div>

        <!-- Document Uploads -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-upload text-primary me-2"></i>Document Uploads
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="client_contract" class="form-label">
                                        Client Contract
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-file-contract"></i></span>
                                        <input type="file" class="form-control @error('client_contract') is-invalid @enderror" 
                                               id="client_contract" name="client_contract[]" 
                                               accept=".pdf,.jpg,.jpeg,.png" multiple>
                                    </div>
                                    @error('client_contract')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($rentalCode->client_contract)
                                        <div class="mt-2">
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Current: 
                                                @if(is_array($rentalCode->client_contract))
                                                    @foreach($rentalCode->client_contract as $index => $contract)
                                                        <a href="{{ Storage::url($contract) }}" target="_blank" class="text-decoration-none">Contract {{ count($rentalCode->client_contract) > 1 ? ($index + 1) : '' }}</a>{{ $index < count($rentalCode->client_contract) - 1 ? ', ' : '' }}
                                                    @endforeach
                                                @else
                                                    <a href="{{ Storage::url($rentalCode->client_contract) }}" target="_blank" class="text-decoration-none">View Current Contract</a>
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                    <small class="form-text text-muted">Upload client contract(s) (PDF, JPG, PNG - Max 10MB each, multiple files allowed)</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="payment_proof" class="form-label">
                                        Payment Proof
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-receipt"></i></span>
                                        <input type="file" class="form-control @error('payment_proof') is-invalid @enderror" 
                                               id="payment_proof" name="payment_proof[]" 
                                               accept=".pdf,.jpg,.jpeg,.png" multiple>
                                    </div>
                                    @error('payment_proof')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($rentalCode->payment_proof)
                                        <div class="mt-2">
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Current: 
                                                @if(is_array($rentalCode->payment_proof))
                                                    @foreach($rentalCode->payment_proof as $index => $proof)
                                                        <a href="{{ Storage::url($proof) }}" target="_blank" class="text-decoration-none">Proof {{ count($rentalCode->payment_proof) > 1 ? ($index + 1) : '' }}</a>{{ $index < count($rentalCode->payment_proof) - 1 ? ', ' : '' }}
                                                    @endforeach
                                                @else
                                                    <a href="{{ Storage::url($rentalCode->payment_proof) }}" target="_blank" class="text-decoration-none">View Current Payment Proof</a>
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                    <small class="form-text text-muted">Upload payment proof(s) (PDF, JPG, PNG - Max 10MB each, multiple files allowed)</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="client_id_document" class="form-label">
                                        Client ID Document
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        <input type="file" class="form-control @error('client_id_document') is-invalid @enderror" 
                                               id="client_id_document" name="client_id_document[]" 
                                               accept=".pdf,.jpg,.jpeg,.png" multiple>
                                    </div>
                                    @error('client_id_document')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($rentalCode->client_id_document)
                                        <div class="mt-2">
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Current: 
                                                @if(is_array($rentalCode->client_id_document))
                                                    @foreach($rentalCode->client_id_document as $index => $document)
                                                        <a href="{{ Storage::url($document) }}" target="_blank" class="text-decoration-none">ID {{ count($rentalCode->client_id_document) > 1 ? ($index + 1) : '' }}</a>{{ $index < count($rentalCode->client_id_document) - 1 ? ', ' : '' }}
                                                    @endforeach
                                                @else
                                                    <a href="{{ Storage::url($rentalCode->client_id_document) }}" target="_blank" class="text-decoration-none">View Current ID</a>
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                    <small class="form-text text-muted">Upload client ID document(s) (PDF, JPG, PNG - Max 10MB each, multiple files allowed)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo me-1"></i> Reset Changes
                                </button>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('rental-codes.show', $rentalCode) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Rental Code
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
/* Responsive improvements */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .card {
        margin-bottom: 1rem;
    }
    
    .input-group-text {
        font-size: 0.875rem;
    }
    
    .btn {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    
    .form-control, .form-select {
        font-size: 0.875rem;
    }
    
    .card-header h5 {
        font-size: 1rem;
    }
    
    .h3 {
        font-size: 1.25rem;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .btn-group .btn {
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 576px) {
    .d-flex.flex-column.flex-md-row {
        flex-direction: column !important;
    }
    
    .mb-3.mb-md-0 {
        margin-bottom: 1rem !important;
    }
    
    .input-group-text {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .btn-group {
        flex-direction: column;
    }
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
    font-weight: 500;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border-color: #dee2e6;
    color: #495057;
}

.nav-tabs .nav-link.active {
    border-bottom-color: #007bff;
    color: #007bff;
    background-color: transparent;
}

.form-group {
    margin-bottom: 1.5rem;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
    min-width: 45px;
    justify-content: center;
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
    border-bottom: 1px solid #e3e6f0;
    font-weight: 600;
}

.info-display {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    border-left: 4px solid #007bff;
}

.info-item {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.info-item:last-child {
    margin-bottom: 0;
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
}

.alert {
    border: none;
    border-radius: 0.5rem;
}

@media (max-width: 768px) {
    .nav-tabs .nav-link {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        margin-bottom: 0.5rem;
    }
}
</style>

<script>
// Form reset functionality
function resetForm() {
    if (confirm('Are you sure you want to reset all changes? This will restore the original values.')) {
        document.getElementById('editRentalCodeForm').reset();
        // Restore original values
        @foreach($rentalCode->getAttributes() as $key => $value)
            @if($value !== null)
                document.getElementById('{{ $key }}').value = '{{ $value }}';
            @endif
        @endforeach
    }
}

// Auto-save functionality (optional)
let autoSaveTimeout;
document.querySelectorAll('input, select, textarea').forEach(field => {
    field.addEventListener('change', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            // Implement auto-save logic here if needed
        }, 2000);
    });
});

// Form validation
document.getElementById('editRentalCodeForm').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
        // Switch to the first tab with errors
        const firstInvalidField = this.querySelector('.is-invalid');
        if (firstInvalidField) {
            const tabId = firstInvalidField.closest('.tab-pane').id;
            const tabButton = document.querySelector(`[data-bs-target="#${tabId}"]`);
            if (tabButton) {
                tabButton.click();
            }
        }
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

// Tab change tracking
document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
    tab.addEventListener('shown.bs.tab', function(e) {
        // Track which tab was visited
        localStorage.setItem('lastEditTab', e.target.getAttribute('data-bs-target'));
    });
});

// Restore last visited tab
document.addEventListener('DOMContentLoaded', function() {
    const lastTab = localStorage.getItem('lastEditTab');
    if (lastTab) {
        const tabButton = document.querySelector(`[data-bs-target="${lastTab}"]`);
        if (tabButton) {
            tabButton.click();
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
});
</script>
@endsection