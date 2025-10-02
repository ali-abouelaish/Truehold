@extends('layouts.admin')

@section('title', 'Rental Codes')

<style>
/* Dark mode styles for rental codes index page */
.card {
    background-color: #1f2937 !important;
    border-color: #374151 !important;
}

.card-header {
    background-color: #374151 !important;
    border-color: #4b5563 !important;
    color: #d1d5db !important;
}

.card-body {
    background-color: #1f2937 !important;
    color: #d1d5db !important;
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

/* Client name styling - make it white */
.fw-bold {
    color: #ffffff !important;
    font-weight: 700 !important;
}

.text-muted {
    color: #9ca3af !important;
}

/* Action button colors */
.btn-outline-primary {
    background: linear-gradient(135deg, #1e40af, #3b82f6) !important;
    border: 1px solid #3b82f6 !important;
    color: #ffffff !important;
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
}

.btn-outline-info {
    background: linear-gradient(135deg, #0891b2, #06b6d4) !important;
    border: 1px solid #06b6d4 !important;
    color: #ffffff !important;
}

.btn-outline-info:hover {
    background: linear-gradient(135deg, #06b6d4, #0891b2) !important;
    border-color: #0891b2 !important;
    color: #ffffff !important;
}

.btn-outline-warning {
    background: linear-gradient(135deg, #d97706, #f59e0b) !important;
    border: 1px solid #f59e0b !important;
    color: #ffffff !important;
}

.btn-outline-warning:hover {
    background: linear-gradient(135deg, #f59e0b, #d97706) !important;
    border-color: #d97706 !important;
    color: #ffffff !important;
}

.btn-outline-danger {
    background: linear-gradient(135deg, #dc2626, #ef4444) !important;
    border: 1px solid #ef4444 !important;
    color: #ffffff !important;
}

.btn-outline-danger:hover {
    background: linear-gradient(135deg, #ef4444, #dc2626) !important;
    border-color: #dc2626 !important;
    color: #ffffff !important;
}

/* Status badges */
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

/* Avatar styling */
.avatar-sm {
    background-color: #374151 !important;
    color: #9ca3af !important;
}

/* Form elements */
.form-check-input {
    background-color: #374151 !important;
    border-color: #4b5563 !important;
}

.form-check-input:checked {
    background-color: #fbbf24 !important;
    border-color: #fbbf24 !important;
}

/* Headers and text - make all text lighter */
h1, h2, h3, h4, h5, h6 {
    color: #f9fafb !important;
}

p, div, span {
    color: #f9fafb !important;
}

small {
    color: #d1d5db !important;
}

strong {
    color: #ffffff !important;
}

/* Specific text elements that need to be lighter */
.text-gray-800 {
    color: #f9fafb !important;
}

.text-gray-700 {
    color: #d1d5db !important;
}

.text-gray-600 {
    color: #d1d5db !important;
}

.text-gray-500 {
    color: #9ca3af !important;
}

.text-gray-400 {
    color: #9ca3af !important;
}

.text-gray-300 {
    color: #d1d5db !important;
}

.text-gray-200 {
    color: #f9fafb !important;
}

.text-gray-100 {
    color: #ffffff !important;
}

/* Table text elements */
.table td, .table th {
    color: #f9fafb !important;
}

.table td small {
    color: #d1d5db !important;
}

/* Card text */
.card-body {
    color: #f9fafb !important;
}

.card-body p {
    color: #f9fafb !important;
}

.card-body div {
    color: #f9fafb !important;
}

.card-body span {
    color: #f9fafb !important;
}

/* Stats cards text */
.card-body .text-xs {
    color: #d1d5db !important;
}

.card-body .text-sm {
    color: #f9fafb !important;
}

.card-body .text-lg {
    color: #ffffff !important;
}

.card-body .text-xl {
    color: #ffffff !important;
}

.card-body .text-2xl {
    color: #ffffff !important;
}

.card-body .text-3xl {
    color: #ffffff !important;
}

/* Form text */
.form-label {
    color: #d1d5db !important;
}

.form-text {
    color: #9ca3af !important;
}

/* Button text */
.btn {
    color: #ffffff !important;
}

.btn-outline-primary {
    color: #ffffff !important;
}

.btn-outline-info {
    color: #ffffff !important;
}

.btn-outline-warning {
    color: #ffffff !important;
}

.btn-outline-danger {
    color: #ffffff !important;
}

/* Badge text */
.badge {
    color: #ffffff !important;
}

/* Alert text */
.alert {
    color: #f9fafb !important;
}

.alert p {
    color: #f9fafb !important;
}

.alert div {
    color: #f9fafb !important;
}

.alert span {
    color: #f9fafb !important;
}

/* Pagination text */
.pagination {
    color: #f9fafb !important;
}

.pagination a {
    color: #f9fafb !important;
}

.pagination span {
    color: #f9fafb !important;
}

/* Dropdown text */
.dropdown-menu {
    background-color: #374151 !important;
    color: #f9fafb !important;
}

.dropdown-item {
    color: #f9fafb !important;
}

.dropdown-item:hover {
    background-color: #4b5563 !important;
    color: #ffffff !important;
}

/* Specific client name styling - database queried elements */
.d-flex .fw-bold {
    color: #ffffff !important;
    font-weight: 700 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
}

/* Database queried text elements - ensure client name is visible */
.fw-bold {
    color: #ffffff !important;
    font-weight: 700 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) !important;
}

/* Specific styling for client name in table */
.table td .fw-bold {
    color: #ffffff !important;
    font-weight: 700 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) !important;
    font-size: 1rem !important;
}

/* Ensure client name div is properly styled */
.table td div .fw-bold {
    color: #ffffff !important;
    font-weight: 700 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) !important;
}

/* Client email from database */
.text-muted {
    color: #9ca3af !important;
}

/* Agent name from database */
.avatar-sm + span {
    color: #f9fafb !important;
}

/* Rental date from database */
.text-muted {
    color: #9ca3af !important;
}

/* Consultation fee from database */
.fw-bold.text-success {
    color: #10b981 !important;
    font-weight: 700 !important;
}

/* Status badge text from database */
.badge {
    color: #ffffff !important;
    font-weight: 600 !important;
}

/* Table cell content from database */
.table td {
    color: #f9fafb !important;
}

.table td .fw-bold {
    color: #ffffff !important;
    font-weight: 700 !important;
}

.table td .text-muted {
    color: #9ca3af !important;
}

.table td .text-success {
    color: #10b981 !important;
}

/* Pagination text from database */
.pagination .page-link {
    color: #f9fafb !important;
    background-color: #374151 !important;
    border-color: #4b5563 !important;
}

.pagination .page-link:hover {
    color: #ffffff !important;
    background-color: #4b5563 !important;
    border-color: #6b7280 !important;
}

.pagination .page-item.active .page-link {
    color: #1f2937 !important;
    background-color: #fbbf24 !important;
    border-color: #fbbf24 !important;
}

/* Stats numbers from database */
.h5.mb-0.font-weight-bold {
    color: #ffffff !important;
    font-weight: 700 !important;
}

.text-xs.font-weight-bold {
    color: #d1d5db !important;
    font-weight: 600 !important;
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
                        <i class="fas fa-key text-primary me-2"></i>Rental Codes Management
                    </h2>
                    <p class="text-muted mb-0">Manage and track all rental code applications</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('rental-codes.agent-earnings') }}" class="btn transition-colors"
                       style="background: linear-gradient(135deg, #fbbf24, #f59e0b); border: 2px solid #fbbf24; color: #1f2937; font-weight: 600; padding: 12px 24px; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);"
                       onmouseover="this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.borderColor='#f59e0b'; this.style.boxShadow='0 6px 16px rgba(251, 191, 36, 0.4)'; this.style.transform='translateY(-2px)';"
                       onmouseout="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.borderColor='#fbbf24'; this.style.boxShadow='0 4px 12px rgba(251, 191, 36, 0.3)'; this.style.transform='translateY(0)';">
                        <i class="fas fa-chart-line me-2"></i> Agent Earnings Report
                    </a>
                    <a href="{{ route('rental-codes.create') }}" class="btn transition-colors"
                       style="background: linear-gradient(135deg, #10b981, #059669); border: 2px solid #10b981; color: #ffffff; font-weight: 600; padding: 12px 24px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);"
                       onmouseover="this.style.background='linear-gradient(135deg, #059669, #047857)'; this.style.borderColor='#059669'; this.style.boxShadow='0 6px 16px rgba(16, 185, 129, 0.4)'; this.style.transform='translateY(-2px)';"
                       onmouseout="this.style.background='linear-gradient(135deg, #10b981, #059669)'; this.style.borderColor='#10b981'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'; this.style.transform='translateY(0)';">
                        <i class="fas fa-plus me-2"></i> Add New Rental Code
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Codes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rentalCodes->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-key fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rentalCodes->where('status', 'completed')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rentalCodes->where('status', 'pending')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Approved</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rentalCodes->where('status', 'approved')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-thumbs-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Rental Codes List</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="exportToCSV()">
                                <i class="fas fa-download fa-sm fa-fw text-gray-400"></i> Export to CSV
                            </a>
                            <a class="dropdown-item" href="#" onclick="printTable()">
                                <i class="fas fa-print fa-sm fa-fw text-gray-400"></i> Print
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Search and Filter Bar -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Search rental codes...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="dateFilter" placeholder="Filter by date">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>

                    @if($rentalCodes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover" id="rentalCodesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Rental Code</th>
                                        <th>Client Name</th>
                                        <th>Rental Date</th>
                                        <th>Consultation Fee</th>
                                        <th>Status</th>
                                        <th>Agent</th>
                                        <th>Quick Actions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rentalCodes as $rentalCode)
                                        <tr data-status="{{ $rentalCode->status }}" data-date="{{ $rentalCode->rental_date }}">
                                            <td>
                                                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $rentalCode->id }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-primary me-2">{{ $rentalCode->rental_code }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fas fa-user text-muted"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $rentalCode->client->full_name ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $rentalCode->formatted_rental_date }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">£{{ number_format($rentalCode->consultation_fee, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $rentalCode->status === 'completed' ? 'success' : ($rentalCode->status === 'approved' ? 'info' : ($rentalCode->status === 'cancelled' ? 'danger' : 'warning')) }} px-3 py-2">
                                                    <i class="fas fa-{{ $rentalCode->status === 'completed' ? 'check' : ($rentalCode->status === 'approved' ? 'thumbs-up' : ($rentalCode->status === 'cancelled' ? 'times' : 'clock')) }} me-1"></i>
                                                    {{ ucfirst($rentalCode->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fas fa-user-tie text-muted"></i>
                                                    </div>
                                                    <span>{{ $rentalCode->rent_by_agent_name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm transition-colors" 
                                                            onclick="toggleStatus({{ $rentalCode->id }}, '{{ $rentalCode->status }}')" 
                                                            title="Change Status"
                                                            style="background: linear-gradient(135deg, #374151, #4b5563); border: 1px solid #6b7280; color: #d1d5db;"
                                                            onmouseover="this.style.background='linear-gradient(135deg, #4b5563, #6b7280)'; this.style.borderColor='#fbbf24'; this.style.color='#f9fafb';"
                                                            onmouseout="this.style.background='linear-gradient(135deg, #374151, #4b5563)'; this.style.borderColor='#6b7280'; this.style.color='#d1d5db';">
                                                        <i class="fas fa-toggle-on"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('rental-codes.show', $rentalCode) }}" class="btn btn-sm transition-colors" title="View Details"
                                                       style="background: linear-gradient(135deg, #1e40af, #3b82f6); border: 1px solid #3b82f6; color: #ffffff; text-decoration: none;"
                                                       onmouseover="this.style.background='linear-gradient(135deg, #3b82f6, #2563eb)'; this.style.borderColor='#2563eb';"
                                                       onmouseout="this.style.background='linear-gradient(135deg, #1e40af, #3b82f6)'; this.style.borderColor='#3b82f6';">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('rental-codes.edit', $rentalCode) }}" class="btn btn-sm transition-colors" title="Edit"
                                                       style="background: linear-gradient(135deg, #d97706, #f59e0b); border: 1px solid #f59e0b; color: #ffffff; text-decoration: none;"
                                                       onmouseover="this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.borderColor='#d97706';"
                                                       onmouseout="this.style.background='linear-gradient(135deg, #d97706, #f59e0b)'; this.style.borderColor='#f59e0b';">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm transition-colors" title="Delete" onclick="confirmDelete({{ $rentalCode->id }})"
                                                            style="background: linear-gradient(135deg, #dc2626, #ef4444); border: 1px solid #ef4444; color: #ffffff;"
                                                            onmouseover="this.style.background='linear-gradient(135deg, #ef4444, #dc2626)'; this.style.borderColor='#dc2626';"
                                                            onmouseout="this.style.background='linear-gradient(135deg, #dc2626, #ef4444)'; this.style.borderColor='#ef4444';">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Bulk Actions -->
                        <div class="row mt-3" id="bulkActions" style="display: none;">
                            <div class="col-12">
                                <div class="alert alert-info d-flex justify-content-between align-items-center">
                                    <span><span id="selectedCount">0</span> items selected</span>
                                    <div>
                                        <button class="btn btn-sm btn-success me-2" onclick="bulkAction('approve')">
                                            <i class="fas fa-thumbs-up"></i> Approve
                                        </button>
                                        <button class="btn btn-sm btn-warning me-2" onclick="bulkAction('complete')">
                                            <i class="fas fa-check"></i> Complete
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="bulkAction('cancel')">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Showing {{ $rentalCodes->firstItem() }} to {{ $rentalCodes->lastItem() }} of {{ $rentalCodes->total() }} results
                            </div>
                            <div>
                                {{ $rentalCodes->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-key fa-4x text-muted"></i>
                            </div>
                            <h4 class="text-muted mb-3">No Rental Codes Found</h4>
                            <p class="text-muted mb-4">Get started by creating your first rental code.</p>
                            <div>
                                <a href="{{ route('rental-codes.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Create Rental Code
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this rental code? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #5a5c69;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    border-radius: 0.35rem;
}

.btn-group .btn:not(:last-child) {
    margin-right: 2px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#rentalCodesTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // Status filter
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const status = this.value;
            const rows = document.querySelectorAll('#rentalCodesTable tbody tr');
            
            rows.forEach(row => {
                if (!status || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Date filter
    const dateFilter = document.getElementById('dateFilter');
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            const date = this.value;
            const rows = document.querySelectorAll('#rentalCodesTable tbody tr');
            
            rows.forEach(row => {
                if (!date || row.dataset.date === date) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Individual checkbox change
    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

// Clear filters
function clearFilters() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    
    if (searchInput) searchInput.value = '';
    if (statusFilter) statusFilter.value = '';
    if (dateFilter) dateFilter.value = '';
    
    const rows = document.querySelectorAll('#rentalCodesTable tbody tr');
    rows.forEach(row => {
        row.style.display = '';
    });
}

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    if (selectedCount) selectedCount.textContent = checkedBoxes.length;
    if (bulkActions) bulkActions.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
}

// Delete confirmation
function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    if (form) {
        form.action = `/admin/rental-codes/${id}`;
        const modal = document.getElementById('deleteModal');
        if (modal && typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modal).show();
        }
    }
}

// Bulk actions
function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (ids.length === 0) return;
    
    if (confirm(`Are you sure you want to ${action} ${ids.length} rental code(s)?`)) {
        // Implement bulk action logic here
    }
}

// Export to CSV
function exportToCSV() {
    // Implement CSV export logic
}

// Print table
function printTable() {
    window.print();
}

// Status toggle function
function toggleStatus(rentalCodeId, currentStatus) {
    const statusOptions = ['pending', 'approved', 'completed', 'cancelled'];
    const currentIndex = statusOptions.indexOf(currentStatus);
    const nextIndex = (currentIndex + 1) % statusOptions.length;
    const newStatus = statusOptions[nextIndex];
    
    if (confirm(`Change status from "${currentStatus}" to "${newStatus}"?`)) {
        // Show loading state
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        fetch(`/admin/rental-codes/${rentalCodeId}/update-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show updated status
                location.reload();
            } else {
                alert('Error updating status: ' + (data.message || 'Unknown error'));
                button.innerHTML = originalHTML;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating status. Please try again.');
            button.innerHTML = originalHTML;
            button.disabled = false;
        });
    }
}
</script>
@endsection