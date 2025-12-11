@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Landlord Bonuses</li>
                    </ol>
                </div>
                <h4 class="page-title">Landlord Bonuses</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Landlord Bonus Records</h5>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-warning me-2" id="bulkUpdateStatusBtn" style="display: none;" data-bs-toggle="modal" data-bs-target="#bulkStatusModal">
                                <i class="fas fa-edit me-1"></i> Update Status
                            </button>
                            <button type="button" class="btn btn-success me-2" id="generateInvoiceBtn" style="display: none;" data-bs-toggle="modal" data-bs-target="#invoiceRecipientModal">
                                <i class="fas fa-file-invoice me-1"></i> Generate Invoice
                            </button>
                            <a href="{{ route('landlord-bonuses.export') }}" class="btn btn-info me-2">
                                <i class="fas fa-file-csv me-1"></i> Export CSV
                            </a>
                            <a href="{{ route('landlord-bonuses.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add Landlord Bonus
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAllBonuses" class="form-check-input">
                                    </th>
                                    <th>Bonus Code</th>
                                    <th>Date</th>
                                    <th>Agent</th>
                                    <th>Landlord</th>
                                    <th>Property</th>
                                    <th>Client</th>
                                    <th>Commission</th>
                                    <th>Agent Split</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($landlordBonuses as $bonus)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input bonus-checkbox" value="{{ $bonus->id }}" data-bonus-id="{{ $bonus->id }}">
                                    </td>
                                    <td>
                                        <span class="badge bg-primary fs-6">{{ $bonus->bonus_code }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $bonus->date->format('M d, Y') }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user-tie text-muted"></i>
                                            </div>
                                            <span>{{ $bonus->agent->user->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $bonus->landlord }}</span>
                                    </td>
                                    <td>
                                        <span class="text-primary">{{ $bonus->property }}</span>
                                    </td>
                                    <td>
                                        <span class="text-success">{{ $bonus->client }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">£{{ number_format($bonus->commission, 2) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-primary">£{{ number_format($bonus->agent_commission, 2) }}</span>
                                            <small class="text-muted">
                                                @if($bonus->bonus_split === '100_0')
                                                    100% Agent
                                                @else
                                                    55% Agent
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusConfig = [
                                                'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Pending'],
                                                'sent' => ['class' => 'info', 'icon' => 'paper-plane', 'text' => 'Sent'],
                                                'paid' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Paid'],
                                                'cancelled' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Cancelled']
                                            ];
                                            $config = $statusConfig[$bonus->status] ?? $statusConfig['pending'];
                                        @endphp
                                        <span class="badge bg-{{ $config['class'] }}">
                                            <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                            {{ $config['text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route('landlord-bonuses.show', $bonus) }}" class="dropdown-item">
                                                        <i class="fas fa-eye me-2"></i>View
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('landlord-bonuses.edit', $bonus) }}" class="dropdown-item">
                                                        <i class="fas fa-edit me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('landlord-bonuses.destroy', $bonus) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this landlord bonus?')">
                                                            <i class="fas fa-trash me-2"></i>Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-gift fa-3x mb-3"></i>
                                            <h5>No Landlord Bonuses Found</h5>
                                            <p>Start by adding your first landlord bonus record.</p>
                                            <a href="{{ route('landlord-bonuses.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i> Add Landlord Bonus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($landlordBonuses->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $landlordBonuses->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Status Update Modal -->
<div class="modal fade" id="bulkStatusModal" tabindex="-1" aria-labelledby="bulkStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkStatusModalLabel">
                    <i class="fas fa-edit me-2"></i>Update Status for Selected Bonuses
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkStatusUpdateForm" method="POST" action="{{ route('landlord-bonuses.bulk-update-status') }}">
                @csrf
                <input type="hidden" name="bonus_ids" id="bulkStatusBonusIds" value="">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You are about to update the status of <strong><span id="selectedBonusCount">0</span> bonus(es)</strong>.
                    </div>
                    <div class="mb-3">
                        <label for="bulk_status" class="form-label">
                            New Status <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="bulk_status" name="status" required>
                            <option value="">-- Select Status --</option>
                            <option value="pending">
                                <i class="fas fa-clock"></i> Pending
                            </option>
                            <option value="sent">
                                <i class="fas fa-paper-plane"></i> Sent
                            </option>
                            <option value="paid">
                                <i class="fas fa-check-circle"></i> Paid
                            </option>
                            <option value="cancelled">
                                <i class="fas fa-times-circle"></i> Cancelled
                            </option>
                        </select>
                        <small class="form-text text-muted">All selected bonuses will be updated to this status</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-check me-1"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Invoice Recipient Modal -->
<div class="modal fade" id="invoiceRecipientModal" tabindex="-1" aria-labelledby="invoiceRecipientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceRecipientModalLabel">
                    <i class="fas fa-file-invoice me-2"></i>Generate Invoice - Recipient Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="generateInvoiceForm" method="POST" action="{{ route('landlord-bonuses.generate-invoice') }}">
                @csrf
                <input type="hidden" name="bonus_ids" id="selectedBonusIds" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="recipient_name" class="form-label">
                            Recipient Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="recipient_name" name="recipient_name" required placeholder="Enter recipient name">
                        <small class="form-text text-muted">The name of the person or company receiving the invoice</small>
                    </div>
                    <div class="mb-3">
                        <label for="recipient_address" class="form-label">
                            Recipient Address <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="recipient_address" name="recipient_address" rows="4" required placeholder="Enter recipient address"></textarea>
                        <small class="form-text text-muted">Full address of the recipient</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="recipient_email" class="form-label">Recipient Email</label>
                            <input type="email" class="form-control" id="recipient_email" name="recipient_email" placeholder="Enter recipient email (optional)">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="recipient_phone" class="form-label">Recipient Phone</label>
                            <input type="text" class="form-control" id="recipient_phone" name="recipient_phone" placeholder="Enter recipient phone (optional)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-invoice me-1"></i> Generate Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllBonuses');
    const bonusCheckboxes = document.querySelectorAll('.bonus-checkbox');
    const generateInvoiceForm = document.getElementById('generateInvoiceForm');
    const generateInvoiceBtn = document.getElementById('generateInvoiceBtn');
    const bulkUpdateStatusBtn = document.getElementById('bulkUpdateStatusBtn');
    const selectedBonusIdsInput = document.getElementById('selectedBonusIds');
    const invoiceRecipientModal = document.getElementById('invoiceRecipientModal');
    const bulkStatusModal = document.getElementById('bulkStatusModal');
    const bulkStatusUpdateForm = document.getElementById('bulkStatusUpdateForm');
    const bulkStatusBonusIds = document.getElementById('bulkStatusBonusIds');
    const selectedBonusCount = document.getElementById('selectedBonusCount');

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            bonusCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionButtons();
        });
    }

    // Individual checkbox change
    bonusCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateBulkActionButtons();
        });
    });

    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.bonus-checkbox:checked');
        if (selectAllCheckbox) {
            if (checkedBoxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedBoxes.length === bonusCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }
    }

    function updateBulkActionButtons() {
        const checkedBoxes = document.querySelectorAll('.bonus-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (selectedIds.length > 0) {
            // Show both bulk action buttons
            generateInvoiceBtn.style.display = 'inline-block';
            bulkUpdateStatusBtn.style.display = 'inline-block';
            
            // Update hidden inputs and count
            selectedBonusIdsInput.value = JSON.stringify(selectedIds);
            bulkStatusBonusIds.value = JSON.stringify(selectedIds);
            if (selectedBonusCount) {
                selectedBonusCount.textContent = selectedIds.length;
            }
        } else {
            // Hide both bulk action buttons
            generateInvoiceBtn.style.display = 'none';
            bulkUpdateStatusBtn.style.display = 'none';
            selectedBonusIdsInput.value = '';
            bulkStatusBonusIds.value = '';
        }
    }

    // Reset invoice form when modal is closed
    if (invoiceRecipientModal) {
        invoiceRecipientModal.addEventListener('hidden.bs.modal', function() {
            generateInvoiceForm.reset();
        });
    }

    // Reset bulk status form when modal is closed
    if (bulkStatusModal) {
        bulkStatusModal.addEventListener('hidden.bs.modal', function() {
            bulkStatusUpdateForm.reset();
        });
    }

    // Invoice form submission - ensure bonus IDs are set
    if (generateInvoiceForm) {
        generateInvoiceForm.addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.bonus-checkbox:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one bonus to generate an invoice.');
                return false;
            }
            
            // Ensure bonus IDs are set before submission
            const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
            selectedBonusIdsInput.value = JSON.stringify(selectedIds);
        });
    }

    // Bulk status form submission - ensure bonus IDs and status are set
    if (bulkStatusUpdateForm) {
        bulkStatusUpdateForm.addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.bonus-checkbox:checked');
            const statusSelect = document.getElementById('bulk_status');
            
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one bonus to update.');
                return false;
            }
            
            if (!statusSelect.value) {
                e.preventDefault();
                alert('Please select a status.');
                return false;
            }
            
            // Ensure bonus IDs are set before submission
            const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
            bulkStatusBonusIds.value = JSON.stringify(selectedIds);
        });
    }
});
</script>
@endpush
@endsection
