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
                                    <label for="rental_date" class="form-label">Date <span class="text-danger">*</span></label>
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
                            <label for="existing_client_id" class="form-label">Select Client <span class="text-danger">*</span></label>
                            <select class="form-select @error('existing_client_id') is-invalid @enderror" 
                                    id="existing_client_id" name="existing_client_id" required>
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
                                    <label for="rental_agent_id" class="form-label">Rental Agent <span class="text-danger">*</span></label>
                                    <select class="form-select @error('rental_agent_id') is-invalid @enderror" 
                                            id="rental_agent_id" name="rental_agent_id" required>
                                        <option value="">Select rental agent</option>
                                        @foreach($agentUsers as $user)
                                            <option value="{{ $user->id }}" {{ old('rental_agent_id', $rentalCode->rental_agent_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                                @if($user->agent && $user->agent->company_name)
                                                    - {{ $user->agent->company_name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('rental_agent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="marketing_agent_id" class="form-label">Marketing Agent</label>
                                    <select class="form-select @error('marketing_agent_id') is-invalid @enderror" 
                                            id="marketing_agent_id" name="marketing_agent_id">
                                        <option value="">Select marketing agent (optional)</option>
                                        @foreach($marketingUsers as $user)
                                            <option value="{{ $user->id }}" {{ old('marketing_agent_id', $rentalCode->marketing_agent_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('marketing_agent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Pasquale Marketing Checkbox -->
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" 
                                               id="pasquale_marketing" name="pasquale_marketing" 
                                               value="1" {{ old('pasquale_marketing', ($rentalCode->client_count >= 2 && $rentalCode->marketing_agent_id) ? '1' : '') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pasquale_marketing">
                                            <strong>Pasquale marketing</strong> (Sets marketing fee to £40)
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        When checked, automatically sets client count to 2+ to ensure marketing fee is £40
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="client_count" class="form-label">Client Count <span class="text-danger">*</span></label>
                                    <select class="form-select @error('client_count') is-invalid @enderror" 
                                            id="client_count" name="client_count" required>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('client_count', $rentalCode->client_count) == $i ? 'selected' : '' }}>
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

                <!-- Existing Documents -->
                @php
                    // Parse existing client contracts (handle both old single string and new array format)
                    $existingClientContracts = null;
                    if ($rentalCode->client_contract) {
                        if (is_string($rentalCode->client_contract)) {
                            $decoded = json_decode($rentalCode->client_contract, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $existingClientContracts = $decoded;
                            } else {
                                $existingClientContracts = [$rentalCode->client_contract];
                            }
                        } elseif (is_array($rentalCode->client_contract)) {
                            $existingClientContracts = $rentalCode->client_contract;
                        } else {
                            $existingClientContracts = [$rentalCode->client_contract];
                        }
                    }
                @endphp
                @if($existingClientContracts && is_array($existingClientContracts) && count($existingClientContracts) > 0)
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-alt me-2"></i>Existing Documents
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3">
                                    <i class="fas fa-file-contract text-primary me-2"></i>Client Contracts ({{ count($existingClientContracts) }})
                                </h6>
                                <div class="list-group">
                                    @foreach($existingClientContracts as $index => $contract)
                                        @if($contract && is_string($contract))
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-contract text-primary me-3"></i>
                                                    <div>
                                                        <strong>{{ basename($contract) }}</strong>
                                                        <br>
                                                        <small class="text-muted">Document {{ $index + 1 }}</small>
                                                    </div>
                                                </div>
                                                <div>
                                                    <a href="{{ route('rental-codes.view-file', ['rentalCode' => $rentalCode->id, 'field' => 'client_contract', 'index' => $index]) }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary me-2">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                    <a href="{{ route('rental-codes.download-file', ['rentalCode' => $rentalCode->id, 'field' => 'client_contract', 'index' => $index]) }}" 
                                                       class="btn btn-sm btn-outline-secondary me-2">
                                                        <i class="fas fa-download me-1"></i>Download
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            onclick="removeDocument('client_contract', {{ $index }})">
                                                        <i class="fas fa-trash me-1"></i>Remove
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Document Uploads -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-upload me-2"></i>Document Uploads (Optional)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="client_contract" class="form-label">
                                        <i class="fas fa-file-contract text-primary me-1"></i>Client Contracts
                                    </label>
                                    <input type="file" class="form-control @error('client_contract.*') is-invalid @enderror" 
                                           id="client_contract" name="client_contract[]" multiple 
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">PDF, JPG, PNG files (max 10MB each). New files will be added to existing documents.</small>
                                    @error('client_contract.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_proof" class="form-label">
                                        <i class="fas fa-receipt text-success me-1"></i>Payment Proof
                                    </label>
                                    <input type="file" class="form-control @error('payment_proof.*') is-invalid @enderror" 
                                           id="payment_proof" name="payment_proof[]" multiple 
                                           accept=".pdf,.jpg,.jpeg,.png">
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
                                    </label>
                                    <input type="file" class="form-control @error('client_id_document.*') is-invalid @enderror" 
                                           id="client_id_document" name="client_id_document[]" multiple 
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">PDF, JPG, PNG files (max 10MB each)</small>
                                    @error('client_id_document.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>File Upload Tips:</strong>
                            <ul class="mb-0 mt-2">
                                <li>You can upload multiple files for each document type</li>
                                <li>Supported formats: PDF, JPG, JPEG, PNG</li>
                                <li>Maximum file size: 10MB per file</li>
                                <li>New files will be added to existing documents</li>
                            </ul>
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
                
                <!-- Hidden input to track documents to remove -->
                <input type="hidden" id="removed_client_contracts" name="removed_client_contracts" value="">
            </form>
        </div>
    </div>
</div>

<script>
// Track removed documents
let removedClientContracts = [];

// Remove document function
function removeDocument(field, index) {
    if (confirm('Are you sure you want to remove this document? This action cannot be undone.')) {
        if (field === 'client_contract') {
            removedClientContracts.push(index);
            document.getElementById('removed_client_contracts').value = JSON.stringify(removedClientContracts);
            
            // Hide the removed document in the UI
            const listItems = document.querySelectorAll('.list-group-item');
            listItems.forEach((item, idx) => {
                if (item.querySelector(`button[onclick*="removeDocument('client_contract', ${index})"]`)) {
                    item.style.opacity = '0.5';
                    item.style.textDecoration = 'line-through';
                    item.querySelector('button[onclick*="removeDocument"]').disabled = true;
                    item.querySelector('button[onclick*="removeDocument"]').innerHTML = '<i class="fas fa-check me-1"></i>Marked for Removal';
                }
            });
        }
    }
}

// Form reset function
function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        document.getElementById('editRentalCodeForm').reset();
        removedClientContracts = [];
        document.getElementById('removed_client_contracts').value = '';
        
        // Reset UI changes
        document.querySelectorAll('.list-group-item').forEach(item => {
            item.style.opacity = '1';
            item.style.textDecoration = 'none';
            const removeBtn = item.querySelector('button[onclick*="removeDocument"]');
            if (removeBtn) {
                removeBtn.disabled = false;
                removeBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Remove';
            }
        });
    }
}

// Pasquale marketing checkbox handler
const pasqualeCheckbox = document.getElementById('pasquale_marketing');
const clientCountSelect = document.getElementById('client_count');

if (pasqualeCheckbox && clientCountSelect) {
    pasqualeCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // Set client count to at least 2 to ensure marketing fee is £40
            const currentValue = parseInt(clientCountSelect.value);
            if (currentValue < 2) {
                clientCountSelect.value = 2;
            }
        }
    });
    
    // Client count change handler
    clientCountSelect.addEventListener('change', function() {
        const currentValue = parseInt(this.value);
        if (pasqualeCheckbox.checked) {
            // If Pasquale marketing is checked, ensure client count is at least 2
            if (currentValue < 2) {
                this.value = 2;
            }
        } else {
            // If client count is set to 1, uncheck Pasquale marketing (if it was somehow checked)
            if (currentValue === 1 && pasqualeCheckbox.checked) {
                pasqualeCheckbox.checked = false;
            }
        }
    });
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