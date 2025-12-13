@extends('layouts.admin')

@section('title', 'Rental Codes')

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
    
    .col-xl-3 {
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

/* Date from database */
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
                    @if(auth()->user()->role === 'admin')
                    <button onclick="approveAllPendingRentals()" class="btn transition-colors"
                       style="background: linear-gradient(135deg, #0891b2, #06b6d4); border: 2px solid #06b6d4; color: #ffffff; font-weight: 600; padding: 12px 24px; box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);"
                       onmouseover="this.style.background='linear-gradient(135deg, #06b6d4, #0891b2)'; this.style.borderColor='#0891b2'; this.style.boxShadow='0 6px 16px rgba(8, 145, 178, 0.4)'; this.style.transform='translateY(-2px)';"
                       onmouseout="this.style.background='linear-gradient(135deg, #0891b2, #06b6d4)'; this.style.borderColor='#06b6d4'; this.style.boxShadow='0 4px 12px rgba(8, 145, 178, 0.3)'; this.style.transform='translateY(0)';">
                        <i class="fas fa-check-double me-2"></i> Approve All Rentals
                    </button>
                    @endif
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlyStats['total'] ?? 0 }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlyStats['completed'] ?? 0 }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlyStats['pending'] ?? 0 }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlyStats['approved'] ?? 0 }}</div>
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

                    <!-- Rental Code Created Modal -->
                    @if(session('new_rental_code'))
                    <div class="modal fade" id="rentalCodeCreatedModal" tabindex="-1" aria-labelledby="rentalCodeCreatedModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="background-color: #1f2937; border-color: #374151;">
                                <div class="modal-header" style="border-color: #4b5563;">
                                    <h5 class="modal-title text-white" id="rentalCodeCreatedModalLabel">
                                        <i class="fas fa-check-circle text-success me-2"></i>Rental Code Created
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-white">
                                    <p class="mb-3">Your rental code has been created successfully!</p>
                                    <div class="bg-dark p-3 rounded mb-3" style="background-color: #111827 !important;">
                                        <small class="text-muted d-block mb-1">Rental Code:</small>
                                        <strong>{{ session('new_rental_code.code') }}</strong>
                                    </div>
                                    <p class="mb-0">Click the button below to copy the rental details and open the WhatsApp group.</p>
                                </div>
                                <div class="modal-footer" style="border-color: #4b5563;">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="copyAndOpenWhatsAppBtn">
                                        <i class="fas fa-copy me-2"></i>Copy Details & Open WhatsApp
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Search and Filter Bar (server-side filtering) -->
                    <form method="GET" action="{{ route('rental-codes.index') }}" class="row mb-3 g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="q" value="{{ $search ?? request('q') }}" placeholder="Search code, client, property, licensor...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" name="payment_method">
                                <option value="">All Methods</option>
                                <option value="Cash" {{ ($filterPaymentMethod ?? request('payment_method')) === 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Transfer" {{ ($filterPaymentMethod ?? request('payment_method')) === 'Transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="Card machine" {{ ($filterPaymentMethod ?? request('payment_method')) === 'Card machine' ? 'selected' : '' }}>Card machine</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Agent</label>
                            <select class="form-select" name="agent_id">
                                <option value="">All Agents</option>
                                @foreach(($agents ?? []) as $id => $name)
                                    <option value="{{ $id }}" {{ (string)($filterAgentId ?? request('agent_id')) === (string)$id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-filter"></i> Apply
                            </button>
                            <a href="{{ route('rental-codes.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>

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
                                        <th>Date</th>
                                        <th>Consultation Fee</th>
                                        <th>Status</th>
                                        <th>Agent</th>
                                        <th>Marketing Agent</th>
                                        @if(auth()->user()->role === 'admin')
                                        <th>Quick Actions</th>
                                        @endif
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
                                                <span class="fw-bold text-success">
                                                    @if($emoji)
                                                        <span class="me-1">{{ $emoji }}</span>
                                                    @endif
                                                    £{{ number_format($rentalCode->consultation_fee, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $rentalCode->status === 'paid' ? 'success' : ($rentalCode->status === 'approved' ? 'info' : ($rentalCode->status === 'cancelled' ? 'danger' : 'warning')) }} px-3 py-2">
                                                    <i class="fas fa-{{ $rentalCode->status === 'paid' ? 'check-circle' : ($rentalCode->status === 'approved' ? 'thumbs-up' : ($rentalCode->status === 'cancelled' ? 'times' : 'clock')) }} me-1"></i>
                                                    {{ ucfirst($rentalCode->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fas fa-user-tie text-muted"></i>
                                                    </div>
                                                    <span>{{ $rentalCode->rentalAgent->name ?? 'N/A' }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fas fa-user-friends text-muted"></i>
                                                    </div>
                                                    <span>{{ $rentalCode->marketingAgentUser->name ?? 'N/A' }}</span>
                                                </div>
                                            </td>
                                            @if(auth()->user()->role === 'admin')
                                            <td>
                                                <div class="dropdown">
                                                    <select class="form-select form-select-sm" 
                                                            onchange="changeStatus({{ $rentalCode->id }}, this.value)"
                                                            style="background: linear-gradient(135deg, #374151, #4b5563); border: 1px solid #6b7280; color: #d1d5db; min-width: 120px;">
                                                        <option value="pending" {{ $rentalCode->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="approved" {{ $rentalCode->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                        <option value="paid" {{ $rentalCode->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                                    </select>
                                                </div>
                                            </td>
                                            @endif
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('rental-codes.show', $rentalCode) }}" class="btn btn-sm transition-colors" title="View Details"
                                                       style="background: linear-gradient(135deg, #1e40af, #3b82f6); border: 1px solid #3b82f6; color: #ffffff; text-decoration: none;"
                                                       onmouseover="this.style.background='linear-gradient(135deg, #3b82f6, #2563eb)'; this.style.borderColor='#2563eb';"
                                                       onmouseout="this.style.background='linear-gradient(135deg, #1e40af, #3b82f6)'; this.style.borderColor='#3b82f6';">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(auth()->user()->canEditRentalCode($rentalCode))
                                                    <a href="{{ route('rental-codes.edit', $rentalCode) }}" class="btn btn-sm transition-colors" title="Edit"
                                                       style="background: linear-gradient(135deg, #d97706, #f59e0b); border: 1px solid #f59e0b; color: #ffffff; text-decoration: none;"
                                                       onmouseover="this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.borderColor='#d97706';"
                                                       onmouseout="this.style.background='linear-gradient(135deg, #d97706, #f59e0b)'; this.style.borderColor='#f59e0b';">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @endif
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
    // Get current filter parameters from the form
    const form = document.querySelector('form[method="GET"][action*="rental-codes"]');
    const params = new URLSearchParams();
    
    if (form) {
        const searchInput = form.querySelector('input[name="q"]');
        const paymentMethodSelect = form.querySelector('select[name="payment_method"]');
        const agentSelect = form.querySelector('select[name="agent_id"]');
        
        if (searchInput && searchInput.value) {
            params.append('q', searchInput.value);
        }
        if (paymentMethodSelect && paymentMethodSelect.value) {
            params.append('payment_method', paymentMethodSelect.value);
        }
        if (agentSelect && agentSelect.value) {
            params.append('agent_id', agentSelect.value);
        }
    }
    
    // Build export URL with current filters
    const exportUrl = '{{ route("rental-codes.export") }}' + (params.toString() ? '?' + params.toString() : '');
    
    // Open export URL in new window to trigger download
    window.location.href = exportUrl;
}

// Print table
function printTable() {
    window.print();
}

// Bulk actions functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Individual checkbox functionality
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActions();
            updateSelectAllState();
        });
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count > 0) {
            bulkActions.style.display = 'block';
            selectedCount.textContent = count;
        } else {
            bulkActions.style.display = 'none';
        }
    }

    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const totalBoxes = rowCheckboxes.length;
        
        if (checkedBoxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedBoxes.length === totalBoxes) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }
});

// Bulk action function
function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const rentalCodeIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (rentalCodeIds.length === 0) {
        alert('Please select at least one rental code.');
        return;
    }

    let status;
    let actionText;
    
    switch(action) {
        case 'approve':
            status = 'approved';
            actionText = 'approve';
            break;
        case 'complete':
            status = 'paid';
            actionText = 'mark as paid';
            break;
        case 'cancel':
            status = 'cancelled';
            actionText = 'cancel';
            break;
        default:
            alert('Invalid action.');
            return;
    }

    if (confirm(`Are you sure you want to ${actionText} ${rentalCodeIds.length} rental code(s)?`)) {
        // Show loading state
        const buttons = document.querySelectorAll('#bulkActions button');
        buttons.forEach(btn => {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        });

        // Send bulk update request
        fetch('/admin/rental-codes/bulk-update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                rental_code_ids: rentalCodeIds,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Successfully updated ${data.updated_count} rental code(s).`);
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update rental codes'));
                // Reset buttons
                resetBulkActionButtons();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating rental codes. Please try again.');
            resetBulkActionButtons();
        });
    }
}

function resetBulkActionButtons() {
    const buttons = document.querySelectorAll('#bulkActions button');
    buttons.forEach((btn, index) => {
        btn.disabled = false;
        const icons = ['<i class="fas fa-thumbs-up"></i> Approve', '<i class="fas fa-check"></i> Complete', '<i class="fas fa-times"></i> Cancel'];
        btn.innerHTML = icons[index];
    });
}

// Status change function for dropdown
function changeStatus(rentalCodeId, newStatus) {
    if (confirm(`Change status to "${newStatus}"?`)) {
        // Show loading state
        const select = event.target;
        const originalValue = select.value;
        select.disabled = true;
        select.style.opacity = '0.6';
        
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
                select.value = originalValue;
                select.disabled = false;
                select.style.opacity = '1';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating status. Please try again.');
            select.value = originalValue;
            select.disabled = false;
            select.style.opacity = '1';
        });
    } else {
        // Reset to original value if user cancels
        event.target.value = event.target.getAttribute('data-original-value') || 'pending';
    }
}

// Approve all pending rentals function
function approveAllPendingRentals() {
    if (confirm('Are you sure you want to approve ALL pending rental codes in the system? This will change their status to "approved".')) {
        // Show loading state on the button
        const approveAllButton = event.target.closest('button');
        const originalHTML = approveAllButton.innerHTML;
        approveAllButton.disabled = true;
        approveAllButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Approving...';
        
        // Send request to approve all pending rentals
        fetch('/admin/rental-codes/approve-all-pending', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.updated_count > 0) {
                    alert(`Successfully approved ${data.updated_count} pending rental code(s)!`);
                    location.reload();
                } else {
                    alert('No pending rental codes found to approve.');
                    approveAllButton.disabled = false;
                    approveAllButton.innerHTML = originalHTML;
                }
            } else {
                alert('Error: ' + (data.message || 'Failed to approve rental codes'));
                approveAllButton.disabled = false;
                approveAllButton.innerHTML = originalHTML;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving rental codes. Please try again.');
            approveAllButton.disabled = false;
            approveAllButton.innerHTML = originalHTML;
        });
    }
}

// Handle rental code created modal - Copy details and open WhatsApp
@if(session('new_rental_code'))
document.addEventListener('DOMContentLoaded', function() {
    const rentalData = @json(session('new_rental_code'));
    const modal = document.getElementById('rentalCodeCreatedModal');
    const copyBtn = document.getElementById('copyAndOpenWhatsAppBtn');
    const whatsappLink = 'https://chat.whatsapp.com/IQyyjhLE8X03QnFyflfJLR?mode=hqrt3';
    
    // Get payment method emoji
    function getPaymentEmoji(paymentMethod) {
        if (!paymentMethod) return '';
        const method = paymentMethod;
        if (method.includes('card')) return '⚡';
        if (method.includes('cash')) return '💵';
        if (method.includes('transfer')) return '💳';
        return '';
    }
    
    // Generate rental code details text in WhatsApp format
    function generateRentalDetails() {
        const client = rentalData.client || {};
        const paymentEmoji = getPaymentEmoji(rentalData.payment_method);
        
        let details = `*Client Code*\n\n`;
        details += `*${rentalData.code}*\n\n`;
        details += `*Date:* ${rentalData.date}\n`;
        details += `*Consultation Fee:* £${rentalData.fee}\n`;
        details += `*Payment:* ${rentalData.payment_method || 'N/A'} ${paymentEmoji}\n`;
        details += `_______________________________\n\n`;
        details += `*Client Information*\n\n`;
        details += `*Full Name:* ${client.name || rentalData.client_name || 'N/A'}\n`;
        details += `*Phone Number:* ${client.phone || 'N/A'}\n`;
        
        // Age with age group
        if (client.age !== null && client.age !== undefined) {
            details += `*Age:* ${client.age} (${client.age_group || 'Unknown'})\n`;
        } else {
            details += `*Age:* N/A\n`;
        }
        
        details += `*Nationality:* ${client.nationality || 'N/A'}\n`;
        details += `*Type:* ${client.type || 'Other'}\n`;
        details += `*Position/Role:* ${client.position_role || 'N/A'}\n`;
        details += `________________________________\n\n`;
        details += `*Assisted by:* ${rentalData.rental_agent || 'N/A'}\n`;
        
        if (rentalData.marketing_agent && rentalData.marketing_agent !== 'N/A') {
            details += `*Marketing Agent:* ${rentalData.marketing_agent}\n`;
        }
        
        return details;
    }
    
    // Handle copy and open WhatsApp button click
    if (copyBtn) {
        copyBtn.addEventListener('click', async function() {
            const rentalDetails = generateRentalDetails();
            
            try {
                // Copy to clipboard
                await navigator.clipboard.writeText(rentalDetails);
                
                // Open WhatsApp in new tab
                window.open(whatsappLink, '_blank');
                
                // Show success toast/notification
                showToast('Rental details copied — paste them in the WhatsApp group', 'success');
                
                // Close modal after a short delay
                setTimeout(() => {
                    if (modal) {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        } else {
                            modal.style.display = 'none';
                            const backdrop = document.querySelector('.modal-backdrop');
                            if (backdrop) backdrop.remove();
                        }
                    }
                }, 500);
            } catch (err) {
                console.error('Failed to copy text: ', err);
                // Fallback: try using execCommand for older browsers
                try {
                    const textArea = document.createElement('textarea');
                    textArea.value = rentalDetails;
                    textArea.style.position = 'fixed';
                    textArea.style.left = '-999999px';
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    
                    window.open(whatsappLink, '_blank');
                    showToast('Rental details copied — paste them in the WhatsApp group', 'success');
                    
                    setTimeout(() => {
                        if (modal) {
                            const bsModal = bootstrap.Modal.getInstance(modal);
                            if (bsModal) {
                                bsModal.hide();
                            } else {
                                modal.style.display = 'none';
                                const backdrop = document.querySelector('.modal-backdrop');
                                if (backdrop) backdrop.remove();
                            }
                        }
                    }, 500);
                } catch (fallbackErr) {
                    showToast('Failed to copy. Please copy manually.', 'error');
                }
            }
        });
    }
    
    // Auto-show modal if it exists
    if (modal) {
        // Wait for Bootstrap to be fully loaded
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const bsModal = new bootstrap.Modal(modal, {
                backdrop: true,
                keyboard: true
            });
            bsModal.show();
            
            // Clean up backdrop when modal is hidden
            modal.addEventListener('hidden.bs.modal', function() {
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            });
        } else {
            // Fallback if Bootstrap isn't loaded yet
            setTimeout(() => {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const bsModal = new bootstrap.Modal(modal, {
                        backdrop: true,
                        keyboard: true
                    });
                    bsModal.show();
                    
                    // Clean up backdrop when modal is hidden
                    modal.addEventListener('hidden.bs.modal', function() {
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.remove();
                    });
                } else {
                    // Last resort: show manually
                    modal.style.display = 'block';
                    modal.classList.add('show');
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                }
            }, 100);
        }
    }
});

// Toast notification function
function showToast(message, type = 'success') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.rental-toast');
    existingToasts.forEach(toast => toast.remove());
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `rental-toast alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}
@endif
</script>
@endsection