@extends('layouts.admin')

@section('title', 'Rental Code Details')

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
                    <a href="{{ route('rental-codes.edit', $rentalCode) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <a href="{{ route('rental-codes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-{{ $rentalCode->status === 'completed' ? 'success' : ($rentalCode->status === 'approved' ? 'info' : ($rentalCode->status === 'cancelled' ? 'danger' : 'warning')) }} d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-{{ $rentalCode->status === 'completed' ? 'check-circle' : ($rentalCode->status === 'approved' ? 'thumbs-up' : ($rentalCode->status === 'cancelled' ? 'times-circle' : 'clock')) }} fa-2x me-3"></i>
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
                        <div class="info-label">Rental Date</div>
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
                            <i class="fas fa-credit-card text-muted me-2"></i>
                            {{ $rentalCode->payment_method }}
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
                        <div class="info-label">Rent By Agent</div>
                        <div class="info-value">
                            <i class="fas fa-user-tie text-muted me-2"></i>
                            <strong>{{ $rentalCode->rent_by_agent_name }}</strong>
                        </div>
                    </div>
                    
                    
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
                        console.log('Copy failed, text:', text);
                    });
                }
            }
        });
    });
});
</script>
@endsection