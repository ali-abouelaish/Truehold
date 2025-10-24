@extends('layouts.admin')

@section('title', 'Create Rental Code')

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
                    <h2 class="h3 mb-0 text-primary fw-bold" style="text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <i class="fas fa-plus-circle text-primary me-2" style="filter: brightness(1.2);"></i>
                        <span class="d-none d-sm-inline">Create New Rental Code</span>
                        <span class="d-sm-none">New Rental Code</span>
                    </h2>
                    <p class="text-muted mb-0 d-none d-md-block">Add a new rental code application to the system</p>
                </div>
                <div>
                    <a href="{{ route('rental-codes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Back to List</span>
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

    <form action="{{ route('rental-codes.store') }}" method="POST" enctype="multipart/form-data" id="rentalCodeForm">
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
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="rental_code" class="form-label">
                                        Rental Code <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="text" class="form-control @error('rental_code') is-invalid @enderror" 
                                               id="rental_code" name="rental_code" 
                                               value="{{ old('rental_code', \App\Models\RentalCode::generateRentalCode()) }}" 
                                               placeholder="Auto-generated" readonly>
                                        <span class="input-group-text bg-success text-white d-none d-sm-flex">
                                            <i class="fas fa-check"></i> <span class="d-none d-md-inline">Auto-generated</span>
                                        </span>
                                    </div>
                                    @error('rental_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Rental code is automatically generated in format: CC0121, CC0122, etc.</small>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="rental_date" class="form-label">
                                        Date <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        <input type="date" class="form-control @error('rental_date') is-invalid @enderror" 
                                               id="rental_date" name="rental_date" 
                                               value="{{ old('rental_date') }}" 
                                               min="{{ date('Y-m-d') }}" required>
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
                                        <input type="number" step="0.01" min="0" class="form-control @error('consultation_fee') is-invalid @enderror" 
                                               id="consultation_fee" name="consultation_fee" 
                                               value="{{ old('consultation_fee') }}" 
                                               placeholder="250.00" required>
                                    </div>
                                    @error('consultation_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-12 col-md-6">
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
                                             <option value="Card machine" {{ old('payment_method') == 'Card machine' ? 'selected' : '' }}>Card machine</option>
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
                                               placeholder="Property address or description" required>
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
                                               placeholder="Property owner or licensor" required>
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

                        <!-- New Client Forms -->
                        <div id="new-client-section" class="client-section" style="display: none;">
                            <div id="client-forms-container">
                                <!-- Client forms will be dynamically generated here -->
                            </div>
                        </div>

        <!-- Agent Information -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0" style="color: #1a1a1a; font-weight: 600;">
                            <i class="fas fa-user-tie text-primary me-2"></i>Agent Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="rental_agent_id" class="form-label">Rental Agent *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                    <select class="form-select @error('rental_agent_id') is-invalid @enderror" 
                                            id="rental_agent_id" name="rental_agent_id" required>
                                        <option value="">Select rental agent</option>
                                        @foreach($agentUsers as $user)
                                            <option value="{{ $user->id }}" {{ old('rental_agent_id', auth()->user()->id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                                @if($user->agent && $user->agent->company_name) - {{ $user->agent->company_name }}@endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('rental_agent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            </div>
                            <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="marketing_agent_id" class="form-label">Marketing Agent</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-bullhorn"></i></span>
                                    <select class="form-select @error('marketing_agent_id') is-invalid @enderror" 
                                            id="marketing_agent_id" name="marketing_agent_id">
                                        <option value="">Select marketing agent (optional)</option>
                                        @forelse($marketingUsers as $user)
                                            <option value="{{ $user->id }}" {{ old('marketing_agent_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No marketing users found</option>
                                        @endforelse
                                    </select>
                                </div>
                                @error('marketing_agent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            </div>
                    </div>
                    <div class="row">
                            <div class="col-12 col-md-6">
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
                        <div class="col-12 col-md-6">
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

        <!-- Document Uploads -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-upload me-2"></i>Document Uploads
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="client_contract" class="form-label">
                                        <i class="fas fa-file-contract text-primary me-1"></i>Client Contracts <span class="text-danger">*</span>
                                        <small class="text-danger d-block">Required</small>
                                    </label>
                                    <input type="file" class="form-control @error('client_contract.*') is-invalid @enderror" 
                                           id="client_contract" name="client_contract[]" 
                                           accept=".pdf,.jpg,.jpeg,.png" multiple required>
                                    <small class="form-text text-muted">PDF, JPG, PNG files (max 10MB each)</small>
                                    @error('client_contract.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_proof" class="form-label">
                                        <i class="fas fa-receipt text-success me-1"></i>Payment Proof <span class="text-danger">*</span>
                                        <small class="text-danger d-block">Required</small>
                                    </label>
                                    <input type="file" class="form-control @error('payment_proof.*') is-invalid @enderror" 
                                           id="payment_proof" name="payment_proof[]" 
                                           accept=".pdf,.jpg,.jpeg,.png" multiple required>
                                    <small class="form-text text-muted">PDF, JPG, PNG files (max 10MB each)</small>
                                    @error('payment_proof.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="client_id_document" class="form-label">
                                        <i class="fas fa-id-card text-info me-1"></i>Client ID Documents
                                        <small class="text-muted d-block">Optional</small>
                                    </label>
                                    <input type="file" class="form-control @error('client_id_document.*') is-invalid @enderror" 
                                           id="client_id_document" name="client_id_document[]" 
                                           accept=".pdf,.jpg,.jpeg,.png" multiple>
                                    <small class="form-text text-muted">PDF, JPG, PNG files (max 10MB each)</small>
                                    @error('client_id_document.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Upload Progress Indicator -->
                        <div id="upload-progress" class="alert alert-info" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                            <strong>Processing files...</strong>
                            <div class="progress mt-2">
                                <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted">Please wait while files are being processed. Submission will proceed automatically once complete.</small>
                            <div id="upload-status" class="mt-2">
                                <small class="text-muted">Checking file uploads...</small>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>File Upload Requirements:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Client Contracts and Payment Proof are required</strong> - marked with red asterisks (*)</li>
                                <li>Client ID Documents are optional but recommended for record keeping</li>
                                <li>If you upload files, the system will wait for all uploads to complete before submission</li>
                                <li>You can upload multiple files for each document type</li>
                                <li>Supported formats: PDF, JPG, JPEG, PNG</li>
                                <li>Maximum file size: 10MB per file</li>
                                <li>Submission will be delayed if files are being uploaded</li>
                            </ul>
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

/* Form validation styles */
.form-control.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.94-.94 1.44-1.44L4.3 3.3l-.94.94L2.3 6.73z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-valid:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-control.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4 1.4-1.4'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Validation feedback styling */
.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.valid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #28a745;
}
</style>

<script>
// Client selection toggle and form validation
document.addEventListener('DOMContentLoaded', function() {
    const existingClientRadio = document.getElementById('existing_client');
    const newClientRadio = document.getElementById('new_client');
    const existingClientSection = document.getElementById('existing-client-section');
    const newClientSection = document.getElementById('new-client-section');
    const form = document.getElementById('rentalCodeForm');
    
    function toggleClientSections() {
        if (existingClientRadio.checked) {
            existingClientSection.style.display = 'block';
            newClientSection.style.display = 'none';
            // Clear validation for new client fields
            clearValidationForNewClientFields();
            // Remove required attributes from new client fields
            removeRequiredFromNewClientFields();
            // Add required attribute to existing client field
            addRequiredToExistingClientField();
            // Clear and disable new client fields to prevent submission
            clearAndDisableNewClientFields();
            // Enable existing client field
            enableExistingClientField();
        } else if (newClientRadio.checked) {
            existingClientSection.style.display = 'none';
            newClientSection.style.display = 'block';
            // Generate client forms based on client count
            generateClientForms();
            // Clear validation for existing client field
            clearValidationForExistingClientField();
            // Remove required attribute from existing client field
            removeRequiredFromExistingClientField();
            // Add required attributes to new client fields
            addRequiredToNewClientFields();
            // Clear and disable existing client field
            clearAndDisableExistingClientField();
            // Enable new client fields
            enableNewClientFields();
        }
    }
    
    existingClientRadio.addEventListener('change', toggleClientSections);
    newClientRadio.addEventListener('change', toggleClientSections);
    
    // Client count change handler
    const clientCountSelect = document.getElementById('client_count');
    if (clientCountSelect) {
        clientCountSelect.addEventListener('change', function() {
            if (newClientRadio.checked) {
                generateClientForms();
            }
        });
    }
    
    // Generate dynamic client forms
    function generateClientForms() {
        const container = document.getElementById('client-forms-container');
        const clientCount = parseInt(document.getElementById('client_count').value) || 1;
        
        if (!container) {
            console.error('Client forms container not found!');
            return;
        }
        
        container.innerHTML = '';
        
        for (let i = 1; i <= clientCount; i++) {
            const clientForm = createClientForm(i);
            container.appendChild(clientForm);
        }
    }
    
    // Create a single client form
    function createClientForm(clientIndex) {
        const formDiv = document.createElement('div');
        formDiv.className = 'card mb-4';
        formDiv.innerHTML = `
            <div class="card-header">
                <h6 class="mb-0">Client ${clientIndex} Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="client_${clientIndex}_full_name" class="form-label">Full Name *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="client_${clientIndex}_full_name" name="client_${clientIndex}_full_name" placeholder="Enter full name" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="client_${clientIndex}_date_of_birth" class="form-label">Date of Birth *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
                                <input type="date" class="form-control" id="client_${clientIndex}_date_of_birth" name="client_${clientIndex}_date_of_birth" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="client_${clientIndex}_phone_number" class="form-label">Phone Number *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="client_${clientIndex}_phone_number" name="client_${clientIndex}_phone_number" placeholder="Enter phone number" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="client_${clientIndex}_email" class="form-label">Email *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="client_${clientIndex}_email" name="client_${clientIndex}_email" placeholder="Enter email address" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="client_${clientIndex}_nationality" class="form-label">Nationality *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-flag"></i></span>
                                <input type="text" class="form-control" id="client_${clientIndex}_nationality" name="client_${clientIndex}_nationality" placeholder="Enter nationality" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="client_${clientIndex}_current_address" class="form-label">Current Address *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea class="form-control" id="client_${clientIndex}_current_address" name="client_${clientIndex}_current_address" rows="3" placeholder="Enter current address" required></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="client_${clientIndex}_company_university_name" class="form-label">Company/University Name *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                <input type="text" class="form-control" id="client_${clientIndex}_company_university_name" name="client_${clientIndex}_company_university_name" placeholder="Enter company or university name" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="client_${clientIndex}_company_university_address" class="form-label">Company/University Address *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea class="form-control" id="client_${clientIndex}_company_university_address" name="client_${clientIndex}_company_university_address" rows="3" placeholder="Enter company or university address" required></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="client_${clientIndex}_position_role" class="form-label">Position/Role *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                <input type="text" class="form-control" id="client_${clientIndex}_position_role" name="client_${clientIndex}_position_role" placeholder="Enter position or role" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="client_${clientIndex}_current_landlord_name" class="form-label">Current Landlord/Agency Name *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-home"></i></span>
                                <input type="text" class="form-control" id="client_${clientIndex}_current_landlord_name" name="client_${clientIndex}_current_landlord_name" placeholder="Enter landlord or agency name" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="client_${clientIndex}_current_landlord_contact_info" class="form-label">Current Landlord/Agency Contact Info *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <textarea class="form-control" id="client_${clientIndex}_current_landlord_contact_info" name="client_${clientIndex}_current_landlord_contact_info" rows="3" placeholder="Phone, email, address, or other contact details..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return formDiv;
    }
    
    // Initialize
    toggleClientSections();
    
    // Add form submission debugging
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submission started');
            console.log('Form data:', new FormData(form));
            
            // Check if all required fields are filled
            const requiredFields = form.querySelectorAll('[required]');
            let missingFields = [];
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    missingFields.push(field.name || field.id);
                }
            });
            
            if (missingFields.length > 0) {
                console.log('Missing required fields:', missingFields);
                e.preventDefault();
                alert('Please fill in all required fields: ' + missingFields.join(', '));
                return false;
            }
            
            console.log('Form validation passed, submitting...');
        });
    }
    
    // Rental code is now auto-generated on page load, no button needed
    
    // Form validation functions
    function validateField(fieldName, value, rules) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field) return true;
        
        // Required validation
        if (rules.required && (!value || value.trim() === '')) {
            showFieldError(fieldName, `${getFieldLabel(fieldName)} is required`);
            return false;
        }
        
        // Email validation
        if (rules.email && value && !isValidEmail(value)) {
            showFieldError(fieldName, 'Please enter a valid email address');
            return false;
        }
        
        // Phone validation
        if (rules.phone && value && !isValidPhone(value)) {
            showFieldError(fieldName, 'Please enter a valid phone number');
            return false;
        }
        
        // Number validation
        if (rules.numeric && value && isNaN(parseFloat(value))) {
            showFieldError(fieldName, 'Please enter a valid number');
            return false;
        }
        
        // Min value validation
        if (rules.min && value && parseFloat(value) < rules.min) {
            showFieldError(fieldName, `Value must be at least ${rules.min}`);
            return false;
        }
        
        // Date validation
        if (rules.date && value && !isValidDate(value)) {
            showFieldError(fieldName, 'Please enter a valid date');
            return false;
        }
        
        // Future date validation
        if (rules.futureDate && value && !isFutureDate(value)) {
            showFieldError(fieldName, 'Date must be in the future');
            return false;
        }
        
        clearFieldError(fieldName);
        return true;
    }
    
    function showFieldError(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field) return;
        
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
        
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
        
        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }
    
    function clearFieldError(fieldName) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field) return;
        
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        
        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
    
    function clearValidationForNewClientFields() {
        const newClientFields = ['client_full_name', 'client_date_of_birth', 'client_phone_number', 'client_email', 'client_nationality', 'client_current_address'];
        newClientFields.forEach(fieldName => {
            clearFieldError(fieldName);
        });
    }
    
    function clearValidationForExistingClientField() {
        clearFieldError('existing_client_id');
    }
    
    function removeRequiredFromNewClientFields() {
        const newClientFields = ['client_full_name', 'client_date_of_birth', 'client_phone_number', 'client_email', 'client_nationality', 'client_current_address'];
        newClientFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.removeAttribute('required');
            }
        });
    }
    
    function addRequiredToNewClientFields() {
        const newClientFields = ['client_full_name', 'client_date_of_birth', 'client_phone_number', 'client_email', 'client_nationality', 'client_current_address'];
        newClientFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.setAttribute('required', 'required');
            }
        });
    }
    
    function removeRequiredFromExistingClientField() {
        const field = document.querySelector('[name="existing_client_id"]');
        if (field) {
            field.removeAttribute('required');
        }
    }
    
    function addRequiredToExistingClientField() {
        const field = document.querySelector('[name="existing_client_id"]');
        if (field) {
            field.setAttribute('required', 'required');
        }
    }
    
    function clearAndDisableNewClientFields() {
        const newClientFields = ['client_full_name', 'client_date_of_birth', 'client_phone_number', 'client_email', 'client_nationality', 'client_current_address'];
        newClientFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.value = '';
                field.disabled = true;
            }
        });
    }
    
    function clearAndDisableExistingClientField() {
        const field = document.querySelector('[name="existing_client_id"]');
        if (field) {
            field.value = '';
            field.disabled = true;
        }
    }
    
    function enableNewClientFields() {
        const newClientFields = ['client_full_name', 'client_date_of_birth', 'client_phone_number', 'client_email', 'client_nationality', 'client_current_address'];
        newClientFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.disabled = false;
            }
        });
    }
    
    function enableExistingClientField() {
        const field = document.querySelector('[name="existing_client_id"]');
        if (field) {
            field.disabled = false;
        }
    }
    
    function getFieldLabel(fieldName) {
        const labels = {
            'rental_code': 'Rental Code',
            'rental_date': 'Date',
            'consultation_fee': 'Consultation Fee',
            'payment_method': 'Payment Method',
            'client_full_name': 'Client Full Name',
            'client_date_of_birth': 'Client Date of Birth',
            'client_phone_number': 'Client Phone Number',
            'client_email': 'Client Email',
            'client_nationality': 'Client Nationality',
            'client_current_address': 'Client Current Address',
            'existing_client_id': 'Existing Client',
            'rent_by_agent': 'Client Code',
            'client_count': 'Client Count'
        };
        return labels[fieldName] || fieldName;
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function isValidPhone(phone) {
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        return phoneRegex.test(phone);
    }
    
    function isValidDate(dateString) {
        const date = new Date(dateString);
        return date instanceof Date && !isNaN(date);
    }
    
    function isFutureDate(dateString) {
        const date = new Date(dateString);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return date > today;
    }
    
    // Real-time validation for key fields
    const validationRules = {
        'rental_date': { required: true, date: true, futureDate: true },
        'consultation_fee': { required: true, numeric: true, min: 0 },
        'payment_method': { required: true },
        'client_full_name': { required: true },
        'client_date_of_birth': { required: true, date: true },
        'client_phone_number': { required: true, phone: true },
        'client_email': { required: true, email: true },
        'client_nationality': { required: true },
        'client_current_address': { required: true },
        'existing_client_id': { required: true },
        'rent_by_agent': { required: true },
        'client_count': { required: true, numeric: true, min: 1 }
    };
    
    // Add event listeners for real-time validation
    Object.keys(validationRules).forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.addEventListener('blur', function() {
                const value = this.value;
                const rules = validationRules[fieldName];
                
                // Check if field is required based on client selection
                if (fieldName.startsWith('client_') && fieldName !== 'client_count') {
                    const isNewClient = newClientRadio.checked;
                    const isExistingClient = existingClientRadio.checked;
                    
                    if (fieldName === 'existing_client_id' && !isExistingClient) {
                        clearFieldError(fieldName);
                        return;
                    }
                    
                    if (fieldName !== 'existing_client_id' && !isNewClient) {
                        clearFieldError(fieldName);
                        return;
                    }
                }
                
                validateField(fieldName, value, rules);
            });
            
            field.addEventListener('input', function() {
                // Clear error on input for better UX
                if (this.classList.contains('is-invalid')) {
                    const value = this.value;
                    const rules = validationRules[fieldName];
                    validateField(fieldName, value, rules);
                }
            });
        }
    });
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validate all required fields
        Object.keys(validationRules).forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                const value = field.value;
                const rules = validationRules[fieldName];
                
                // Check if field should be validated based on client selection
                if (fieldName.startsWith('client_') && fieldName !== 'client_count') {
                    const isNewClient = newClientRadio.checked;
                    const isExistingClient = existingClientRadio.checked;
                    
                    if (fieldName === 'existing_client_id' && !isExistingClient) {
                        return;
                    }
                    
                    if (fieldName !== 'existing_client_id' && !isNewClient) {
                        return;
                    }
                }
                
                if (!validateField(fieldName, value, rules)) {
                    isValid = false;
                }
            }
        });
        
        // Check if files are being uploaded and wait for completion
        const requiredFileInputs = ['client_contract', 'payment_proof'];
        const optionalFileInputs = ['client_id_document'];
        const allFileInputs = [...requiredFileInputs, ...optionalFileInputs];
        let hasFiles = false;
        let uploadInProgress = false;
        
        allFileInputs.forEach(fieldName => {
            const fileInput = form.querySelector(`[name="${fieldName}[]"]`);
            if (fileInput && fileInput.files && fileInput.files.length > 0) {
                hasFiles = true;
                // Check if any files are still being processed or are very small (indicating incomplete upload)
                for (let i = 0; i < fileInput.files.length; i++) {
                    const file = fileInput.files[i];
                    if (file.size === 0 || file.size < 1024) { // Less than 1KB might indicate incomplete upload
                        uploadInProgress = true;
                        break;
                    }
                }
            }
        });
        
        if (hasFiles) {
            e.preventDefault();
            uploadCheckCount = 0; // Reset counter
            showUploadProgress();
            // Wait for uploads to complete
            setTimeout(() => {
                checkUploadStatus();
            }, 2000); // Give more time for uploads to complete
            return;
        }

        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });
    
    // Upload progress functions
    function showUploadProgress() {
        const progressDiv = document.getElementById('upload-progress');
        const progressBar = document.getElementById('upload-progress-bar');
        
        progressDiv.style.display = 'block';
        progressBar.style.width = '0%';
        
        // Animate progress bar
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            progressBar.style.width = progress + '%';
        }, 200);
        
        // Store interval for cleanup
        progressDiv.dataset.interval = interval;
    }
    
    function hideUploadProgress() {
        const progressDiv = document.getElementById('upload-progress');
        const progressBar = document.getElementById('upload-progress-bar');
        
        progressBar.style.width = '100%';
        setTimeout(() => {
            progressDiv.style.display = 'none';
            if (progressDiv.dataset.interval) {
                clearInterval(progressDiv.dataset.interval);
            }
        }, 500);
    }
    
    let uploadCheckCount = 0;
    const maxUploadChecks = 20; // Maximum 30 seconds of waiting (20 * 1.5s)
    
    function checkUploadStatus() {
        uploadCheckCount++;
        
        const statusDiv = document.getElementById('upload-status');
        statusDiv.innerHTML = `<small class="text-muted">Checking file uploads... (${uploadCheckCount}/${maxUploadChecks})</small>`;
        
        const requiredFileInputs = ['client_contract', 'payment_proof'];
        const optionalFileInputs = ['client_id_document'];
        const allFileInputs = [...requiredFileInputs, ...optionalFileInputs];
        let allUploadsComplete = true;
        
        allFileInputs.forEach(fieldName => {
            const fileInput = document.querySelector(`[name="${fieldName}[]"]`);
            if (fileInput && fileInput.files && fileInput.files.length > 0) {
                for (let i = 0; i < fileInput.files.length; i++) {
                    const file = fileInput.files[i];
                    if (file.size === 0 || file.size < 1024) { // Less than 1KB might indicate incomplete upload
                        allUploadsComplete = false;
                        break;
                    }
                }
            }
        });
        
        if (allUploadsComplete || uploadCheckCount >= maxUploadChecks) {
            if (uploadCheckCount >= maxUploadChecks) {
                statusDiv.innerHTML = `<small class="text-warning">Timeout reached, proceeding with submission...</small>`;
            } else {
                statusDiv.innerHTML = `<small class="text-success">All files processed successfully!</small>`;
            }
            hideUploadProgress();
            // Submit the form
            document.getElementById('rentalCodeForm').submit();
        } else {
            // Continue waiting
            setTimeout(() => {
                checkUploadStatus();
            }, 1500); // Check every 1.5 seconds
        }
    }
    
    // Monitor file inputs for changes
    const allFileInputs = ['client_contract', 'payment_proof', 'client_id_document'];
    allFileInputs.forEach(fieldName => {
        const fileInput = document.querySelector(`[name="${fieldName}[]"]`);
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                // Reset any previous validation states
                this.classList.remove('is-invalid');
                const errorDiv = this.parentNode.querySelector('.invalid-feedback');
                if (errorDiv) {
                    errorDiv.remove();
                }
            });
        }
    });
    
    // Add specific validation for required fields
    const requiredFileInputs = ['client_contract', 'payment_proof'];
    requiredFileInputs.forEach(fieldName => {
        const fileInput = document.querySelector(`[name="${fieldName}[]"]`);
        if (fileInput) {
            fileInput.addEventListener('blur', function() {
                if (!this.files || this.files.length === 0) {
                    this.classList.add('is-invalid');
                    const errorDiv = this.parentNode.querySelector('.invalid-feedback') || 
                        this.parentNode.appendChild(document.createElement('div'));
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = `${fieldName.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())} is required.`;
                } else {
                    this.classList.remove('is-invalid');
                    const errorDiv = this.parentNode.querySelector('.invalid-feedback');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }
            });
        }
    });
});
</script>
@endsection