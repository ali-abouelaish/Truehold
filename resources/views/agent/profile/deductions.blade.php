@extends('layouts.admin')

@section('title', 'Agent Deductions')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h1 class="h2 mb-0 text-primary fw-bold" style="text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <i class="fas fa-minus-circle text-danger me-2" style="filter: brightness(1.2);"></i>Deductions Overview
                    </h1>
                    <p class="text-muted mb-0">Track all deductions from your earnings including VAT, marketing fees, and other charges.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('agent.profile.earnings') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-line me-1"></i>View Earnings
                    </a>
                    <a href="{{ route('agent.profile.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Internal Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <nav class="nav nav-pills nav-fill">
                        <a class="nav-link" href="{{ route('agent.profile.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="{{ route('agent.profile.rental-codes') }}">
                            <i class="fas fa-receipt me-2"></i>My Rental Codes
                        </a>
                        <a class="nav-link" href="{{ route('agent.profile.earnings') }}">
                            <i class="fas fa-chart-line me-2"></i>Earnings
                        </a>
                        <a class="nav-link active" href="{{ route('agent.profile.deductions') }}">
                            <i class="fas fa-minus-circle me-2"></i>Deductions
                        </a>
                        <a class="nav-link" href="{{ route('agent.profile.clients') }}">
                            <i class="fas fa-users me-2"></i>My Clients
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Deductions Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-3" style="border-left: 4px solid #e74a3b !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total VAT Deductions</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">£{{ number_format($deductionsData['total_vat_deductions'], 2) }}</div>
                            <div class="text-xs text-muted mt-1">20% VAT on transfers</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-3x text-danger" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-3" style="border-left: 4px solid #f6c23e !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Marketing Deductions</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">£{{ number_format($deductionsData['total_marketing_deductions'], 2) }}</div>
                            <div class="text-xs text-muted mt-1">Marketing agent fees</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-3x text-warning" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-3" style="border-left: 4px solid #36b9cc !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Agency Cut</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">£{{ number_format($deductionsData['total_agency_cut'], 2) }}</div>
                            <div class="text-xs text-muted mt-1">45% agency commission</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-3x text-info" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-3" style="border-left: 4px solid #5a5c69 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Total Deductions</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">£{{ number_format($deductionsData['total_deductions'], 2) }}</div>
                            <div class="text-xs text-muted mt-1">All deductions combined</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-3x text-dark" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deductions Breakdown -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Deductions Breakdown
                    </h6>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterDeductions('all')">All</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="filterDeductions('vat')">VAT</button>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="filterDeductions('marketing')">Marketing</button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="filterDeductions('agency')">Agency</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="h4 mb-0 font-weight-bold text-danger">£{{ number_format($deductionsData['total_vat_deductions'], 2) }}</div>
                            <div class="text-xs text-muted">VAT Deductions</div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-danger" style="width: {{ $deductionsData['total_deductions'] > 0 ? ($deductionsData['total_vat_deductions'] / $deductionsData['total_deductions']) * 100 : 0 }}%"></div>
                            </div>
                            <div class="text-xs text-muted mt-1">{{ $deductionsData['total_deductions'] > 0 ? number_format(($deductionsData['total_vat_deductions'] / $deductionsData['total_deductions']) * 100, 1) : 0 }}% of total deductions</div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="h4 mb-0 font-weight-bold text-warning">£{{ number_format($deductionsData['total_marketing_deductions'], 2) }}</div>
                            <div class="text-xs text-muted">Marketing Deductions</div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: {{ $deductionsData['total_deductions'] > 0 ? ($deductionsData['total_marketing_deductions'] / $deductionsData['total_deductions']) * 100 : 0 }}%"></div>
                            </div>
                            <div class="text-xs text-muted mt-1">{{ $deductionsData['total_deductions'] > 0 ? number_format(($deductionsData['total_marketing_deductions'] / $deductionsData['total_deductions']) * 100, 1) : 0 }}% of total deductions</div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="h4 mb-0 font-weight-bold text-info">£{{ number_format($deductionsData['total_agency_cut'], 2) }}</div>
                            <div class="text-xs text-muted">Agency Cut</div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-info" style="width: {{ $deductionsData['total_deductions'] > 0 ? ($deductionsData['total_agency_cut'] / $deductionsData['total_deductions']) * 100 : 0 }}%"></div>
                            </div>
                            <div class="text-xs text-muted mt-1">{{ $deductionsData['total_deductions'] > 0 ? number_format(($deductionsData['total_agency_cut'] / $deductionsData['total_deductions']) * 100, 1) : 0 }}% of total deductions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Deductions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Detailed Deductions History</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="filterTable('all')">All Deductions</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterTable('vat')">VAT Only</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterTable('marketing')">Marketing Only</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterTable('agency')">Agency Cut Only</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    @if($deductionsHistory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0" id="deductionsTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Rental Code</th>
                                        <th>Client</th>
                                        <th>Deduction Type</th>
                                        <th>Amount</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deductionsHistory as $deduction)
                                        <tr data-type="{{ $deduction['type'] }}">
                                            <td>{{ $deduction['date']->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('rental-codes.show', $deduction['rental_code_id']) }}" class="text-primary">
                                                    {{ $deduction['rental_code'] }}
                                                </a>
                                            </td>
                                            <td>{{ $deduction['client_name'] }}</td>
                                            <td>
                                                <span class="badge badge-{{ $deduction['type'] === 'vat' ? 'danger' : ($deduction['type'] === 'marketing' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($deduction['type']) }}
                                                </span>
                                            </td>
                                            <td class="font-weight-bold text-{{ $deduction['type'] === 'vat' ? 'danger' : ($deduction['type'] === 'marketing' ? 'warning' : 'info') }}">
                                                £{{ number_format($deduction['amount'], 2) }}
                                            </td>
                                            <td>{{ $deduction['reason'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-minus-circle fa-3x mb-3"></i>
                            <p>No deductions found</p>
                            <p class="text-sm">Deductions will appear here as you create rental codes with various payment methods and marketing arrangements.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterTable(type) {
    const table = document.getElementById('deductionsTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const rowType = row.getAttribute('data-type');
        
        if (type === 'all' || rowType === type) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function filterDeductions(type) {
    // Update button states
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Filter table
    filterTable(type);
}
</script>
@endpush
