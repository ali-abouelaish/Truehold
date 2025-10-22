@extends('layouts.admin')

@section('title', 'Edit Rental Code')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-edit text-primary me-2"></i>Edit Rental Code
                    </h1>
                    <p class="text-muted mb-0">Update rental code information and documents</p>
                </div>
                <div>
                    <a href="{{ route('rental-codes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('rental-codes.update', $rentalCode) }}" method="POST" enctype="multipart/form-data" id="editRentalCodeForm">
                @csrf
                @method('PUT')

                <!-- Rental Information -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-key me-2"></i>Rental Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rental_code" class="form-label">Rental Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('rental_code') is-invalid @enderror" 
                                           id="rental_code" name="rental_code" 
                                           value="{{ old('rental_code', $rentalCode->rental_code) }}" required>
                                    @error('rental_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rental_date" class="form-label">Rental Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('rental_date') is-invalid @enderror" 
                                           id="rental_date" name="rental_date" 
                                           value="{{ old('rental_date', $rentalCode->rental_date ? $rentalCode->rental_date->format('Y-m-d') : '') }}" required>
                                    @error('rental_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="consultation_fee" class="form-label">Consultation Fee <span class="text-danger">*</span></label>
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
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror" 
                                            id="payment_method" name="payment_method" required>
                                        <option value="">Select payment method</option>
                                        <option value="Cash" {{ old('payment_method', $rentalCode->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="Transfer" {{ old('payment_method', $rentalCode->payment_method) == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                                        <option value="Card machine" {{ old('payment_method', $rentalCode->payment_method) == 'Card machine' ? 'selected' : '' }}>Card machine</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="property" class="form-label">Property</label>
                                    <input type="text" class="form-control @error('property') is-invalid @enderror" 
                                           id="property" name="property" 
                                           value="{{ old('property', $rentalCode->property) }}">
                                    @error('property')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="licensor" class="form-label">Licensor</label>
                                    <input type="text" class="form-control @error('licensor') is-invalid @enderror" 
                                           id="licensor" name="licensor" 
                                           value="{{ old('licensor', $rentalCode->licensor) }}">
                                    @error('licensor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client Information -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>Client Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Client Selection <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="client_selection_type" id="existing_client" 
                                       value="existing" {{ old('client_selection_type', 'existing') == 'existing' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="existing_client">Existing Client</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="client_selection_type" id="new_client" 
                                       value="new" {{ old('client_selection_type') == 'new' ? 'checked' : '' }}>
                                <label class="form-check-label" for="new_client">New Client</label>
                            </div>
                        </div>

                        <!-- Existing Client Selection -->
                        <div id="existing_client_section">
                            <div class="mb-3">
                                <label for="existing_client_id" class="form-label">Select Client <span class="text-danger">*</span></label>
                                <select class="form-select @error('existing_client_id') is-invalid @enderror" 
                                        id="existing_client_id" name="existing_client_id">
                                    <option value="">Choose a client...</option>
                                    @foreach($existingClients as $client)
                                        <option value="{{ $client->id }}" {{ old('existing_client_id', $rentalCode->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->full_name }} 
                                            @if($client->phone_number) - {{ $client->phone_number }}@endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('existing_client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- New Client Form -->
                        <div id="new_client_section" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('client_full_name') is-invalid @enderror" 
                                               id="client_full_name" name="client_full_name" 
                                               value="{{ old('client_full_name') }}">
                                        @error('client_full_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control @error('client_phone_number') is-invalid @enderror" 
                                               id="client_phone_number" name="client_phone_number" 
                                               value="{{ old('client_phone_number') }}">
                                        @error('client_phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('client_email') is-invalid @enderror" 
                                               id="client_email" name="client_email" 
                                               value="{{ old('client_email') }}">
                                        @error('client_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('client_date_of_birth') is-invalid @enderror" 
                                               id="client_date_of_birth" name="client_date_of_birth" 
                                               value="{{ old('client_date_of_birth') }}">
                                        @error('client_date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agent Information -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-tie me-2"></i>Agent Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rent_by_agent" class="form-label">Rent By Agent <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('rent_by_agent') is-invalid @enderror" 
                                           id="rent_by_agent" name="rent_by_agent" 
                                           value="{{ old('rent_by_agent', $rentalCode->rent_by_agent) }}" required>
                                    @error('rent_by_agent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rental_agent_id" class="form-label">Rental Agent</label>
                                    <select class="form-select @error('rental_agent_id') is-invalid @enderror" 
                                            id="rental_agent_id" name="rental_agent_id">
                                        <option value="">Select rental agent</option>
                                        @foreach($agentUsers as $user)
                                            <option value="{{ $user->id }}" {{ old('rental_agent_id', $rentalCode->rental_agent_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                                @if($user->agent && $user->agent->company_name)
                                                    ({{ $user->agent->company_name }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('rental_agent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="marketing_agent_id" class="form-label">Marketing Agent</label>
                                    <select class="form-select @error('marketing_agent_id') is-invalid @enderror" 
                                            id="marketing_agent_id" name="marketing_agent_id">
                                        <option value="">Select marketing agent</option>
                                        @foreach($marketingUsers as $user)
                                            <option value="{{ $user->id }}" {{ old('marketing_agent_id', $rentalCode->marketing_agent_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('marketing_agent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="client_count" class="form-label">Client Count <span class="text-danger">*</span></label>
                                    <input type="number" min="1" max="10" class="form-control @error('client_count') is-invalid @enderror" 
                                           id="client_count" name="client_count" 
                                           value="{{ old('client_count', $rentalCode->client_count) }}" required>
                                    @error('client_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $rentalCode->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Document Uploads -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-upload me-2"></i>Document Uploads
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="client_contract" class="form-label">Client Contracts</label>
                                    <input type="file" class="form-control @error('client_contract.*') is-invalid @enderror" 
                                           id="client_contract" name="client_contract[]" multiple 
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">PDF, JPG, PNG files only (max 10MB each)</small>
                                    @error('client_contract.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_proof" class="form-label">Payment Proof</label>
                                    <input type="file" class="form-control @error('payment_proof.*') is-invalid @enderror" 
                                           id="payment_proof" name="payment_proof[]" multiple 
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">PDF, JPG, PNG files only (max 10MB each)</small>
                                    @error('payment_proof.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="client_id_document" class="form-label">Client ID Documents</label>
                                    <input type="file" class="form-control @error('client_id_document.*') is-invalid @enderror" 
                                           id="client_id_document" name="client_id_document[]" multiple 
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">PDF, JPG, PNG files only (max 10MB each)</small>
                                    @error('client_id_document.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rental-codes.show', $rentalCode) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-warning me-2" onclick="resetForm()">
                                    <i class="fas fa-undo me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Update Rental Code
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Client selection toggle
document.addEventListener('DOMContentLoaded', function() {
    const existingClientRadio = document.getElementById('existing_client');
    const newClientRadio = document.getElementById('new_client');
    const existingSection = document.getElementById('existing_client_section');
    const newSection = document.getElementById('new_client_section');

    function toggleClientSections() {
        if (existingClientRadio.checked) {
            existingSection.style.display = 'block';
            newSection.style.display = 'none';
            // Make existing client required
            document.getElementById('existing_client_id').required = true;
            // Make new client fields not required
            document.querySelectorAll('#new_client_section input[required]').forEach(input => {
                input.required = false;
            });
        } else if (newClientRadio.checked) {
            existingSection.style.display = 'none';
            newSection.style.display = 'block';
            // Make existing client not required
            document.getElementById('existing_client_id').required = false;
            // Make new client fields required
            document.querySelectorAll('#new_client_section input[required]').forEach(input => {
                input.required = true;
            });
        }
    }

    existingClientRadio.addEventListener('change', toggleClientSections);
    newClientRadio.addEventListener('change', toggleClientSections);
    
    // Initialize
    toggleClientSections();
});

// Form reset function
function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        document.getElementById('editRentalCodeForm').reset();
    }
}

// Form submission with validation
document.getElementById('editRentalCodeForm').addEventListener('submit', function(e) {
    console.log('Form submission started');
    
    // Basic validation
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    let missingFields = [];
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
            missingFields.push(field.name || field.id);
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields: ' + missingFields.join(', '));
        return false;
    }
    
    console.log('Form validation passed, submitting...');
    return true;
});
</script>
@endsection