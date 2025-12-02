@extends('layouts.admin')

@section('title', 'Rental Code Details')

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
    
    .col-lg-4 {
        margin-bottom: 1rem;
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
    
    .table th, .table td {
        padding: 0.5rem;
        font-size: 0.8rem;
    }
}
</style>

<style>
/* Dark mode styles for rental codes show page */
.card {
    background-color: #1f2937 !important;
    border-color: #374151 !important;
}

.card-header {
    background-color: #374151 !important;
    border-color: #4b5563 !important;
}

.card-header.bg-primary {
    background-color: #1e40af !important;
}

.card-header.bg-info {
    background-color: #0891b2 !important;
}

.card-header.bg-success {
    background-color: #059669 !important;
}

.card-header.bg-warning {
    background-color: #d97706 !important;
}

.card-header.bg-danger {
    background-color: #dc2626 !important;
}

.card-body {
    background-color: #1f2937 !important;
    color: #d1d5db !important;
}

.info-label {
    color: #9ca3af !important;
    font-weight: 500;
}

.info-value {
    color: #d1d5db !important;
}

.text-muted {
    color: #6b7280 !important;
}

.text-success {
    color: #10b981 !important;
}

.text-warning {
    color: #f59e0b !important;
}

.text-danger {
    color: #ef4444 !important;
}

.text-info {
    color: #06b6d4 !important;
}

.text-primary {
    color: #3b82f6 !important;
}

.alert {
    background-color: #374151 !important;
    border-color: #4b5563 !important;
    color: #d1d5db !important;
}

.alert-success {
    background-color: #064e3b !important;
    border-color: #10b981 !important;
    color: #d1d5db !important;
}

.alert-info {
    background-color: #164e63 !important;
    border-color: #06b6d4 !important;
    color: #d1d5db !important;
}

.alert-warning {
    background-color: #78350f !important;
    border-color: #f59e0b !important;
    color: #d1d5db !important;
}

.alert-danger {
    background-color: #7f1d1d !important;
    border-color: #ef4444 !important;
    color: #d1d5db !important;
}

.badge {
    background-color: #374151 !important;
    color: #d1d5db !important;
    border: 1px solid #4b5563 !important;
}

.badge.bg-primary {
    background-color: #1e40af !important;
    color: #ffffff !important;
}

.badge.bg-success {
    background-color: #059669 !important;
    color: #ffffff !important;
}

.badge.bg-warning {
    background-color: #d97706 !important;
    color: #ffffff !important;
}

.badge.bg-danger {
    background-color: #dc2626 !important;
    color: #ffffff !important;
}

.badge.bg-info {
    background-color: #0891b2 !important;
    color: #ffffff !important;
}

h1, h2, h3, h4, h5, h6 {
    color: #d1d5db !important;
}

p, div, span {
    color: #d1d5db !important;
}

small {
    color: #9ca3af !important;
}

strong {
    color: #f9fafb !important;
}

.fw-bold {
    color: #f9fafb !important;
}

.table {
    background-color: #1f2937 !important;
    color: #d1d5db !important;
}

.table th {
    background-color: #374151 !important;
    color: #d1d5db !important;
    border-color: #4b5563 !important;
}

.table td {
    background-color: #1f2937 !important;
    color: #d1d5db !important;
    border-color: #4b5563 !important;
}

.table-striped tbody tr:nth-of-type(odd) td {
    background-color: #374151 !important;
}

.table-hover tbody tr:hover td {
    background-color: #4b5563 !important;
}

/* Document items styling */
.document-item {
    background-color: #374151 !important;
    border: 1px solid #4b5563 !important;
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.document-item:hover {
    background-color: #4b5563 !important;
    border-color: #6b7280 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.document-item h6 {
    color: #d1d5db !important;
    font-weight: 600;
}

.document-item small {
    color: #9ca3af !important;
}

.document-item .btn {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.document-item .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}
</style>

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-key text-primary me-2"></i>Rental Code Details
                    </h2>
                    <p class="text-muted mb-0">View and manage rental code information</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('rental-codes.edit', $rentalCode) }}" class="btn transition-colors"
                       style="background: linear-gradient(135deg, #d97706, #f59e0b); border: 1px solid #f59e0b; color: #ffffff; text-decoration: none;"
                       onmouseover="this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.borderColor='#d97706';"
                       onmouseout="this.style.background='linear-gradient(135deg, #d97706, #f59e0b)'; this.style.borderColor='#f59e0b';">
                        <i class="fas fa-edit me-1"></i> Edit
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

    <!-- Status Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-{{ $rentalCode->status === 'paid' ? 'success' : ($rentalCode->status === 'approved' ? 'info' : 'warning') }} d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-{{ $rentalCode->status === 'paid' ? 'check-circle' : ($rentalCode->status === 'approved' ? 'thumbs-up' : 'clock') }} fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-0">Status: {{ ucfirst($rentalCode->status) }}</h5>
                        <small>Rental Code: <strong>{{ $rentalCode->rental_code }}</strong></small>
                    </div>
                </div>
                <div class="text-end">
                    <div class="h4 mb-0">£{{ number_format($rentalCode->consultation_fee, 2) }}</div>
                    <small>Consultation Fee</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Rental Information Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-key me-2"></i>Rental Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">Rental Code</div>
                        <div class="info-value">
                            <span class="badge bg-primary fs-6">{{ $rentalCode->rental_code }}</span>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Date</div>
                        <div class="info-value">
                            <i class="fas fa-calendar text-muted me-2"></i>
                            {{ $rentalCode->formatted_rental_date }}
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Consultation Fee</div>
                        <div class="info-value">
                            <i class="fas fa-pound-sign text-success me-2"></i>
                            <span class="fw-bold text-success">£{{ number_format($rentalCode->consultation_fee, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Payment Method</div>
                        <div class="info-value">
                            @php
                                $paymentMethod = $rentalCode->payment_method ?? 'N/A';
                                $emoji = '';
                                if (strtolower($paymentMethod) === 'transfer') {
                                    $emoji = '⚡';
                                } elseif (strtolower($paymentMethod) === 'card machine') {
                                    $emoji = '💳';
                                } elseif (strtolower($paymentMethod) === 'cash') {
                                    $emoji = '💰';
                                }
                            @endphp
                            <i class="fas fa-credit-card text-muted me-2"></i>
                            @if($emoji)
                                <span class="me-1">{{ $emoji }}</span>
                            @endif
                            {{ $paymentMethod }}
                        </div>
                    </div>
                    
                    @if($rentalCode->property)
                    <div class="info-item">
                        <div class="info-label">Property</div>
                        <div class="info-value">
                            <i class="fas fa-building text-muted me-2"></i>
                            {{ $rentalCode->property }}
                        </div>
                    </div>
                    @endif
                    
                    @if($rentalCode->licensor)
                    <div class="info-item">
                        <div class="info-label">Licensor</div>
                        <div class="info-value">
                            <i class="fas fa-user-tie text-muted me-2"></i>
                            {{ $rentalCode->licensor }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Client Information Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Client Information
                    </h5>
                </div>
                <div class="card-body">
                    @if($rentalCode->client)
                        <!-- Use client relationship data -->
                        <div class="info-item">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">
                                <i class="fas fa-user text-muted me-2"></i>
                                <strong>{{ $rentalCode->client->full_name }}</strong>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Date of Birth</div>
                            <div class="info-value">
                                <i class="fas fa-birthday-cake text-muted me-2"></i>
                                {{ $rentalCode->client->formatted_date_of_birth }}
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value">
                                <i class="fas fa-phone text-muted me-2"></i>
                                <a href="tel:{{ $rentalCode->client->phone_number }}" class="text-decoration-none">
                                    {{ $rentalCode->client->formatted_phone }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value">
                                <i class="fas fa-envelope text-muted me-2"></i>
                                <a href="mailto:{{ $rentalCode->client->email }}" class="text-decoration-none">
                                    {{ $rentalCode->client->email }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Nationality</div>
                            <div class="info-value">
                                <i class="fas fa-flag text-muted me-2"></i>
                                {{ $rentalCode->client->nationality }}
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Current Address</div>
                            <div class="info-value">
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                <div class="address-text">{{ $rentalCode->client->current_address }}</div>
                            </div>
                        </div>
                        
                        @if($rentalCode->client->company_university_name)
                        <div class="info-item">
                            <div class="info-label">Company/University</div>
                            <div class="info-value">
                                <i class="fas fa-building text-muted me-2"></i>
                                {{ $rentalCode->client->company_university_name }}
                            </div>
                        </div>
                        @endif
                        
                        @if($rentalCode->client->position_role)
                        <div class="info-item">
                            <div class="info-label">Position/Role</div>
                            <div class="info-value">
                                <i class="fas fa-briefcase text-muted me-2"></i>
                                {{ $rentalCode->client->position_role }}
                            </div>
                        </div>
                        @endif
                    @else
                        <!-- Fallback to old client fields for backward compatibility -->
                        <div class="info-item">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">
                                <i class="fas fa-user text-muted me-2"></i>
                                <strong>{{ $rentalCode->client_full_name }}</strong>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Date of Birth</div>
                            <div class="info-value">
                                <i class="fas fa-birthday-cake text-muted me-2"></i>
                                {{ $rentalCode->formatted_client_date_of_birth }}
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value">
                                <i class="fas fa-phone text-muted me-2"></i>
                                <a href="tel:{{ $rentalCode->client_phone_number }}" class="text-decoration-none">
                                    {{ $rentalCode->client_phone_number }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value">
                                <i class="fas fa-envelope text-muted me-2"></i>
                                <a href="mailto:{{ $rentalCode->client_email }}" class="text-decoration-none">
                                    {{ $rentalCode->client_email }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Nationality</div>
                            <div class="info-value">
                                <i class="fas fa-flag text-muted me-2"></i>
                                {{ $rentalCode->client_nationality }}
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Current Address</div>
                            <div class="info-value">
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                <div class="address-text">{{ $rentalCode->client_current_address }}</div>
                            </div>
                        </div>
                        
                        @if($rentalCode->client_company_university_name)
                        <div class="info-item">
                            <div class="info-label">Company/University</div>
                            <div class="info-value">
                                <i class="fas fa-building text-muted me-2"></i>
                                {{ $rentalCode->client_company_university_name }}
                            </div>
                        </div>
                        @endif
                        
                        @if($rentalCode->client_position_role)
                        <div class="info-item">
                            <div class="info-label">Position/Role</div>
                            <div class="info-value">
                                <i class="fas fa-briefcase text-muted me-2"></i>
                                {{ $rentalCode->client_position_role }}
                            </div>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Agent Information Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie me-2"></i>Agent Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">Assisted by</div>
                        <div class="info-value">
                            <i class="fas fa-user-tie text-muted me-2"></i>
                            <strong>{{ $rentalCode->rent_by_agent_name }}</strong>
                        </div>
                    </div>
                    
                    @if($rentalCode->marketingAgentUser)
                    <div class="info-item">
                        <div class="info-label">Marketing Agent</div>
                        <div class="info-value">
                            <i class="fas fa-bullhorn text-muted me-2"></i>
                            <strong>{{ $rentalCode->marketingAgentUser->name }}</strong>
                        </div>
                    </div>
                    @endif
                    
                    <div class="info-item">
                        <div class="info-label">Created</div>
                        <div class="info-value">
                            <i class="fas fa-calendar-plus text-muted me-2"></i>
                            {{ $rentalCode->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Last Updated</div>
                        <div class="info-value">
                            <i class="fas fa-calendar-edit text-muted me-2"></i>
                            {{ $rentalCode->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    
                    @if($rentalCode->notes)
                    <div class="info-item">
                        <div class="info-label">Notes</div>
                        <div class="info-value">
                            <i class="fas fa-sticky-note text-muted me-2"></i>
                            <div class="notes-text">{{ $rentalCode->notes }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i>Available Documents
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Client Contract -->
                        @php
                            $clientContracts = null;
                            if ($rentalCode->client_contract) {
                                // Handle both new format (JSON array) and old format (single string) for backward compatibility
                                if (is_string($rentalCode->client_contract)) {
                                    $decoded = json_decode($rentalCode->client_contract, true);
                                    // If JSON decode was successful and it's an array, use it (new format)
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $clientContracts = $decoded;
                                    } else {
                                        // If it's a single string path (old format), convert to array for consistent display
                                        $clientContracts = [$rentalCode->client_contract];
                                    }
                                } elseif (is_array($rentalCode->client_contract)) {
                                    // Already an array (new format)
                                    $clientContracts = $rentalCode->client_contract;
                                } else {
                                    // Fallback: wrap in array for backward compatibility
                                    $clientContracts = [$rentalCode->client_contract];
                                }
                            }
                        @endphp
                        @if($clientContracts && is_array($clientContracts) && count($clientContracts) > 0)
                        <div class="col-md-4 mb-3">
                            <div class="document-item">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-file-contract text-primary me-3 fs-4"></i>
                                    <div>
                                        <h6 class="mb-1">Client Contract</h6>
                                        <small class="text-muted">{{ count($clientContracts) }} document(s)</small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    @foreach($clientContracts as $index => $contract)
                                        @if($contract && is_string($contract) && !empty(trim($contract)))
                                            <div class="mb-2">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="text-muted me-2" style="font-size: 0.875rem;">Document {{ $index + 1 }}:</span>
                                                    <span class="text-info" style="font-size: 0.875rem;">{{ basename($contract) }}</span>
                                                </div>
                                                <div>
                                                    <a href="{{ route('rental-codes.view-file', ['rentalCode' => $rentalCode->id, 'field' => 'client_contract', 'index' => $index]) }}" 
                                                       target="_blank" 
                                                       class="btn btn-outline-primary btn-sm me-2" 
                                                       style="border-color: #3b82f6 !important; color: #3b82f6 !important; background-color: transparent !important;">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                    <a href="{{ route('rental-codes.download-file', ['rentalCode' => $rentalCode->id, 'field' => 'client_contract', 'index' => $index]) }}" 
                                                       class="btn btn-outline-secondary btn-sm" 
                                                       style="border-color: #6b7280 !important; color: #d1d5db !important; background-color: transparent !important;">
                                                        <i class="fas fa-download me-1"></i>Download
                                                    </a>
                                                </div>
                                            </div>
                                            @if(!$loop->last)
                                                <hr style="border-color: #4b5563; margin: 0.75rem 0;">
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Payment Proof -->
                        @if($rentalCode->payment_proof && !empty($rentalCode->payment_proof))
                        <div class="col-md-4 mb-3">
                            <div class="document-item">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-receipt text-success me-3 fs-4"></i>
                                    <div>
                                        <h6 class="mb-1">Payment Proof</h6>
                                        <small class="text-muted">Payment receipt</small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('rental-codes.view-file', ['rentalCode' => $rentalCode->id, 'field' => 'payment_proof']) }}" 
                                       target="_blank" 
                                       class="btn btn-outline-success btn-sm me-2">
                                        <i class="fas fa-eye me-1"></i>View Document
                                    </a>
                                    <a href="{{ route('rental-codes.download-file', ['rentalCode' => $rentalCode->id, 'field' => 'payment_proof']) }}" 
                                       class="btn btn-outline-secondary btn-sm me-2">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Client ID Document -->
                        @if($rentalCode->client_id_document && !empty($rentalCode->client_id_document))
                        <div class="col-md-4 mb-3">
                            <div class="document-item">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-id-card text-info me-3 fs-4"></i>
                                    <div>
                                        <h6 class="mb-1">Client ID Document</h6>
                                        <small class="text-muted">Identity verification</small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('rental-codes.view-file', ['rentalCode' => $rentalCode->id, 'field' => 'client_id_document']) }}" 
                                       target="_blank" 
                                       class="btn btn-outline-info btn-sm me-2">
                                        <i class="fas fa-eye me-1"></i>View Document
                                    </a>
                                    <a href="{{ route('rental-codes.download-file', ['rentalCode' => $rentalCode->id, 'field' => 'client_id_document']) }}" 
                                       class="btn btn-outline-secondary btn-sm me-2">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Client ID Image -->
                        @if($rentalCode->client_id_image)
                        <div class="col-md-4 mb-3">
                            <div class="document-item">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-image text-warning me-3 fs-4"></i>
                                    <div>
                                        <h6 class="mb-1">Client ID Image</h6>
                                        <small class="text-muted">ID photo</small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('rental-codes.view-file', ['rentalCode' => $rentalCode->id, 'field' => 'client_id_image']) }}" 
                                       target="_blank" 
                                       class="btn btn-outline-warning btn-sm me-2">
                                        <i class="fas fa-eye me-1"></i>View Image
                                    </a>
                                    <a href="{{ route('rental-codes.download-file', ['rentalCode' => $rentalCode->id, 'field' => 'client_id_image']) }}" 
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Cash Receipt Image -->
                        @if($rentalCode->cash_receipt_image)
                        <div class="col-md-4 mb-3">
                            <div class="document-item">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-money-bill-wave text-success me-3 fs-4"></i>
                                    <div>
                                        <h6 class="mb-1">Cash Receipt</h6>
                                        <small class="text-muted">Cash payment receipt</small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('rental-codes.view-file', ['rentalCode' => $rentalCode->id, 'field' => 'cash_receipt_image']) }}" 
                                       target="_blank" 
                                       class="btn btn-outline-success btn-sm me-2">
                                        <i class="fas fa-eye me-1"></i>View Receipt
                                    </a>
                                    <a href="{{ route('rental-codes.download-file', ['rentalCode' => $rentalCode->id, 'field' => 'cash_receipt_image']) }}" 
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Contact Images -->
                        @php
                            $contactImages = null;
                            if ($rentalCode->contact_images) {
                                $contactImages = is_string($rentalCode->contact_images) ? json_decode($rentalCode->contact_images, true) : $rentalCode->contact_images;
                            }
                        @endphp
                        @if($contactImages && is_array($contactImages) && count($contactImages) > 0)
                        <div class="col-md-4 mb-3">
                            <div class="document-item">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-images text-secondary me-3 fs-4"></i>
                                    <div>
                                        <h6 class="mb-1">Contact Images</h6>
                                        <small class="text-muted">{{ count($contactImages) }} image(s)</small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    @foreach($contactImages as $index => $image)
                                        @if($image && is_string($image))
                                            <a href="{{ route('rental-codes.view-file', ['rentalCode' => $rentalCode->id, 'field' => 'contact_images', 'index' => $index]) }}" 
                                               target="_blank" 
                                               class="btn btn-outline-secondary btn-sm me-1 mb-1">
                                                <i class="fas fa-eye me-1"></i>View {{ $index + 1 }}
                                            </a>
                                            <a href="{{ route('rental-codes.download-file', ['rentalCode' => $rentalCode->id, 'field' => 'contact_images', 'index' => $index]) }}" 
                                               class="btn btn-outline-dark btn-sm me-1 mb-1">
                                                <i class="fas fa-download me-1"></i>Download {{ $index + 1 }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- No Documents Message -->
                    @php
                        $hasDocuments = false;
                        // Check client_contract (can be array or string)
                        if ($rentalCode->client_contract) {
                            $contracts = is_string($rentalCode->client_contract) ? json_decode($rentalCode->client_contract, true) : $rentalCode->client_contract;
                            if (is_array($contracts) && count($contracts) > 0) {
                                $hasDocuments = true;
                            } elseif (is_string($rentalCode->client_contract) && !empty($rentalCode->client_contract)) {
                                $hasDocuments = true;
                            }
                        }
                        // Check other single file fields
                        if ($rentalCode->payment_proof || $rentalCode->client_id_document || 
                            $rentalCode->client_id_image || $rentalCode->cash_receipt_image) {
                            $hasDocuments = true;
                        }
                        // Check contact_images array
                        if ($rentalCode->contact_images) {
                            $contactImages = is_string($rentalCode->contact_images) ? json_decode($rentalCode->contact_images, true) : $rentalCode->contact_images;
                            if ($contactImages && is_array($contactImages) && count($contactImages) > 0) {
                                $hasDocuments = true;
                            }
                        }
                    @endphp
                    @if(!$hasDocuments)
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt text-muted fs-1 mb-3"></i>
                        <h5 class="text-muted">No Documents Available</h5>
                        <p class="text-muted">No documents have been uploaded for this rental code.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    @if($rentalCode->client_company_university_address)
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>Company/University Address
                    </h5>
                </div>
                <div class="card-body">
                    <div class="address-block">
                        <i class="fas fa-building text-muted me-2"></i>
                        {{ $rentalCode->client_company_university_address }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif


    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-outline-primary w-100" onclick="printDetails()">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-outline-success w-100" onclick="exportToPDF()">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-outline-info w-100" onclick="sendEmail()">
                                <i class="fas fa-envelope me-1"></i> Send Email
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-outline-warning w-100" onclick="duplicateRecord()">
                                <i class="fas fa-copy me-1"></i> Duplicate
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('rental-codes.edit', $rentalCode) }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-file-upload me-1"></i> Manage Documents
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-item {
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.info-label {
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 1rem;
    color: #495057;
    display: flex;
    align-items: center;
}

.address-text, .notes-text {
    word-wrap: break-word;
    white-space: pre-wrap;
}

.address-block {
    font-size: 1rem;
    color: #495057;
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 0.375rem;
    border-left: 4px solid #007bff;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.card-header {
    border-bottom: 1px solid #e3e6f0;
    font-weight: 600;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
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
    .info-value {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .info-value i {
        margin-bottom: 0.25rem;
    }
}
</style>

<script>
function printDetails() {
    window.print();
}

function exportToPDF() {
    // Implement PDF export functionality
    alert('PDF export functionality would be implemented here');
}

function sendEmail() {
    // Implement email functionality
    const email = '{{ $rentalCode->client_email }}';
    const subject = 'Rental Code: {{ $rentalCode->rental_code }}';
    const body = `Dear {{ $rentalCode->client_full_name }},\n\nYour rental code {{ $rentalCode->rental_code }} has been processed.\n\nConsultation Fee: £{{ number_format($rentalCode->consultation_fee, 2) }}\nPayment Method: {{ $rentalCode->payment_method }}\n\nThank you for your business.`;
    
    window.location.href = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
}

function duplicateRecord() {
    if (confirm('Create a duplicate of this rental code?')) {
        // Implement duplication logic
        window.location.href = '{{ route("rental-codes.create") }}?duplicate={{ $rentalCode->id }}';
    }
}

// Add some interactive features
document.addEventListener('DOMContentLoaded', function() {
    // Add click-to-copy functionality for important fields
    const copyableFields = document.querySelectorAll('.info-value');
    copyableFields.forEach(field => {
        field.style.cursor = 'pointer';
        field.title = 'Click to copy';
        
        field.addEventListener('click', function() {
            const text = this.textContent.trim();
            if (text && text !== 'Not specified') {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(() => {
                        // Show temporary feedback
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check text-success me-2"></i>Copied!';
                        setTimeout(() => {
                            this.innerHTML = originalText;
                        }, 1000);
                    }).catch(() => {
                        // Fallback for older browsers
                    });
                }
            }
        });
    });
});
</script>
@endsection